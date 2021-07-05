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

class BuscaAereo extends Controller
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
        $divResp = $this::divAeroComplete($this->config, 'divAeroOrigem', 'origem', '#Informe a origem');
        $this->view()->addChild($divResp);
        //
        $divResp = $this::divAeroComplete($this->config, 'divAeroDestino', 'destino', '#Informe o destino');
        $this->view()->addChild($divResp);

        //
        $this->view()->addChild(new InputDate("#Ida", 'dataIda', null, $_POST, true));
        $this->view()->addChild(new InputDate("#Volta", 'dataVolta', null, $_POST, true));

        //
        $this->view()->addChild(new InputSelect("#Adultos", 'nrAdultos'));
        $this->view()->addChild(new InputSelect("#Crianças", 'nrCriancas'));
        $this->view()->addChild(new InputSelect("#Bebês", 'nrBebes'));

        //
        $this->view()->getSelect('nrAdultos')->setListSource(self::arrayNome('Adulto(s)'));
        $this->view()->getSelect('nrCriancas')->setListSource(self::arrayNome('Crianças(s)'));
        $this->view()->getSelect('nrBebes')->setListSource(self::arrayNome('Bebês(s)'));

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
            if (!postIf('idAeroporto_origem') || !postIf('idAeroporto_destino')) throw new ValidationException("Informe a origem e destino");
            if (!postIf('dataIda') || !postIf('dataVolta')) throw new ValidationException("Informe as datas de ida e volta");
            if (!postIf('nrAdultos')) throw new ValidationException("Informe a quantidade de adultos");

            //
            $url = "https://agencias.portalvoetur.com.br/OnlineTravelFrameMVC/Aereo/Disponibilidade?";
            $url .= "&Adultos=" . post('nrAdultos');
            $url .= "&Criancas=" . post('nrCriancas');
            $url .= "&Bebes=" . post('nrBebes');
            $url .= "&Origem=" . post('idAeroporto_origem');
            $url .= "&Destino=" . post('idAeroporto_destino');
            $url .= "&Tipo=" . post('metodoViagem');
            $url .= "&DataVolta=" . post('dataVolta');
            $url .= "&DataIda=" . post('dataIda');
            $url .= "&LojaChave=bGVnaXNjbHViYnJhc2ls";

            //
            Location::go($url);
            //
        } catch (ValidationException $e) {
            $this->messages()->error($e);
        }
    }

    public static function arrayNome($label, $max = 4)
    {
        //
        $array = [];
        for ($x = 1; $x <= $max; $x++)
            $array[$x] = "$x $label";

        //
        return $array;
    }

    public static function divAeroComplete(Config $config, $divName, $sufix, $label = null)
    {
        // TODO
        HeadHelper::includeAutocomplete(config());

        //
        $div = new HtmlDiv($divName, "Inatto_Turism_Third_Voetur_BuscaAereo DivInput");

        // hidden ID
        $inputHidden = new InputHidden(null, "idAeroporto_{$sufix}", null, $_POST);
        $inputHidden->setClass("Id");
        $div->addInput($inputHidden);

        // hidden url root
        $inputHidden = new InputHidden(null, "root_{$sufix}", null, $_POST);
        $inputHidden->setClass("Root");
        $inputHidden->setValue("/{$config->miniSigla}/");
        $div->addInput($inputHidden);

        //
        $inputText = new InputText($label, "aeroname_{$sufix}", null, $_POST);
        $div->addInput($inputText);

        //
        return $div;
    }

}