<?php

namespace Entity\Repository;

/**
 * Description of FototecaRepository
 *
 * @author Eduardo
 */
class FototecaRepository extends BaseRepository
{
    
    public function getRelacionados($id)
    {
        $query = $this->createQueryBuilder('F')
                    ->select("F, FR")
                    ->leftJoin("F.fototecasFilhas", "FR")
                    ->andWhere('F.publicado = 1')
                    ->andWhere('FR.publicado = 1')
                    ->andWhere('F.dataInicial < :today')
                    ->andWhere('F.dataFinal > :today OR F.dataFinal IS NULL')
                    ->andWhere('FR.dataInicial < :today')
                    ->andWhere('FR.dataFinal > :today OR FR.dataFinal IS NULL');        
        $query->andWhere('F.id = :id');
        
        return $query->orderBy('FR.ordem', 'ASC')
                    ->setParameter('id', $id)
                    ->setParameter('today', new \DateTime('now'))
                    ->getQuery()
                    ->useQueryCache(TRUE)
                    ->useResultCache(TRUE, CACHE_LIFE_TIME)
                    ->getOneOrNullResult();
    }

    /**
     * 
     * @param integer $categoria ID da categoria
     * @param string $busca Query de busca.
     * @return string
     */
    public function getQueryPortal($site = NULL, $categoria = NULL, $busca = NULL, $limit = null, $offset = null)
    {
        $query = $this->createQueryBuilder('e')
                      ->distinct()
                      ->andWhere('e.publicado = 1')
                      ->andWhere('e.dataInicial < :today')
                      ->andWhere('e.dataFinal > :today OR e.dataFinal IS NULL');

        if (!empty($categoria)) {
            $query->andWhere('e.categoria = :categoria')
                  ->setParameter('categoria', $categoria);
        }

//        if ($site instanceof \Entity\Site) {
//            $query->andWhere("s.sigla = :sigla")
//                  ->setParameter('sigla', $site->getSigla());
//        } else {
//            $query->andWhere("s.sigla = 'SEDE'");
//        }

        if (!empty($busca)) {
            $query->andWhere('LOWER(CONCAT(e.descricao, e.titulo)) LIKE LOWER(:busca)')
                  ->setParameter('busca', '%' . $busca . '%');
        }
        
        if (($limit + $offset) > 0) {
            $query->setFirstResult($offset)
                    ->setMaxResults($limit);
        }
        
        return $query->orderBy('e.ordem', 'ASC')
                     ->setParameter('today', new \DateTime('now'))
                     ->getQuery()
                     ->useQueryCache(TRUE)
                     ->useResultCache(TRUE, CACHE_LIFE_TIME);
    }

    /**
     * 
     * @return int
     */
    public function countAll($user)
    {
        //monta o dql
        $dql = "SELECT COUNT(DISTINCT fot) FROM Entity\Fototeca fot";

        //Executa a query e retorna o resultado
        $query = $this->getEntityManager()->createQuery($dql);
        return $query->getSingleScalarResult();
    }

    /**
     * 
     * @param int $limit
     * @param int $offset
     * @param array $filtros
     * @param \Entity\Usuario $user
     * @return array
     */
    public function getBuscaFototeca($limit, $offset, $filtro, array $user)
    {
        //Estrutura o sql da busca
        $dql = "SELECT DISTINCT fot FROM Entity\Fototeca fot WHERE 1 = 1 ";
        $parametros = array();

        if (!empty($filtro['busca'])) {
            $dql .= " AND Unaccent(LOWER(CONCAT(fot.nome, fot.descricao))) LIKE Unaccent(:nome) ";
            $parametros['nome'] = "%" . $filtro['busca'] . "%";
        }

        if ($filtro['status'] !== "") {
            $dql .= " AND fot.publicado = :publicado ";
            $parametros['publicado'] = $filtro['status'];
        }

        if (!empty($filtro['data_inicial']) && !empty($filtro['data_final'])) {
            $dql .= " AND (fot.dataCadastro BETWEEN :data_inicial AND :data_final  )";
            $parametros['data_inicial'] = $filtro['data_inicial'] . " 00:00:00";
            $parametros['data_final'] = $filtro['data_final'] . " 23:59:59";
        }

        //Ordena os dados
        $dql .= " ORDER BY fot.ordem asc ";

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
     * @param \Entity\Usuario $user
     * @return array
     */
    public function getTotalBuscaFototeca($filtro, array $user)
    {
        //Estrutura o sql da busca
        $dql = "SELECT COUNT(DISTINCT fot) total FROM Entity\Fototeca fot WHERE 1 = 1 ";
        $parametros = array();

        if (!empty($filtro['busca'])) {
            $dql .= " AND LOWER(CONCAT(fot.nome, fot.descricao)) LIKE :nome ";
            $parametros['nome'] = "%" . $filtro['busca'] . "%";
        }

        if ($filtro['status'] !== "") {
            $dql .= " AND fot.publicado = :publicado ";
            $parametros['publicado'] = $filtro['status'];
        }

        if (!empty($filtro['data_inicial']) && !empty($filtro['data_final'])) {
            $dql .= " AND (fot.dataCadastro BETWEEN :data_inicial AND :data_final  )";
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
        $dql = "SELECT count(fot) total FROM Entity\Fototeca fot JOIN fot.categoria cat WHERE cat.id = :id_categoria  ";
        $parametros['id_categoria'] = $categoria;

        //Executa a query e retorna o resultado
        $query = $this->getEntityManager()->createQuery($dql);
        $query->setParameters($parametros);
        $result = $query->getResult();

        return $result[0]['total'] == 0 ? true : false;
    }

    /**
     * 
     * @param $id
     * @return integer
     */
    public function getVinculadoGaleria($id)
    {
        try {
            $query = $this->createQueryBuilder('e')
                        ->select('e.nome')
                        ->distinct()
                        ->andWhere('e.galerias = :id');

            return $query->setParameter('id', $id)
                        ->getQuery()
                        ->getResult();
        } catch (\Doctrine\ORM\NoResultExceptionption $e) {
            return false;
        }
    }

}