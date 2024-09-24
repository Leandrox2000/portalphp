<?php

namespace Entity\Repository;

/**
 * Description of UnidadeRepository
 *
 * @author Eduardo
 */
class UnidadeRepository extends BaseRepository
{

    /**
     * Retorna as opções para preencher um select.
     *
     * @param integer $id
     * @return array
     */
    public function selectOptions($id = NULL)
    {
        $query = $this->createQueryBuilder('e');

        if (!empty($id)) {
            $query->where('e.id <> :id')
                  ->setParameter('id', $id);
        }

        return $query->getQuery()
                     ->getResult();
    }

    /**
     *
     * @return int
     */
    public function countAll()
    {
        //monta o dql
        $dql = "SELECT COUNT(DISTINCT uni) FROM Entity\Unidade uni ";

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
    public function getBuscaUnidade($limit, $offset, $filtro)
    {
        //Estrutura o dql da busca
        $dql = "SELECT uni FROM Entity\Unidade uni WHERE 1 = 1 ";
        $parametros = array();

        if (!empty($filtro['busca'])) {
            $dql .= " AND semAcento(LOWER(uni.nome)) LIKE semAcento(lower(:nome)) ";
            $parametros['nome'] = "%" . $filtro['busca'] . "%";
        }

        if (!empty($filtro['data_inicial']) && !empty($filtro['data_final'])) {
            $dql .= " AND (uni.dataCadastro BETWEEN :data_inicial AND :data_final  )";
            $parametros['data_inicial'] = $filtro['data_inicial'] . " 00:00:00";
            $parametros['data_final'] = $filtro['data_final'] . " 23:59:59";
        } else if (!empty($filtro['data_final'])) {
            $dql .= " AND uni.dataCadastro < :data_final ";
            $parametros['data_final'] = $filtro['data_final'] . " 23:59:59";
        } else if (!empty($filtro['data_inicial'])) {
            $dql .= " AND (uni.dataCadastro > :data_inicial ) ";
            $parametros['data_inicial'] = $filtro['data_inicial'] . " 00:00:00";
        }

        //Ordena os dados
        $dql .= " ORDER BY uni.nome ASC ";

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
     * @param int $limit
     * @param int $offset
     * @param array $filtros
     * @return array
     */
    public function getBuscaUnidadeOrder($limit, $offset, $filtro)
    {
        //Estrutura o dql da busca
        $dql = "SELECT uni FROM Entity\Unidade uni WHERE 1 = 1 ";
        $parametros = array();

        if (!empty($filtro['busca'])) {
            $dql .= " AND semAcento(LOWER(uni.nome)) LIKE semAcento(lower(:nome)) ";
            $parametros['nome'] = "%" . $filtro['busca'] . "%";
        }

        if (!empty($filtro['data_inicial']) && !empty($filtro['data_final'])) {
            $dql .= " AND (uni.dataCadastro BETWEEN :data_inicial AND :data_final  )";
            $parametros['data_inicial'] = $filtro['data_inicial'] . " 00:00:00";
            $parametros['data_final'] = $filtro['data_final'] . " 23:59:59";
        } else if (!empty($filtro['data_final'])) {
            $dql .= " AND uni.dataCadastro < :data_final ";
            $parametros['data_final'] = $filtro['data_final'] . " 23:59:59";
        } else if (!empty($filtro['data_inicial'])) {
            $dql .= " AND (uni.dataCadastro > :data_inicial ) ";
            $parametros['data_inicial'] = $filtro['data_inicial'] . " 00:00:00";
        }

        //Ordena os dados
        $dql .= " ORDER BY uni.ordem ASC ";

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
     * @param int $limit
     * @param int $offset
     * @param array $filtros
     * @return array
     */
    public function getUnidade($limit, $offset, $filtro = null)
    {
        $query = $this->createQueryBuilder('u')
                    ->distinct()
                    ->orderBy('u.nome', 'asc');

        if (!empty($filtro)) {
            $conditions = $query->expr()->orX(
                'semAcento(lower(u.nome)) LIKE semAcento(lower(:busca))',
                'semAcento(lower(u.uf)) LIKE semAcento(lower(:busca))',
                'semAcento(lower(u.cidade)) LIKE semAcento(lower(:busca))',
                'semAcento(lower(u.estado)) LIKE semAcento(lower(:busca))'
            );
            $query->andWhere($conditions)
                  ->setParameter('busca', '%' . $filtro . '%');
        }

        $query->setFirstResult(($offset * $limit) - $limit)
            ->setMaxResults($limit);
        try {
            $return['unidade'] = $query->getQuery()->getResult();
            $return['pagina'] = $query->getQuery();
            return $return;
        } catch (\Doctrine\ORM\NoResultException $e) {
            return null;
        }
    }
    
    
    
      /**
     *
     * @param int $limit
     * @param int $offset
     * @param array $filtros
     * @return array
     */
    public function getUnidadeOrder($limit, $offset, $filtro = null)
    {
        $query = $this->createQueryBuilder('u')
                    ->distinct()
                    ->orderBy('u.ordem', 'asc');

        if (!empty($filtro)) {
            $conditions = $query->expr()->orX(
                'semAcento(lower(u.nome)) LIKE semAcento(lower(:busca))',
                'semAcento(lower(u.uf)) LIKE semAcento(lower(:busca))',
                'semAcento(lower(u.cidade)) LIKE semAcento(lower(:busca))',
                'semAcento(lower(u.estado)) LIKE semAcento(lower(:busca))'
            );
            $query->andWhere($conditions)
                  ->setParameter('busca', '%' . $filtro . '%');
        }
        
        $query->setFirstResult(($offset * $limit) - $limit)
            ->setMaxResults($limit);
        try {
            $return['unidade'] = $query->getQuery()->getResult();
            $return['pagina'] = $query->getQuery();
            return $return;
        } catch (\Doctrine\ORM\NoResultException $e) {
            return null;
        }
    }
    

    /**
     *
     * @param int $limit
     * @param int $offset
     * @param array $filtros
     * @return array
     */
    public function getTotalBuscaUnidade($filtro)
    {
        //Estrutura o dql da busca
        $dql = "SELECT COUNT(uni) total FROM Entity\Unidade uni WHERE 1 = 1 ";
        $parametros = array();

        if (!empty($filtro['busca'])) {
            $dql .= " AND semAcento(LOWER(uni.nome)) LIKE semAcento(LOWER(:nome))";
            $parametros['nome'] = "%" . $filtro['busca'] . "%";
        }

        if (!empty($filtro['data_inicial']) && !empty($filtro['data_final'])) {
            $dql .= " AND (uni.dataCadastro BETWEEN :data_inicial AND :data_final  )";
            $parametros['data_inicial'] = $filtro['data_inicial'] . " 00:00:00";
            $parametros['data_final'] = $filtro['data_final'] . " 23:59:59";
        }

        //Executa aquery e passa os parâmetros
        $query = $this->getEntityManager()->createQuery($dql);
        $query->setParameters($parametros);

        $resultado = $query->getResult();
        return $resultado[0]['total'];
    }
    
    
      public function buscarUltimaOrdem(){
        
        $dql = "SELECT (max(p.ordem)+1) as ordem FROM Entity\Unidade p";
        $query = $this->getEntityManager()->createQuery($dql);
        $aux = $query->getResult();
        
        foreach ($aux as $a){
            $ordem = $a["ordem"];
        }
        
       return $ordem;
    }

}
