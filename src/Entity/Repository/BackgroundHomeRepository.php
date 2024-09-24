<?php

namespace Entity\Repository;

//talvez remover depois
use \Doctrine\ORM\EntityRepository;

class BackgroundHomeRepository extends BaseRepository
{
    protected $entity = 'Entity\BackgroundHome';
    protected $search_column = 'nome';

    /**
     * 
     * @return int
     */
    public function countAll()
    {
        //monta o dql
        $dql = "SELECT COUNT(e) total FROM {$this->entity} e ";

        //Executa a query e retorna o resultado
        $query = $this->getEntityManager()->createQuery($dql);
        return $query->getSingleScalarResult();
    }

    /**
     * 
     * @param int $limit
     * @param int $offset
     * @param array $filtro
     * @return array
     */
    public function getBusca($limit, $offset, $filtro)
    {
        //Estrutura o sql da busca
        $dql = "SELECT e FROM {$this->entity} e WHERE 1 = 1 ";
        $parametros = array();

        if (!empty($filtro['busca'])) {
            $dql .= " AND LOWER(e.{$this->search_column}) LIKE :{$this->search_column} ";
            $parametros[$this->search_column] = "%" . $filtro['busca'] . "%";
        }

        if ($filtro['status'] !== "") {
            $dql .= " AND e.publicado = :publicado ";
            $parametros['publicado'] = $filtro['status'];
        }

        if (!empty($filtro['data_inicial']) && !empty($filtro['data_final'])) {
            $dql .= " AND (e.dataCadastro BETWEEN :data_inicial AND :data_final  )";
            $parametros['data_inicial'] = $filtro['data_inicial'] . " 00:00:00";
            $parametros['data_final'] = $filtro['data_final'] . " 23:59:59";
        }

        //Ordena os dados
        //$dql .= " ORDER BY e.dataCadastro DESC ";
        $dql .= " ORDER BY e.nome ASC ";

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
     * @param array $filtro
     * @return integer
     */
    public function getTotal($filtro)
    {
        //Estrutura o sql da busca
        $dql = "SELECT COUNT(e) total FROM {$this->entity} e WHERE 1 = 1 ";
        $parametros = array();

        if (!empty($filtro['busca'])) {
            $dql .= " AND LOWER(e.{$this->search_column}) LIKE :{$this->search_column} ";
            $parametros[$this->search_column] = "%" . $filtro['busca'] . "%";
        }

        if ($filtro['status'] !== "") {
            $dql .= " AND e.publicado = :publicado ";
            $parametros['publicado'] = $filtro['status'];
        }

        if (!empty($filtro['data_inicial']) && !empty($filtro['data_final'])) {
            $dql .= " AND (e.dataCadastro BETWEEN :data_inicial AND :data_final  )";
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
     * @param $publicado
     * @return object
     */
    public function getBackgroundHomeFunc()
    {

    	return $this->getEntityManager()
                ->createQueryBuilder()
                ->select('b')
                ->from('Entity\BackgroundHome', 'b')
                ->andWhere('b.publicado = 1')
                // ->andWhere('b.dataInicial < CURRENT_TIMESTAMP()')
                // ->andWhere('b.dataFinal > CURRENT_TIMESTAMP() OR b.dataFinal IS NULL')
                ->andWhere('b.dataInicial <= :today')
                ->andWhere('b.dataFinal >= :today OR b.dataFinal IS NULL')
                ->setParameter('today', new \DateTime('now'))
                ->getQuery()
                ->setMaxResults(1)->getOneOrNullResult();

    }
}
