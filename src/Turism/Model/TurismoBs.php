<?php

namespace Inatto\Turism\Model;

use syspais\vo\VoAeroporto;
use syspessoa\vo\VoCidade;
use system\data\Connection;
use system\mvc\MySqlBusiness;

class TurismoBs extends MySqlBusiness
{
    protected function getPersistence()
    {
        return new TurismoDs();
    }

    public function readerAeroporto(Connection $connection = null, VoAeroporto $param)
    {
        $this->connect($connection);
        $reader = $this->getPersistence()->readerAeroporto($this->connection, $param);
        $this->disconnect($connection);
        return $reader;
    }

    public function readerCidade(Connection $connection = null, VoCidade $param)
    {
        $this->connect($connection);
        $reader = $this->getPersistence()->readerCidade($this->connection, $param);
        $this->disconnect($connection);
        return $reader;
    }


}


