<?php

namespace Entity\Repository;

class DicionarioPatrimonioCulturalRepository extends BaseRepository
{

    protected $entity = 'Entity\DicionarioPatrimonioCultural';
    protected $search_column = 'titulo';

    public function getConteudoInterna($categoria = NULL, $busca = NULL, $letra = NULL)
    {
        
        $query = $this->createQueryBuilder('a')
                      ->distinct()
                      ->andWhere('a.publicado = 1')
                      ->andWhere('a.dataInicial < :today')
                      ->andWhere('a.dataFinal > :today OR a.dataFinal IS NULL');

        if (!empty($categoria)) {
            $query->andWhere('a.categoria = :cat')
                  ->setParameter('cat', $categoria);
        }

        if ($busca) {
            $conditions = $query->expr()->orX(
                'Unaccent(lower(a.titulo)) LIKE Unaccent(lower(:busca))',
                'Unaccent(lower(a.descricao)) LIKE Unaccent(lower(:busca))',
                'Unaccent(lower(a.fichaTecnica)) LIKE Unaccent(lower(:busca))'
            );
            $query->andWhere($conditions)
                  ->setParameter('busca', '%' . $busca . '%');
        }else{
        
            if (!empty($letra)) {
                $query->andWhere('Unaccent(LOWER(a.titulo)) LIKE Unaccent(LOWER(:primeiraLetra))')
                      ->setParameter('primeiraLetra', $letra. '%');
            }
        }
        return $query->orderBy('a.titulo', 'ASC')
                     ->setParameter('today', new \DateTime('now'))
                     ->getQuery()
                     ->useQueryCache(TRUE)
                     ->useResultCache(TRUE, CACHE_LIFE_TIME);
    }

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
            $dql .= " AND Unaccent(LOWER(CONCAT(e.{$this->search_column}, e.descricao))) LIKE Unaccent(:{$this->search_column}) ";
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
        $dql .= " ORDER BY e.titulo ASC ";

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
            $dql .= " AND Unaccent(LOWER(CONCAT(e.{$this->search_column}, e.descricao))) LIKE Unaccent(:{$this->search_column}) ";
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
     * @param integer $categoria
     * @return type
     */
    public function verificaVinculoCategoria($categoria)
    {
        //monta o dql
        $dql = "SELECT count(dic) total FROM {$this->entity} dic JOIN dic.categoria cat WHERE cat.id = :id_categoria  ";
        $parametros['id_categoria'] = $categoria;

        //Executa a query e retorna o resultado
        $query = $this->getEntityManager()->createQuery($dql);
        $query->setParameters($parametros);
        $result = $query->getResult();

        return $result[0]['total'] == 0 ? true : false;
    }

}
