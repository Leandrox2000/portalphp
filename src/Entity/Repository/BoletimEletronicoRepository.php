<?php

namespace Entity\Repository;

use \Doctrine\ORM\Query\ResultSetMapping;

/**
 * Classe BoletimEletronicoRepository
 *
 * Responsável por todas as consultas a tabela EmailBoletim
 * @author Eduardo
 */
class BoletimEletronicoRepository extends BaseRepository
{

    /**
     *
     * @return \Doctrine\ORM\Query
     */
    public function getConteudoInterna($filter = array()) {
        $query = $this->createQueryBuilder('a')
            ->distinct()
            ->andWhere('a.publicado = 1')
            ->andWhere('a.dataInicial < :today')
            ->andWhere('a.dataFinal > :today OR a.dataFinal IS NULL')
            ->setParameter('today', new \DateTime('now'))
            ->addOrderBy('a.ano', 'DESC')
            ->addOrderBy('a.numero', 'DESC');


        //filtro - data inicial - pesquisa do usuário
        if (array_key_exists('dataInicial', $filter) && preg_match('/[0-9]{2}\/[0-9]{2}\/[0-9]{4}/', $filter['dataInicial'])) {
            $date = implode('-', array_reverse(explode('/', $filter['dataInicial'])));
            $query->andWhere("a.dataInicial BETWEEN '$date 00:00:00' AND '$date 23:59:59' ");
        }

         //filtro - número - pesquisa do usuário
         //if (array_key_exists('numero', $filter) && preg_match('/[0-9]{2}\/[0-9]{4}/', $filter['numero'])) {//old
         if (array_key_exists('numero', $filter) && preg_match('/[0-9]{1,4}\/[0-9]{4}/', $filter['numero'])) {//new

             list($numero, $ano) = explode('/', $filter['numero']);

             $query->andWhere('a.numero IN('.$numero.') AND a.ano IN('.$ano.')');
         }

        //die($query);

        return $query->getQuery()
                        ->useQueryCache(TRUE)
                        ->useResultCache(TRUE, CACHE_LIFE_TIME);
    }

    /**
     * Metodo getArquivosBoletins
     *
     * Retorna um array de arquivos dos boletins que estão entre os ids passados por parâmetro
     * @param array $ids
     * @return array
     */
    public function getArquivosBoletins(array $ids)
    {
        $dql = "SELECT be.arquivo FROM Entity\BoletimEletronico be WHERE be.id IN(" . implode(',', $ids) . ")";
        $query = $this->getEntityManager()->createQuery($dql);
        return $query->getResult();
    }

    /**
     * Metodo getBoletimEletronico
     *
     * Busca boletins com um limit e offset passados por parametro
     * @param int $limit
     * @param int $offset
     * @return array
     */
    public function getBoletimEletronico($limit, $offset)
    {
        $dql = "SELECT be FROM Entity\BoletimEletronico be ORDER BY be.dataCadastro DESC";

        $query = $this->getEntityManager()->createQuery($dql);

        if ($offset > 0) {
            $query->setFirstResult($offset);
        }
        if ($limit > 0) {
            $query->setMaxResults($limit);
        }

        return $query->getResult();
    }

    /**
     * Metodo getBuscaBoletimEletronico
     *
     * Busca boletins de acordo com os filtros passados por parâmetros
     * @param int $limit
     * @param int $offset
     * @param array $filtros
     * @return array
     */
    public function getBuscaBoletimEletronico($limit, $offset, $filtros)
    {
        //Monta a subquery nativa
        $sql = "SELECT be.id_boletim_eletronico, be.dt_cadastro, "
                . "be.dt_periodo_inicial, be.dt_periodo_final, be.nu_numero, "
                . "be.nu_ano, be.no_arquivo, be.st_publicado "
                . "FROM tb_boletim_eletronico be ";

        //Monta o restante do sql aplicando os filtros selecionados
        $flgWhere = true;
        if (!empty($filtros['busca'])) {
            $sql .= $flgWhere  ? " WHERE" : " AND";
            $sql .= " CONCAT(CAST(be.nu_numero AS VARCHAR), '/', CAST(be.nu_ano AS VARCHAR) ) LIKE  '%" . $filtros['busca'] . "%'  ";
            $flgWhere = false;
        }

        if ($filtros['status'] !== "") {
            $sql .= $flgWhere  ? " WHERE" : " AND";
            $sql .= " be.st_publicado = " . $filtros['status'] . " ";
            $flgWhere = false;
        }

        if (!empty($filtros['ano'])) {
            $sql .= $flgWhere ? " WHERE" : " AND";
            $sql .= "  EXTRACT(year from be.dt_cadastro) = " . $filtros['ano'] . " ";
            $flgWhere = false;
        }

        if (!empty($filtros['data_inicial']) && !empty($filtros['data_final'])) {
            $sql .= $flgWhere ? " WHERE" : " AND";
            $sql .= "  ( be.dt_cadastro BETWEEN '" . $filtros['data_inicial'] . " 00:00:00' AND '" . $filtros['data_final'] . " 23:59:59' )";
            $flgWhere = false;
        }

        //Monta o sql final
        $sql .= " ORDER BY be.dt_cadastro";

        if ($limit > 0) {
            $sql .= " DESC LIMIT " . $limit;
        }

        if ($offset > 0) {
            $sql .= " OFFSET " . $offset . " ";
        }

        //Monta o resultSetMapping
        $rsm = new ResultSetMapping;
        $rsm->addEntityResult('Entity\BoletimEletronico', 'be');
        $rsm->addFieldResult('be', 'id_boletim_eletronico', 'id');
        $rsm->addFieldResult('be', 'dt_cadastro', 'dataCadastro');
        $rsm->addFieldResult('be', 'dt_periodo_inicial', 'periodoInicial');
        $rsm->addFieldResult('be', 'dt_periodo_final', 'periodoFinal');
        $rsm->addFieldResult('be', 'nu_numero', 'numero');
        $rsm->addFieldResult('be', 'nu_ano', 'ano');
        $rsm->addFieldResult('be', 'no_arquivo', 'arquivo');
        $rsm->addFieldResult('be', 'st_publicado', 'publicado');

        //Executa a query nativa
        $query = $this->_em->createNativeQuery($sql, $rsm);
        $resultados = $query->getResult();

        //Retorna o resultado
        return $resultados;
    }

    /**
     * Metodo getTotalBuscaBoletimEletronico
     *
     * Busca boletins de acordo com os filtros passados por parâmetro
     * @param int $limit
     * @param int $offset
     * @param array $filtros
     * @return array
     */
    public function getTotalBuscaBoletimEletronico($filtros)
    {
        //Monta a subquery nativa
        $sql = "SELECT COUNT(*) total FROM tb_boletim_eletronico be ";

        //Monta o restante do sql aplicando os filtros selecionados
        $flgWhere = true;
        if (!empty($filtros['busca'])) {
            $sql .= $flgWhere ? " WHERE" : " AND";
            $sql .= " CONCAT(CAST(be.nu_numero AS VARCHAR), '/', CAST(be.nu_ano AS VARCHAR) ) LIKE  '%" . $filtros['busca'] . "%'  ";
            $flgWhere = false;
        }

        if ($filtros['status'] !== "") {
            $sql .= $flgWhere  ? " WHERE" : " AND";
            $sql .= " be.st_publicado = " . $filtros['status'] . " ";
            $flgWhere = false;
        }

        if (!empty($filtros['ano'])) {
            $sql .= $flgWhere ? " WHERE" : " AND";
            $sql .= "  EXTRACT(year from be.dt_cadastro) = " . $filtros['ano'] . " ";
            $flgWhere = false;
        }

        if (!empty($filtros['data_inicial']) && !empty($filtros['data_final'])) {
            $sql .= $flgWhere ? " WHERE" : " AND";
            $sql .= "  ( be.dt_cadastro BETWEEN '" . $filtros['data_inicial'] . " 00:00:00' AND '" . $filtros['data_final'] . " 23:59:59' )";
            $flgWhere = false;
        }

        //Monta o resultSetMapping
        $rsm = new ResultSetMapping;
        $rsm->addEntityResult('Entity\BoletimEletronico', 'be');
        $rsm->addScalarResult('total', 'total');

        //Executa a query nativa
        $query = $this->_em->createNativeQuery($sql, $rsm);
        $resultados = $query->getArrayResult();

        //Retorna o total
        return $resultados[0]['total'];
    }

    /**
     * Metodo countAll
     *
     * Conta todos os boletins
     * @return int
     */
    public function countAll()
    {
        $dql = "SELECT count(be) FROM Entity\BoletimEletronico be";
        $query = $this->getEntityManager()->createQuery($dql);

        return $query->getSingleScalarResult();
    }

    /**
     * Metodo getAnos
     *
     * Busca todos os anos
     */
    public function getAnos()
    {
        //Monta o resultSetMapping
        $rsm = new ResultSetMapping;
        $rsm->addEntityResult('Entity\BoletimEletronico', 'be');
        $rsm->addScalarResult('ano', 'ano');

        $sql = "SELECT EXTRACT(year from dt_cadastro) ano  FROM tb_boletim_eletronico be GROUP BY ano";
        $query = $this->_em->createNativeQuery($sql, $rsm);

        return $query->getArrayResult();
    }



}
