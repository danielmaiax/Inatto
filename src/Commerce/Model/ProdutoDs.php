<?php

namespace Inatto\Commerce\Model;

use system\data\Connection;
use system\data\MySqlPersistence;
use system\data\Reader;
use system\Date;

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

    /**
     * Seleciona produtos filtrados da tabela de vouchers
     *
     * @param Connection $conn
     * @param Date $ini
     * @param Date $fim
     * @param string $tipoData
     * @return Reader
     */
    public function produtosDosVouchers(Connection $conn, Date $ini = null, Date $fim = null, $tipoData = 'dataValidacao')
    {
        //
        $campo = 'dataValidacao';
        if ($tipoData == 'dataEmissao') $campo = 'dataEmissao';

        //
        $filtro = '';
        if ($ini) $filtro .= " AND {$campo} >= {$conn->toDbDate($ini)} \n";
        if ($fim) $filtro .= " AND {$campo} <= {$conn->toDbDate($fim)} \n";

        //
        $sql =
            "   select distinct id_produto, nome from produto
                where 1
                and id_produto in 
                (
                    select id_produto from voucher
                    where 1 {$filtro}
                )
                order by produto.nome;
            ";

        //
        $reader = $conn->createReader($sql);

        //
        return $reader;
    }

    /**
     * Seleciona pessoas que validaram filtrados da tabela de vouchers
     *
     * @param Connection $conn
     * @param Date $ini
     * @param Date $fim
     * @param string $tipoData
     * @return Reader
     */
    public function validadoresDosVouchers(Connection $conn, Date $ini = null, Date $fim = null, $tipoData = 'dataValidacao')
    {
        //
        $campo = 'dataValidacao';
        if ($tipoData == 'dataEmissao') $campo = 'dataEmissao';

        //
        $filtro = '';
        if ($ini) $filtro .= " AND {$campo} >= {$conn->toDbDate($ini)} \n";
        if ($fim) $filtro .= " AND {$campo} <= {$conn->toDbDate($fim)} \n";

        //
        $sql =
            "   select distinct id_pessoa, nome from pessoa
                where 1
                and id_pessoa in 
                (
                    select id_pessoa_queValidou from voucher
                    where 1 {$filtro}
                )
                order by pessoa.nome;
            ";

        //
        $reader = $conn->createReader($sql);

        //
        return $reader;
    }



}
