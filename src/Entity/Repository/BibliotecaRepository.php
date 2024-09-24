<?php
namespace Entity\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * Description of BibliotecaRepository
 *
 * @author Eduardo
 */
class BibliotecaRepository extends BaseRepository
{

    public function getConteudoInterna($estado = NULL, $busca = NULL)
    {
        $query = $this->createQueryBuilder('a')
                      ->distinct()
                      ->andWhere('a.publicado = 1')
                      ->andWhere('a.dataInicial < :today')
                      ->andWhere('a.dataFinal > :today OR a.dataFinal IS NULL');

        if (!empty($estado)) {
            $query->andWhere('a.uf= :estado')
                  ->setParameter('estado', $estado);
        }

        if (!empty($busca)) {
            $conditions = $query->expr()->orX(
                'lower(a.nome) LIKE lower(:busca)',
                'lower(a.descricao) LIKE lower(:busca)',
                'lower(a.responsavel) LIKE lower(:busca)',
                'lower(a.horarioFuncionamento) LIKE lower(:busca)',
                'lower(a.bairro) LIKE lower(:busca)',
                'lower(a.cidade) LIKE lower(:busca)',
                'lower(a.uf) LIKE lower(:busca)'
            );
            $query->andWhere($conditions)
                  ->setParameter('busca', '%' . $busca . '%');
        }

        return $query->orderBy('a.dataInicial', 'DESC')
                     ->setParameter('today', new \DateTime('now'))
                     ->getQuery()
                     ->useQueryCache(TRUE)
                     ->useResultCache(TRUE, CACHE_LIFE_TIME);
    }

    
     public function getConteudoInternaOrder($estado = NULL, $busca = NULL)
    {
        $query = $this->createQueryBuilder('a')
                      ->distinct()
                      ->andWhere('a.publicado = 1')
                      ->andWhere('a.dataInicial < :today')
                      ->andWhere('a.dataFinal > :today OR a.dataFinal IS NULL');

        if (!empty($estado)) {
            $query->andWhere('a.uf= :estado')
                  ->setParameter('estado', $estado);
        }

        if (!empty($busca)) {
            $conditions = $query->expr()->orX(
                'lower(a.nome) LIKE lower(:busca)',
                'lower(a.descricao) LIKE lower(:busca)',
                'lower(a.responsavel) LIKE lower(:busca)',
                'lower(a.horarioFuncionamento) LIKE lower(:busca)',
                'lower(a.bairro) LIKE lower(:busca)',
                'lower(a.cidade) LIKE lower(:busca)',
                'lower(a.uf) LIKE lower(:busca)'
            );
            $query->andWhere($conditions)
                  ->setParameter('busca', '%' . $busca . '%');
        }

        return $query->orderBy('a.ordem', 'ASC')
                     ->setParameter('today', new \DateTime('now'))
                     ->getQuery()
                     ->useQueryCache(TRUE)
                     ->useResultCache(TRUE, CACHE_LIFE_TIME);
    }
    
    
    public function getEstados()
    {
        return $this->createQueryBuilder('b')
                    ->select('b.uf')
                    ->distinct()
                    ->groupBy('b.uf')
                    ->getQuery()
                    ->getResult();
    }

    /**
     * 
     * @return int
     */
    public function countAll()
    {
        //monta o dql
        $dql = "SELECT COUNT(b) total FROM Entity\Biblioteca b  ";

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
    public function getBuscarBiblioteca($limit, $offset, $filtro)
    {
        //Estrutura o sql da busca
        $dql = "SELECT b FROM Entity\Biblioteca b WHERE 1 = 1 ";
        $parametros = array();

        if (!empty($filtro['busca'])) {
            $dql .= " AND LOWER(CONCAT(b.nome, b.descricao)) LIKE :nome ";
            $parametros['nome'] = "%" . $filtro['busca'] . "%";
        }

        if ($filtro['status'] !== "") {
            $dql .= " AND b.publicado = :publicado ";
            $parametros['publicado'] = $filtro['status'];
        }

        if (!empty($filtro['data_inicial']) && !empty($filtro['data_final'])) {
            $dql .= " AND (b.dataCadastro BETWEEN :data_inicial AND :data_final  )";
            $parametros['data_inicial'] = $filtro['data_inicial'] . " 00:00:00";
            $parametros['data_final'] = $filtro['data_final'] . " 23:59:59";
        }

        //Ordena os dados
        $dql .= " ORDER BY b.dataCadastro DESC ";

        //Executa aquery e passa os parâmetros
        $query = $this->getEntityManager()->createQuery($dql);
        $query->setParameters($parametros);

        if (($limit + $offset) > 0) {
            $query->setFirstResult($offset)
                    ->setMaxResults($limit);
        }

        return $query->getResult();
    }

 
    public function getBuscarBibliotecaOrder($limit, $offset, $filtro)
    {
        //Estrutura o sql da busca
        $dql = "SELECT b FROM Entity\Biblioteca b WHERE 1 = 1 ";
        $parametros = array();

        if (!empty($filtro['busca'])) {
            $dql .= " AND LOWER(CONCAT(b.nome, b.descricao)) LIKE :nome ";
            $parametros['nome'] = "%" . $filtro['busca'] . "%";
        }

        if ($filtro['status'] !== "") {
            $dql .= " AND b.publicado = :publicado ";
            $parametros['publicado'] = $filtro['status'];
        }

        if (!empty($filtro['data_inicial']) && !empty($filtro['data_final'])) {
            $dql .= " AND (b.dataCadastro BETWEEN :data_inicial AND :data_final  )";
            $parametros['data_inicial'] = $filtro['data_inicial'] . " 00:00:00";
            $parametros['data_final'] = $filtro['data_final'] . " 23:59:59";
        }

        //Ordena os dados
        $dql .= " ORDER BY b.ordem ASC";

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
    public function getTotalBiblioteca($filtro)
    {
        //Estrutura o sql da busca
        $dql = "SELECT COUNT(b) total FROM Entity\Biblioteca b WHERE 1 = 1 ";
        $parametros = array();

          if (!empty($filtro['busca'])) {
            $dql .= " AND LOWER(CONCAT(b.nome, b.descricao)) LIKE :nome ";
            $parametros['nome'] = "%" . $filtro['busca'] . "%";
        }

        if ($filtro['status'] !== "") {
            $dql .= " AND b.publicado = :publicado ";
            $parametros['publicado'] = $filtro['status'];
        }

        if (!empty($filtro['data_inicial']) && !empty($filtro['data_final'])) {
            $dql .= " AND (b.dataCadastro BETWEEN :data_inicial AND :data_final  )";
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
        
        $dql = "SELECT (max(p.ordem)+1) as ordem FROM Entity\Biblioteca p";
        $query = $this->getEntityManager()->createQuery($dql);
        $aux = $query->getResult();
        
        foreach ($aux as $a){
            $ordem = $a["ordem"];
        }
        
       return $ordem;
    }

}
