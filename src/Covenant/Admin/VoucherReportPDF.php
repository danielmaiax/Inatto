<?php

namespace Inatto\Covenant\Admin;

use Exception;
use sysadmin\NegocioAdmin;
use sysadmin\PDF;
use sysconvenio\vo\VoVoucher;
use sysservidor\vo\VoEntidadeServidor;
use system\Config;
use system\data\MySqlConnection;
use system\data\Reader;
use system\Date;
use system\mvc\Controller;
use system\mvc\MySqlBusiness;
use system\Text;
use systems\syspessoa\core\Negocio;

class VoucherReportPDF extends Controller
{
    private $line = 0.4;

    public function _initialize()
    {
    }

    public function pdf()
    {
        //
//        d($_POST);
//        $GLOBALS['dbgBanco'] = 1;
        $reader = $this->select($this->getInicio(), $this->getFim(), $this->getSituacao(), $this->idPessoaEmpresa(), $this->getTipoVoucher(), $this->idPessoaEmpresaEntidade(), $this->getTipoData(), postIf('_apenasPresidentes'));
//        exit;

        // presidentes
        $negocio = new Negocio();
        $vos = new VoEntidadeServidor();
        $vos->newVoPessoaPresidente(null, 'emailPrincipal');

        //
        $vos->idConfigFather = $this->getConfig()->idConfig;
        $vos = $negocio->select(null, $vos);
        $arrayPresidentes = [];
        foreach ($vos as $vo) {
            $vo = VoEntidadeServidor::cast($vo);
            if ($vo->voPessoaPresidente->emailPrincipal) $arrayPresidentes[] = $vo->voPessoaPresidente->emailPrincipal;
        }

        //
        $pdf = new PDF("L", "cm", "A4"); //
//		$pdf->titulo = "{$vos->total()} Vouchers";
        $pdf->AddPage();
        $this->tabela($pdf, $reader, $arrayPresidentes);
        $pdf->Output();
    }

    private function select(Date $inicio, Date $fim, $situacao, $idPessoaEmpresa, $tipoVoucher, $idPessoaEmpresaEntidade, $tipoData, $apenasPresidentes = false)
    {
        //
        null($idPessoaEmpresa);
        if ($inicio->toYMD() > $fim->toYMD()) die("Data inicial não pode ser maior que a data final.");

        //
        $array = null;
        if ($apenasPresidentes) $array = $this->presidentes();

        //
        $negocio = new NegocioAdmin(config());
        $vos = new VoVoucher(null, "dataEmissao dataValidacao entidade codigo nrAcesso nome cpf obs tipoVoucher volumeVoucher valorVoucher");
        $vos->newVoMembro(null, "nrCartaoMembro")->newVoPessoa(null, 'emailPrincipal');
        $vos->newVoPessoaQueValidou(null, "nome");
        $vos->idPessoaQueValidou = $this->getidPessoaValidou();
        $vos->idProduto = $this->getidProduto();
        $vos->addOrder('dataEmissao');
        if ($array) $vos->voMembro->voPessoa->addFilter('emailPrincipal', 'IN', $array);

        //
        if ($idPessoaEmpresaEntidade) $vos->voMembro->idPessoaEmpresa = $idPessoaEmpresaEntidade;

        //
        if ($tipoData == 'dataEmissao' && $inicio) $vos->addFilter('insertDate', ">=", $inicio, "filtroInicio");
        if ($tipoData == 'dataEmissao' && $fim) $vos->addFilter('insertDate', "<=", $fim, "filtroFim");
        if ($tipoData == 'dataValidacao' && $inicio) $vos->addFilter('dataValidacao', ">=", $inicio, "filtroInicio");
        if ($tipoData == 'dataValidacao' && $fim) $vos->addFilter('dataValidacao', "<=", $fim, "filtroFim");
        if ($tipoVoucher) $vos->tipoVoucher = $tipoVoucher;

        //
        if ($situacao == 'validados') $vos->addFilter("dataValidacao", '<>', null);
        if ($situacao == 'validados') $vos->canceled = 0; // IMPORTANT -  pode ter sido validado antes de ser cancelado

        // amarra empresa
        // TODO ver sala vip
//        $vos->newVoProduto()->newVoConvenio()->idPessoaEmpresa = $idPessoaEmpresa;
//        $vos->newVoConvenio()->idPessoaEmpresa = $idPessoaEmpresa;
        if ($this->ms('asa')) $vos->voProduto->voConvenio->idPessoaEmpresa = null;
        if ($this->ms('asa')) $vos->voConvenio->idPessoaEmpresa = null;

        //
        $vos->idConfigFather = $this->getConfig()->idConfig;
        if ($this->lcb()) $vos->idConfigFather = null;

        //
        $vos = $negocio->reader(null, $vos);

        //
        return $vos;
    }

    /**
     * Array com emails de presidentes de entidades
     * @throws Exception
     */
    private function presidentes()
    {
        $sql = "
				SELECT p.emailPrincipal FROM entidade_servidor es
				LEFT JOIN pessoa p ON p.id_pessoa = es.id_pessoa_presidente
				WHERE 1 AND IFNULL(emailPrincipal, '') <> ''
				ORDER BY emailPrincipal";

        $negocio = new NegocioAdmin(config());
        $negocio->connect();
        $reader = $negocio->getConnection()->createReader($sql);

        //
        $emails = [];
        while ($reader->next()) {
            $row = $reader->row();
            $email = $row['p.emailPrincipal'];
            $emails[] = $email;
        }

        //
        $negocio->disconnect();
        return $emails;
    }

    private function tabela(PDF $pdf, Reader $reader, $arrayPresidentes)
    {
        //
        $w = [2.3, 2.3, 2, 2.2, 2, 1, 1, 2.7, 5.6, 4, 1.6];

        //
        $pdf->boldOn();
        $pdf->SetFontSize(6);
        $pdf->cellX(1.5, $w[0], $this->line, 'Emissão', "B");
        $pdf->cellX(null, $w[1], $this->line, 'Validação', "B");
        $pdf->cellX(null, $w[2], $this->line, 'Entidade', "B");
        $pdf->cellX(null, $w[3], $this->line, 'Código', "B");
        $pdf->cellX(null, $w[4], $this->line, 'Tipo', "B");
        $pdf->cellX(null, $w[6], $this->line, 'Acesso', "B");
        if ($this->config->miniSigla == "lcb") $pdf->cellX(null, $w[7], $this->line, 'Cartão', "B");
        else $pdf->cellX(null, $w[7], $this->line, 'CPF', "B");
        $pdf->cellX(null, $w[8], $this->line, 'Nome', "B");
        $pdf->cellX(null, $w[9], $this->line, 'Validado Por', "B");
        $pdf->cellX(null, $w[5], $this->line, 'Volume', "B");
        $pdf->cellX(null, $w[10], $this->line, 'Valor', "B", 0, 'R');
        $pdf->defaultColor();
        $pdf->boldOff();

        //
        $qtd = 0;
        $totalValor = 0;
        $totalVolume = 0;

        //
        $pdf->SetFontSize(7);
        while ($reader->next()) {
            //
            $qtd++;
            $row = $reader->row();

            //
            $ehPresidente = '';
            $email = $row['voucher__membro__pessoa.emailPrincipal'];
            if (in_array($email, $arrayPresidentes)) $ehPresidente = true;
            if ($ehPresidente) $ehPresidente = "*PRESIDENTE*  ";

            //
            $dataEmissao = MySqlConnection::fromMysqlDbDate($row['voucher.dataEmissao']);
            $dataValidacao = MySqlConnection::fromMysqlDbDate($row['voucher.dataValidacao']);

            //
            $pdf->SetY($pdf->GetY() + 0.4);
            $pdf->cellX(1.5, $w[0], $this->line, $dataEmissao ? $dataEmissao->toDateTimeString() : '');
            $pdf->cellX(null, $w[1], $this->line, $dataValidacao ? $dataValidacao->toDateTimeString() : '');
            $pdf->cellX(null, $w[2], $this->line, $row['voucher.entidade']);
            $pdf->cellX(null, $w[3], $this->line, $row['voucher.codigo']);
            $pdf->cellX(null, $w[4], $this->line, $row['voucher.tipoVoucher']);
            $pdf->cellX(null, $w[6], $this->line, $row['voucher.nrAcesso'] > 1 ? "*{$row['voucher.nrAcesso']}" : $row['voucher.nrAcesso'], 0, 0, "R");
            if ($this->config->miniSigla == "lcb") $pdf->cellX(null, $w[7], $this->line, $row['voucher__membro.nrCartaoMembro']);
            else $pdf->cellX(null, $w[7], $this->line, $row['voucher.cpf']);
            $pdf->cellX(null, $w[8], $this->line, Text::upper($ehPresidente . $row['voucher.nome']));
            $pdf->cellX(null, $w[9], $this->line, Text::upper($row['voucher__pessoa_queValidou.nome']));

            //
            $totalValor += $row['voucher.valorVoucher'];
            $totalVolume += $row['voucher.volumeVoucher'];

            //
            $pdf->cellX(null, $w[5], $this->line, $row['voucher.volumeVoucher'], 0, 0, 'R');
            $pdf->cellX(null, $w[10], $this->line, $row['voucher.valorVoucher'], 0, 0, 'R');
        }

        //
        $pdf->SetY($pdf->GetY() + 0.8);
        $pdf->Cell(0, 0.4, "", "T"); // linha

        //
        $pdf->cellX(1.5, $w[0], $this->line, "{$qtd} itens");
        $pdf->cellX(null, $w[1], $this->line, '');
        $pdf->cellX(null, $w[2], $this->line, '');
        $pdf->cellX(null, $w[3], $this->line, '');
        $pdf->cellX(null, $w[4], $this->line, '');
        $pdf->cellX(null, $w[6], $this->line, '');
        if ($this->config->miniSigla == "lcb") $pdf->cellX(null, $w[7], $this->line, '');
        else $pdf->cellX(null, $w[7], $this->line, '');
        $pdf->cellX(null, $w[8], $this->line, '');
        $pdf->cellX(null, $w[9], $this->line, '');

        //
        $pdf->cellX(null, $w[5], $this->line, $totalVolume, 0, 0, 'R');
        $pdf->cellX(null, $w[10], $this->line, $totalValor, 0, 0, 'R');


    }

    public function csv(Config $config)
    {
        header('Content-type: text/plain');
        header("Content-Type: application/force-download");
        header("Content-Disposition: attachment; filename={$config->miniSigla}_exportacao_VoucherCampanha.csv");
        header("Content-Type: application/download");
        header("Content-Description: File Transfer");

        $reader = $this->select($this->getInicio(), $this->getFim(), $this->getSituacao(), $this->idPessoaEmpresa(), $this->getTipoVoucher(), $this->idPessoaEmpresaEntidade(), $this->getTipoData());
        $this->gerarCsv($reader);
    }

    public function gerarCsv(Reader $reader)
    {
        //
        $primeiraVez = true;

        //
        while ($reader->next()) {
            $row = $reader->row();
            $linha = "";
            $linhaHeader = "";

            //
            if ($primeiraVez) {
                $linhaHeader .= "Emissão;";
                $linhaHeader .= "Validação;";
                $linhaHeader .= "Entidade;";
                $linhaHeader .= "Código;";
                $linhaHeader .= "Tipo;";
                $linhaHeader .= "Volume;";
                $linhaHeader .= "Acesso;";
                $linhaHeader .= "CPF;";
                $linhaHeader .= "Nome;";
                $linhaHeader .= "Validado Por;";
                $linhaHeader .= "Valor;";
//				$linhaHeader .= "Obs;";
            }

            $linha .= campoCSV(MySqlBusiness::myDate($row['voucher.dataEmissao'])) . ";";
            $linha .= campoCSV(MySqlBusiness::myDate($row['voucher.dataValidacao'])) . ";";
            $linha .= campoCSV($row['voucher.entidade']) . ";";
            $linha .= campoCSV($row['voucher.codigo']) . ";";
            $linha .= campoCSV($row['voucher.tipoVoucher']) . ";";
            $linha .= campoCSV($row['voucher.volumeVoucher']) . ";";
            $linha .= campoCSV($row['voucher.nrAcesso']) . ";";
            $linha .= campoCSV($row['voucher.cpf']) . ";";
            $linha .= campoCSV($row['voucher.nome']) . ";";
            $linha .= campoCSV($row['voucher__pessoa_queValidou.nome']) . ";";
            $linha .= campoCSV($row['voucher.valorVoucher']) . ";";
//			$linha .= campoCSV($vo->obs) . ";";

            if ($primeiraVez) $linhaHeader .= "\n";
            $primeiraVez = false;
            $linha = trim($linha);
            $linha .= "\n";
            print utf8_decode($linhaHeader);
            print utf8_decode($linha);
        }
        exit;
    }

    private function idPessoaEmpresa()
    {
        return ls()->idPessoaEmpresaa();
    }

    private function getTipoVoucher()
    {
        $tipoVoucher = postIf('_tipoVoucher');
        return $tipoVoucher;
    }

    private function getTipoData()
    {
        $tipoVoucher = postIf('_tipoData');
        return $tipoVoucher;
    }

    private function idPessoaEmpresaEntidade()
    {
        $tipoVoucher = postIf('_idPessoaEmpresaEntidade');
        return $tipoVoucher;
    }

    private function getSituacao()
    {
        $situacao = postIf("_situacao");
        return $situacao;
    }

    private function getInicio()
    {
        return Date::parseDateIf(postIf("_dataInicio"));
    }

    private function getFim()
    {
        return Date::parseDateIf(postIf("_dataFim"));
    }

    private function getidPessoaValidou()
    {
        return postIf('idPessoaOut', null, true);
    }

    private function getidProduto()
    {
        return postIf('idProdutoOut', null, true);
    }

}