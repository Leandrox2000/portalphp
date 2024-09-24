<?php
namespace Entity\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * Description of FuncionalidadeSiteRepository
 *
 * @author Rafael
 */
class FuncionalidadeSiteRepository extends BaseRepository
{

    public function getFuncionalidades($id_site = NULL){
        /*
        $dql = "SELECT t.id FROM {$this->getEntityName()} t WHERE t.id_site = {$id_site}";
        $query = $this->getEntityManager()->createQuery($dql);
        //$query->setParameters(array('id' => $id));
        $result = $query->getResult();
        $array = array();
        
        return $result;*/
        
        $query = $this->createQueryBuilder('FS');
        $query->select("FS, F");
        $query->join('FS.funcionalidade', "F");
        $query->join('FS.site', "S");
        $query->where('S.id = :site')
                ->setParameter('site', $id_site);
        $query->orderBy('FS.ordem', 'ASC');
        
        return $query->getQuery()->getResult();
    }

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

}
