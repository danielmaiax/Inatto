<?php

namespace Inatto\Commerce\Model;

use system\data\Connection;
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


}
