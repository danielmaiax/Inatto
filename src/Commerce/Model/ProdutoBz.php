<?php

namespace Inatto\Commerce\Model;

use system\data\Connection;
use system\Date;
use system\mvc\MySqlBusiness;

class ProdutoBz extends MySqlBusiness
{

    //
    protected function getPersistence()
    {
        return new ProdutoDs();
    }

    public function atualizarStatusProduto(Connection $connection = null)
    {
        //
        $this->connect($connection);
        $this->getPersistence()->atualizarStatusProduto($this->getConnection());
        $this->disconnect($connection);
    }

    public function produtosDosVouchers(Connection $connection = null, Date $dataInicio = null, Date $dataFim = null, $tipoData = 'dataValidacao')
    {
        //
        $this->connect($connection);
        $reader = $this->getPersistence()->produtosDosVouchers($this->getConnection(), $dataInicio, $dataFim, $tipoData);
        $this->disconnect($connection);

        //
        return $reader;
    }

    public function validadoresDosVouchers(Connection $connection = null, Date $dataInicio = null, Date $dataFim = null, $tipoData = 'dataValidacao')
    {
        //
        $this->connect($connection);
        $reader = $this->getPersistence()->validadoresDosVouchers($this->getConnection(), $dataInicio, $dataFim, $tipoData);
        $this->disconnect($connection);

        //
        return $reader;
    }




}
