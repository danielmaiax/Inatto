<?php

namespace Inatto\Turism\Third\Voetur;

use sysadmin\HeadHelper;
use system\Config;
use system\html\HtmlDiv;
use system\input\InputHidden;
use system\input\InputSelect;
use system\input\InputSubmit;
use system\mvc\Controller;
use system\ValidationException;
use systems\syscore\controllers\Messages;
use systems\syscore\helper\Location;
use systems\syscore\inputs\InputDate;
use systems\syscore\inputs\InputText;

class BuscaHotel extends Controller
{

    /**
     * @robot_auto_create
     */

    public function _initialize()
    {
        // TODO generalizar
        if (!$this->lcb()) dierror(3237827323);

        //
        parent::_initialize();
        $this->addAndInitialize(new Messages($this->config));

        //
        $divResp = $this::divMunicipioComplete($this->config, 'divDestino', 'destino', '#Para onde quer ir?');
        $this->view()->addChild($divResp);

        //
        $this->view()->addChild(new InputDate("#Checkin", 'dataIda', null, $_POST, true));
        $this->view()->addChild(new InputDate("#Checkout", 'dataVolta', null, $_POST, true));

        //
        $this->view()->addChild(new InputSelect("#Quartos", 'nrQuartos'));
        $this->view()->addChild(new InputSelect("#Adultos", 'nrAdultos'));
        $this->view()->addChild(new InputSelect("#Crianças", 'nrCriancas'));

        //
        $this->view()->getSelect('nrAdultos')->setListSource(BuscaAereo::arrayNome('Adulto(s)'));
        $this->view()->getSelect('nrCriancas')->setListSource(BuscaAereo::arrayNome('Crianças(s)'));
        $this->view()->getSelect('nrQuartos')->setListSource(BuscaAereo::arrayNome('Quartos(s)'));

        //
        $this->view()->addChild(new InputSubmit("Pesquisar", 'buttonPesquisar'));

        //
        if (isPostBack()) $this->post();
    }

    private function post()
    {
        //
        try {
            //
            if (!postIf('idCidade_destino')) throw new ValidationException("Informe o destino");
            if (!postIf('dataIda') || !postIf('dataVolta')) throw new ValidationException("Informe as datas de chek-in e check-out");
            if (!postIf('nrAdultos')) throw new ValidationException("Informe a quantidade de adultos");

            //
            $url = "https://agencias.portalvoetur.com.br/OnlineTravelFrameMVC/Hotel/Disponibilidade?";
            $url .= "&cidade=" . post('idCidade_destino');
            $url .= "&checkin=" . post('dataIda');
            $url .= "&checkout=" . post('dataVolta');
            $url .= "&quantidadeQuartos=" . post('nrQuartos');
            $url .= "&AdultoQuartos=" . post('nrAdultos');
            $url .= "&CriancaQuartos=" . post('nrCriancas');
            $url .= "&LojaChave=bGVnaXNjbHViYnJhc2ls";

            //
            Location::go($url);
            //
        } catch (ValidationException $e) {
            $this->messages()->error($e);
        }
    }

    public static function divMunicipioComplete(Config $config, $divName, $sufix, $label = null)
    {
        // TODO
        HeadHelper::includeAutocomplete(config());

        //
        $div = new HtmlDiv($divName, "Inatto_Turism_Third_Voetur_BuscaHotel DivInput");

        // hidden ID
        $inputHidden = new InputHidden(null, "idCidade_{$sufix}", null, $_POST);
        $inputHidden->setClass("Id");
        $div->addInput($inputHidden);

        // hidden url root
        $inputHidden = new InputHidden(null, "root_{$sufix}", null, $_POST);
        $inputHidden->setClass("Root");
        $inputHidden->setValue("/{$config->miniSigla}/");
        $div->addInput($inputHidden);

        //
        $inputText = new InputText($label, "municipio_{$sufix}", null, $_POST);
        $div->addInput($inputText);

        //
        return $div;
    }

}