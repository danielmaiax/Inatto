<?php

namespace Inatto\Covenant\Admin;

use Exception;
use Inatto\Commerce\Model\ProdutoBz;
use sysconvenio\HelperConvenio;
use system\Date;
use system\input\InputCheck;
use system\input\InputSelect;
use system\mvc\FormController;
use systems\syscore\inputs\InputDate;

class VoucherReport extends FormController
{

    /**
     * @robot_auto_create
     */

    public function _initialize()
    {
        //
        $this->createView();
        $this->setTitle("Relatório de vouchers");

        //
        $this->inputsStart();

        //
        if (isPostBack('continuar'))
            $this->postContinuar();
        elseif (isPostBack('confirmar'))
            $this->postConfirmar();
        else
            $this->noPost();
    }

    private function noPost()
    {
        //
        $this->setVal("dataInicio", today());
        $this->setVal("dataFim", today());
        $this->createActionSubmit("Continuar");
    }

    private function inputsStart()
    {
        //
        $this->form()->addDivInputMargin(new InputCheck("Apenas Presidentes", "apenasPresidentes", null, $_POST), "S2", "Filtra apenas perfis de presidentes de entidades.");
        $this->form()->addBr();

        //
        $this->form()->addDivInputMargin(new InputSelect("Tipo da data", "tipoData", null, $_POST, true), 'S1');
        $this->form()->addDivInputMargin(new InputDate("Data início", "dataInicio", null, $_POST, true));
        $this->form()->addDivInputMargin(new InputDate("Data fim", "dataFim", null, $_POST));
        $this->form()->addBr();
        $this->form()->addDivInputMargin(new InputSelect("Formato", "formato", null, $_POST, true), "S1");
        $this->form()->addDivInputMargin(new InputSelect("Situação", "situacao", null, $_POST), "S1");
        $this->form()->addDivInputMargin(new InputSelect("Entidade:", "idPessoaEmpresaEntidade", null, $_POST), "S1");

        //
        $this->form()->getSelect('idPessoaEmpresaEntidade')->setListSource(HelperConvenio::entidades(), 'voPessoaEmpresa.idPessoa', 'voPessoaEmpresa.sigla');
        $this->form()->getSelect('formato')->setListSource(['pdf' => 'PDF', 'csv' => 'CSV/Excel']);
        $this->form()->getSelect('situacao')->setListSource(['validados' => 'Validados', 'todos' => 'Todos']);
        $this->form()->getSelect("tipoData")->setListSource(['dataValidacao' => "Validação", 'dataEmissao' => "Emissão"]);
    }

    private function inputsConfirmar()
    {
        //
        $this->form()->addBr();
        $this->form()->addDivInputMargin(new InputSelect("Produto:", "idProdutoOut", null, $_POST), "S3");

        //
        $this->form()->addBr();
        $this->form()->addDivInputMargin(new InputSelect("Validado por:", "idPessoaOut", null, $_POST), "S3");

        //
        $ds = new ProdutoBz();

        //
        $reader = $ds->produtosDosVouchers(null, Date::parseDateIf(postIf('dataInicio')), Date::parseDateIf(postIf('dataFim')), postIf('tipoData'));
        $this->form()->getSelect('idProdutoOut')->setListSource($reader, 'produto.id_produto', 'produto.nome');

        //
        $reader = $ds->validadoresDosVouchers(null, Date::parseDateIf(postIf('dataInicio')), Date::parseDateIf(postIf('dataFim')), postIf('tipoData'));
        $this->form()->getSelect('idPessoaOut')->setListSource($reader, 'pessoa.id_pessoa', 'pessoa.nome');
    }

    private function postContinuar()
    {
        try {
            //
            $this->_validate();

            //
            $this->form()->disable();
            $this->form()->addHiddens();

            //
            $this->inputsConfirmar();

            //
            $this->form()->setTarget(_BLANK);
            $this->createActionRestart();
            $this->createActionSubmit("Confirmar");
        } catch (Exception $e) {
            $this->noPost();
        }
    }

    private function postConfirmar()
    {
        //
        if (post('_formato') == 'pdf') $this->pdf();
        if (post('_formato') == 'csv') $this->csv();
    }

    private function pdf()
    {
        //
        $relatorio = new VoucherReportPDF();
        $relatorio->pdf();
        exit;
    }

    private function csv()
    {
        $relatorio = new VoucherReportPDF();
        $relatorio->csv($this->config);
        exit;
    }

    /** @noinspection PhpUnusedPrivateMethodInspection */
    private function tipoVoucher()
    {
        //        $this->form()->addBr();
        //        $this->form()->addDivInputMargin(new InputSelect("Tipo Voucher:", "tipoVoucher", null, $_POST), "S3");

        //
        //        $negocio = new NegocioConvenio();
        //        $voucher = new VoVoucher(null, 'tipoVoucher');
        //        $voucher->addOrder('tipoVoucher');
        //        $voucher->setDistinct();

        // TODO ver sala vip
        //        $voucher->newVoConvenio()->newVoPessoaEmpresa()->cnpj = ls()->getVoPessoaEmpresa()->cnpj;
        //        if ($this->ms('asa')) $voucher->voConvenio->voPessoaEmpresa->cnpj = null;
        //        if ($this->ls()->ehAdmin()) $voucher->voConvenio->voPessoaEmpresa->cnpj = null;

        //
        //        $voucher = $negocio->select(null, $voucher);
        //        $this->form()->getSelect('tipoVoucher')->setListSource($voucher, 'tipoVoucher', 'tipoVoucher');

    }

}
