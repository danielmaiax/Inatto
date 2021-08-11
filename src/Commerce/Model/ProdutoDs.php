<?php

namespace Inatto\Commerce\Model;

use system\data\Connection;
use system\data\MySqlPersistence;

class ProdutoDs extends MySqlPersistence
{

    /**
     * Desativa repositorios vencidos
     * @param Connection $connection
     * @return mixed
     */
    public function atualizarStatusProduto(Connection $connection)
    {
        //
        $dateTimeMy = $connection->toDbDateTime(today());
        $dateMy = $connection->toDbDate(today());

        // desativa
        $sql =
            "
            UPDATE produto SET active = 0
            WHERE 1
            AND IFNULL(active, 0) = 1 AND (
            IFNULL(dataPublicacao, '9999-99-99 99:99:99') > $dateMy OR
            IFNULL(dataValidade, '9999-99-99 99:99:99') < $dateMy );
			";
        $connection->executeSql($sql);

        //
        $sql =
            "
            UPDATE produto SET active = 1
            WHERE 1
            AND IFNULL(active, 0) = 0
            AND IFNULL(dataPublicacao, '9999-99-99 99:99:99') <= $dateTimeMy
            AND IFNULL(dataValidade, '9999-99-99 99:99:99') >= $dateMy;
			";
        $connection->executeSql($sql);

        //
        return null;
    }

}
