<?php

namespace Entity\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * Description of LicitacaoConvenioContratoRepository
 *
 * @author Eduardo
 */
class LicitacaoConvenioContratoRepository extends BaseRepository
{

     /**
     *
     * @return int
     */
    public function countAll()
    {
        //monta o dql
        $dql = "SELECT count(lcc) FROM Entity\LicitacaoConvenioContrato lcc ";

        //Executa a query e retorna o resultado
        $query = $this->getEntityManager()->createQuery($dql);
        return $query->getSingleScalarResult();
    }

    /**
     *
     * @param int $limit
     * @param int $offset
     * @param array $filtros
     * @return array
     */
    public function getBuscaLcc($limit, $offset, $filtro)
    {
        //Estrutura o dql da busca
        $dql = "SELECT lcc FROM Entity\LicitacaoConvenioContrato lcc JOIN lcc.categoria cat JOIN lcc.tipo tip WHERE 1 = 1 ";
        $parametros = array();

        if (!empty($filtro['busca'])) {
            $dql .= " AND LOWER(lcc.objeto) LIKE :nome ";
            $parametros['nome'] = "%" . $filtro['busca'] . "%";
        }

        if (!empty($filtro['categoria'])) {
            $dql .= " AND cat.id = :id_categoria ";
            $parametros['id_categoria'] = $filtro['categoria'];
        }

        if (!empty($filtro['tipo'])) {
            $dql .= " AND tip.id = :id_tipo ";
            $parametros['id_tipo'] = $filtro['tipo'];
        }

        if ($filtro['status'] !== "") {
            $dql .= " AND lcc.publicado = :publicado ";
            $parametros['publicado'] = $filtro['status'];
        }

        if (!empty($filtro['data_inicial']) && !empty($filtro['data_final'])) {
            $dql .= " AND (lcc.dataCadastro BETWEEN :data_inicial AND :data_final  )";
            $parametros['data_inicial'] = $filtro['data_inicial'] . " 00:00:00";
            $parametros['data_final'] = $filtro['data_final'] . " 23:59:59";
        } else if (!empty($filtro['data_final'])) {
            $dql .= " AND lcc.dataCadastro < :data_final ";
            $parametros['data_final'] = $filtro['data_final'] . " 23:59:59";
        } else if (!empty($filtro['data_inicial'])) {
            $dql .= " AND (lcc.dataCadastro > :data_inicial ) ";
            $parametros['data_inicial'] = $filtro['data_inicial'] . " 00:00:00";
        }

        //Ordena os dados
        $dql .= " ORDER BY lcc.dataCadastro DESC ";

        //Executa aquery e passa os parâmetros
        $query = $this->getEntityManager()->createQuery($dql);
        $query->setParameters($parametros);

        if (($limit + $offset) > 0) {
            $query->setFirstResult($offset)
                    ->setMaxResults($limit);
        }

        return $query->getResult();
    }

    /**
     *
     * @param array $filtros
     * @return array
     */
    public function getTotalBuscaLcc($filtro)
    {
        //Estrutura o dql da busca
        $dql = "SELECT count(lcc) total FROM Entity\LicitacaoConvenioContrato lcc  JOIN lcc.categoria cat JOIN lcc.tipo tip WHERE 1 = 1 ";
        $parametros = array();

        if (!empty($filtro['busca'])) {
            $dql .= " AND LOWER(lcc.objeto) LIKE :nome ";
            $parametros['nome'] = "%" . $filtro['busca'] . "%";
        }

        if (!empty($filtro['categoria'])) {
            $dql .= " AND cat.id = :id_categoria ";
            $parametros['id_categoria'] = $filtro['categoria'];
        }

        if (!empty($filtro['tipo'])) {
            $dql .= " AND tip.id = :id_tipo ";
            $parametros['id_tipo'] = $filtro['tipo'];
        }


        if ($filtro['status'] !== "") {
            $dql .= " AND lcc.publicado = :publicado ";
            $parametros['publicado'] = $filtro['status'];
        }

        if (!empty($filtro['data_inicial']) && !empty($filtro['data_final'])) {
            $dql .= " AND (lcc.dataCadastro BETWEEN :data_inicial AND :data_final  )";
            $parametros['data_inicial'] = $filtro['data_inicial'] . " 00:00:00";
            $parametros['data_final'] = $filtro['data_final'] . " 23:59:59";
        }

        //Executa aquery e passa os parâmetros
        $query = $this->getEntityManager()->createQuery($dql);
        $query->setParameters($parametros);

        $resultado = $query->getResult();
        return $resultado[0]['total'];
    }

    /**
     *
     * @param integer $cargo
     * @return type
     */
    public function verificaVinculoCargo($cargo)
    {
        //monta o dql
        $dql = "SELECT count(func) total FROM Entity\LicitacaoConvenioContrato func JOIN func.cargo carg WHERE carg.id = :id_cargo  ";
        $parametros['id_cargo'] = $cargo;

        //Executa a query e retorna o resultado
        $query = $this->getEntityManager()->createQuery($dql);
        $query->setParameters($parametros);
        $result = $query->getResult();

        return $result[0]['total'] == 0 ? true : false;
    }

    /**
     *
     * @param String $id
     * @param String $join
     * @return boolean
     */
    public function verificaVinculo($id, $join)
    {
        //monta o dql
        $dql = "SELECT count(lcc) total FROM Entity\LicitacaoConvenioContrato lcc JOIN lcc.{$join} vinc WHERE vinc.id = :id  ";
        $parametros['id'] = $id;

        //Executa a query e retorna o resultado
        $query = $this->getEntityManager()->createQuery($dql);
        $query->setParameters($parametros);
        $result = $query->getResult();

        return $result[0]['total'] == 0 ? true : false;
    }

    /**
     *
     * @return array
     */
    public function getArquivosLcc($ids)
    {
        //Busca o nome de todos os arquivos relacionados a uma licitação, convênio ou contrato
        $dql = "SELECT arq.nome FROM Entity\LicitacaoConvenioContrato lcc JOIN lcc.arquivos arq WHERE lcc.id IN(" . implode(',', $ids) . ")";
        $query = $this->getEntityManager()->createQuery($dql);
        $result = $query->getResult();

        //Armazena os arquivos em um array
        $arquivos = array();
        foreach ($result as $arq) {
            $arq[] = $arq['nome'];
        }

        return $arquivos;
    }

    /**
     * Retorna um pagination com drive DBAL, ou seja, os resultados não são ORM e
     * sim um array.
     * Método utilizado em função de um erro no paginador com inner join do doctrine na versão 2.4
     * 
     * @link https://groups.google.com/forum/#!msg/doctrine-user/AZyudulmXWE/z9oqG3e4DWoJ
     * @link http://www.doctrine-project.org/2015/01/25/orm-2-5-0-alpha-2.html
     * @deprecated since Doctrine 2.5.0-alpha2
     * @todo Quando atualizar o php >=5.4 e o doctrine para >=2-5-0-alpha-2, o método
     * getConteudoInterna() pode voltar a ser utilizado
     * @return \Pagerfanta\Pagerfanta
     */
    public function getPagination($filter = array()){
        
                
        $dbal = $this->getEntityManager()->getConnection();

        $qbuider = new \Doctrine\DBAL\Query\QueryBuilder($dbal);
        
        $query = $qbuider->select('l.id_licitacao_convenio_contrato')
                ->from('tb_licitacao_convenio_contrato', 'l')
                ->andWhere('l.st_publicado = 1')
                ->orderBy('l.id_status_lcc, l.dt_cadastro');
        

        if(array_key_exists('tipo', $filter) && !empty($filter['tipo'])) {
            
            $query->andWhere('l.id_tipo_lcc = :tipo')
                    ->setParameter('tipo', $filter['tipo']);
        }
        
        if(array_key_exists('categoria', $filter) && !empty($filter['categoria'])) {
            
            $query->andWhere('l.id_categoria_lcc = :categoria')
                  ->setParameter('categoria', $filter['categoria']);
        }
        
        if(array_key_exists('status', $filter) && !empty($filter['status'])) {
            
            /**
             * SELECT * FROM tb_licitacao_convenio_contrato AS a
             * INNER JOIN tb_status_lcc AS s
             * ON a.id_status_lcc = s.id_status_lcc
             * ORDER BY CASE 
             *    WHEN s.nu_ordem_column = 2 THEN a.dt_final
             *    ELSE a.dt_cadastro
             * END ASC
             */

            $query->join('l', 'tb_status_lcc', 's', 'l.id_status_lcc = s.id_status_lcc')
                    ->andWhere('l.id_status_lcc = :status')
                    ->setParameter('status', $filter['status'])
                    ->orderBy('CASE WHEN s.nu_ordem_column = 2 THEN l.dt_final ELSE l.dt_cadastro END', 'DESC');
        }
        //print $query->getSQL(); exit;
       //print $query->getSQL();
        
        // Adaptator DBAL ao invés de ORM
        $adapter = new \Pagerfanta\Adapter\DoctrineDbalAdapter($query, function($query) {

            $query->select('COUNT(l.*) AS total_results')
                    ->setMaxResults(1)->resetQueryPart('orderBy');
        });
        return $adapter;
        //return new \Pagerfanta\Pagerfanta($adapter);
    }
    
     /**
     * Retorna um pagination com drive DBAL, ou seja, os resultados não são ORM e
     * sim um array.
     * Método utilizado em função de um erro no paginador com inner join do doctrine na versão 2.4
     * 
     * @link https://groups.google.com/forum/#!msg/doctrine-user/AZyudulmXWE/z9oqG3e4DWoJ
     * @link http://www.doctrine-project.org/2015/01/25/orm-2-5-0-alpha-2.html
     * @deprecated since Doctrine 2.5.0-alpha2
     * @todo Quando atualizar o php >=5.4 e o doctrine para >=2-5-0-alpha-2, o método
     * getConteudoInterna() pode voltar a ser utilizado
     * @return \Pagerfanta\Pagerfanta
     */
    public function getResultsFromPagination(\Pagerfanta\Pagerfanta $pagination){

        $array = array();
        
        foreach($pagination as $row) {
           $array[] = $row['id_licitacao_convenio_contrato'];
        }
        
        if(empty($array)) return array();
        
        $queryBuilder = $this->getEntityManager()->createQueryBuilder();
        
        $query = $queryBuilder->select('a')
                      ->from($this->getEntityName(), 'a')
                      ->where($queryBuilder->expr()->in('a.id', $array));        

        return $query->getQuery()->getResult();
    }
    
    public function getConteudoInterna($status = NULL, $categoria = NULL, $tipo = NULL) {
                    
        $query = $this->getEntityManager()->createQueryBuilder()
                      ->select('a')
                      ->from($this->getEntityName(), 'a')
                      ->andWhere('a.publicado = 1')
                      ->andWhere('a.dataInicial < :today')
                      ->andWhere('a.dataFinal > :today OR a.dataFinal IS NULL')
                      ->setParameter('today', new \DateTime('now'))
                      ->orderBy('a.dataInicial', 'DESC');
                      

        if (!empty($status)) {
            
            /**
             * SELECT * FROM tb_licitacao_convenio_contrato AS a
             * INNER JOIN tb_status_lcc AS s
             * ON a.id_status_lcc = s.id_status_lcc
             * ORDER BY CASE 
             *      WHEN s.nu_ordem_column = 2 THEN a.dt_final
             *      ELSE a.dt_cadastro
             * END ASC
             */
            
            $query->join('Entity\StatusLcc', 'S', 'WITH', 'a.status = S.id')
                    ->andWhere('a.status = :status')
                    ->setParameter('status', $status)
                    ->addSelect('(CASE WHEN S.column = 2 THEN a.dataFinal ELSE a.dataCadastro END) AS HIDDEN ORD')
                    ->orderBy('ORD', 'DESC');
        }

        if (!empty($tipo)) {
            $query->andWhere('a.tipo = :tipo')
                  ->setParameter('tipo', $tipo);
        }

        if (!empty($categoria)) {
            $query->andWhere('a.categoria = :categoria')
                  ->setParameter('categoria', $categoria);
        }
      

        return $query->getQuery()
                    ->useQueryCache(TRUE)
                    ->useResultCache(TRUE, CACHE_LIFE_TIME);
    }
    
    /**
     * 
     * @param $id
     * @param $tipo
     * @return integer
     */
    public function getLccVinculado($id, $tipo)
    {
        try {
            $query = $this->createQueryBuilder('e')
                        ->select('e.objeto')
                        ->distinct()
                        ->andWhere("e.{$tipo} = :id");

            return $query->setParameter('id', $id)
                        ->getQuery()
                        ->getResult();
        } catch (\Doctrine\ORM\NoResultExceptionption $e) {
            return false;
        }
    }
}
