<?php

namespace Inatto\Turism\Model;

use syspais\vo\VoAeroporto;
use system\data\Connection;
use system\data\MySqlPersistence;

class TurismoDs extends MySqlPersistence
{

    public function readerAeroporto(Connection $connection, VoAeroporto $param)
    {
        //
        $columns = $this->buildColumns($connection, $param);
        $join = $this->buildJoin($connection, $param);
        $where = $this->buildWhere($connection, $param);
        $order = $this->buildOrder($connection, $param);

        //
        if ($param->_getExtra("queryAeroporto")) {
            $filtro = $connection->removeInvalidChars($param->_getExtra("queryAeroporto"));
            $filtro = strtolower($filtro);
            $where .= "
                and (
                    LOWER(ifnull(codigoIata,'')) like '%{$filtro}%' or
                    LOWER(ifnull(nome,'')) like '%{$filtro}%' 
                )
            ";
        }

        //
        $sql = " SELECT $columns FROM aeroporto $join $where $order ";
//        dd($sql);
        if ($param->get_pagination())
            $vos = $connection->createReader($sql, $param->get_pagination()->getOffset(), $param->get_pagination()->getMaximo());
        else
            $vos = $connection->createReader($sql);

        //
        return $vos;
    }

}