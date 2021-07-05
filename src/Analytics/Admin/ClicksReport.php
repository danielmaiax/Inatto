<?php

namespace Inatto\Analytics\Admin;

use sysadmin\HeadHelper;
use syssite\Grid;
use syssite\NegocioSite;
use syssite\PersistenciaSite;
use system\data\Connection;
use system\data\MySqlConnection;
use system\Date;
use system\html\HtmlFieldset;
use system\input\InputSelect;
use system\input\InputSubmit;
use system\mvc\Controller;
use system\mvc\FormController;
use system\mvc\PageController;
use system\Text;
use systems\syscore\helper\Masks;
use systems\syscore\inputs\InputDate;
use systems\syssite\padrao\Style;

class ClicksReport extends Controller
{
    public function _initialize()
    {
        //
        parent::_initialize();

        //
        if (isInattoSig() && getDirectIf('pre')) $this->preProcess();

        //
        HeadHelper::includeJQuery($this->config);
        PageController::addJsCss(FormController::class);
        PageController::addJsCss(Style::class);
        PageController::addJsCss(Grid::class);
        PageController::addJsCss(Masks::class);

        //
        $fieldset = new HtmlFieldset('defaultField', null, "Clicks Reports");
        $fieldset->addDivInputMargin(new InputSelect("Database", "anoFiltro", null, $_POST));
        $fieldset->addBr();
        $fieldset->addDivInputMargin(new InputDate("Início (opcional)", "dataInicio", null, $_POST));
        $fieldset->addDivInputMargin(new InputDate("Fim (opcional)", "dataFim", null, $_POST));
        $fieldset->addH4("O período acima, se informado, deve estar dentro do ano do filtro 'database'");
        $fieldset->addBr();
        $fieldset->addDivInput(new InputSubmit("", 'gerar', 'Gerar'));
        $this->view()->addChild($fieldset);

        //
        $fieldset->getSelect('anoFiltro')->setShowBlankItem(false);
        $fieldset->getSelect('anoFiltro')->setListSource(['2020/2021' => '2020+', '2019' => '2019', '2018' => '2018', '2017' => '2017']);

        //
        if (isPostBack()) $this->post();
//        else {
//            $fieldset->findInput('dataInicio')->setValue(today()->subDays(90));
//            $fieldset->findInput('dataFim')->setValue(today());
//        }
    }

    private function post()
    {
        //
        $negocio = new NegocioSite();
        $negocio->setUseStaticDB(true);

        //
        $this->html()->assign("anoFiltro", postIf("anoFiltro"));
        $this->html()->assign("dataInicio", postIf("dataInicio"));
        $this->html()->assign("dataFim", postIf("dataFim"));

        // ACESSOS POR DATA
        $reader1 = $negocio->readerClickPorData(null, Date::parseDateIf(postIf("dataInicio")), Date::parseDateIf(postIf("dataFim")), 'click', postIf('anoFiltro'));
        $this->html()->assign("readerClick", $reader1);

        // ACESSOS POR DATA
        $reader1 = $negocio->readerClickPorData(null, Date::parseDateIf(postIf("dataInicio")), Date::parseDateIf(postIf("dataFim")), 'data', postIf('anoFiltro'));
        $this->html()->assign("readerData", $reader1);

        // ACESSOS POR URL convenios
        $reader2 = $negocio->readerClickPorUrl(null, Date::parseDateIf(postIf("dataInicio")), Date::parseDateIf(postIf("dataFim")), '%convenio/%', postIf('anoFiltro'));
        $this->html()->assign("readerUrl", $reader2);

        // segmentos
        $reader2 = $negocio->readerClickPorUrl(null, Date::parseDateIf(postIf("dataInicio")), Date::parseDateIf(postIf("dataFim")), '%convenios%', postIf('anoFiltro'));
        $this->html()->assign("readerSegmentos", $reader2);
    }

    //
    private function preProcess()
    {
        //
        $negocio = new NegocioSite($this->config);
        $negocio->setUseStaticDB(true);

        //
        $GLOBALS['dbgBanco'] = 1;

        //
        $array = [];
        $negocio->connect();
        $reader = $negocio->readerClickPorUrl(null, null, null, '%convenio/%', getDirectIf('anoFiltro'));
        while ($reader->next()) {
            //
            $row = $reader->row();
            $url = $row['.Url'];
            $url = MySqlConnection::removeInvalidChars($url);

            // TODO
            $counts = 0;
            if (Text::exists('disbrave', $url)) $counts += $this->vouchersCount($negocio->getConnection(), $array, 386, $url);
            if (Text::exists('vip-club', $url)) $counts += $this->vouchersCount($negocio->getConnection(), $array, 1005, $url);
            if (Text::exists('tanque-cheio', $url)) $counts += $this->vouchersCount($negocio->getConnection(), $array, 2289, $url);
            if (Text::exists('exame', $url)) $counts += $this->vouchersCount($negocio->getConnection(), $array, 2398, $url);
            if (Text::exists('exame', $url)) $counts += $this->clicksCount($negocio->getConnection(), 'vacina', getDirectIf('anoFiltro'));
            if (!$counts) continue;

            // atualiza click no banco com titulo e clicks
//            $leftUrl = Text::left($url, 50);
//            $sql = "update click set click.clicks = {$counts} where url like '{$leftUrl}%' limit 1";
//            $negocio->getConnection()->executeSql($sql);

            // busca convenio para pegar titulo e clicks
//            $sqlConvenio = "select titulo from lcorgbr_lcb2017.convenio where convenio.tagTitulo like '%{$tagConvenio}%' or convenio.id_convenio = '{$tagIdConvenio}' limit 1";
//            $tituloConvenio  = $negocio->getConnection()->returnFieldValue($sqlConvenio) ?? $tagConvenio;
//            $tituloConvenio = MySqlConnection::removeInvalidChars($tituloConvenio);
        }

        //
        $negocio->disconnect();
        dd($array);
        return $array;
    }

    private function vouchersCount(Connection $connection, &$array, $idConvenio, $url)
    {
        //
        if (isset($array[$url])) return $array[$idConvenio];

        //
        $negocio = new NegocioSite();
        $negocio->connect($connection);
        $sqlVoucher = "select count(*) from lcorgbr_lcb2017.voucher where voucher.id_convenio = {$idConvenio}";
        $count = $negocio->getConnection()->returnFieldValue($sqlVoucher) ?? 0;
        $array[$url] = $count;
        $negocio->disconnect($connection);

        //
        $negocio = new NegocioSite();
        $negocio->setUseStaticDB(true);
        $negocio->connect();

        // limpa e atualiza
        $db = PersistenciaSite::myDbFilter(getDirectIf('anoFiltro'));
        $sql = "update {$db} set clicks = null where url = '{$url}';
                update {$db} set clicks = {$count} where url = '{$url}' limit 1;";
        $negocio->getConnection()->executeSql($sql);

        //
        $negocio->disconnect($connection);
        return $count;
    }

    private function clicksCount(Connection $connection, $tag, $anoFiltro = null)
    {
        //
        $negocio = new NegocioSite();
        $negocio->setUseStaticDB(true);
        $negocio->connect($connection);

        //
        $db = PersistenciaSite::myDbFilter($anoFiltro);
        $sqlVoucher = "select count(*) from {$db} where url like '%{$tag}%'";
        $count = $negocio->getConnection()->returnFieldValue($sqlVoucher) ?? 0;

        //
        $negocio->disconnect($connection);
        return $count;
    }

}