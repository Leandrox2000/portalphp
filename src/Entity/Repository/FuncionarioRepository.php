<?php
namespace Entity\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * Description of FuncionarioRepository
 *
 * @author Eduardo
 */
class FuncionarioRepository extends BaseRepository
{

    /**
     * 
     * @return int
     */
    public function countAll()
    {
        //monta o dql
        $dql = "SELECT COUNT(DISTINCT func) FROM Entity\Funcionario func ";

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
    public function getBuscaFuncionario($limit, $offset, $filtro)
    {
        //Estrutura o dql da busca
        $dql = "SELECT DISTINCT func FROM Entity\Funcionario func  WHERE 1 = 1 ";
        $parametros = array();
        
        if (!empty($filtro['busca'])) {
            $dql .= " AND (semAcento(LOWER(func.nome)) LIKE semAcento(:nome) OR semAcento(LOWER(func.curriculo)) LIKE semAcento(:nome))";
            $parametros['nome'] = "%" . $filtro['busca'] . "%";
        }

        if ($filtro['status'] !== "") {
            $dql .= " AND func.publicado = :publicado ";
            $parametros['publicado'] = $filtro['status'];
        }

        if (!empty($filtro['data_inicial']) && !empty($filtro['data_final'])) {
            $dql .= " AND (func.dataCadastro BETWEEN :data_inicial AND :data_final  )";
            $parametros['data_inicial'] = $filtro['data_inicial'] . " 00:00:00";
            $parametros['data_final'] = $filtro['data_final'] . " 23:59:59";
        }
      
        //Ordena os dados
        //$dql .= " ORDER BY func.dataCadastro DESC ";
        $dql .= " ORDER BY func.nome ASC ";

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
    public function getTotalBuscaFuncionario($filtro)
    {
        //Estrutura o dql da busca
        $dql = "SELECT COUNT(DISTINCT func) total FROM Entity\Funcionario func  WHERE 1 = 1 ";
        $parametros = array();

        if (!empty($filtro['busca'])) {
            $dql .= " AND LOWER(CONCAT(func.nome, func.curriculo)) LIKE :nome ";
            $parametros['nome'] = "%" . $filtro['busca'] . "%";
        }

        if ($filtro['status'] !== "") {
            $dql .= " AND func.publicado = :publicado ";
            $parametros['publicado'] = $filtro['status'];
        }

        if (!empty($filtro['data_inicial']) && !empty($filtro['data_final'])) {
            $dql .= " AND (func.dataCadastro BETWEEN :data_inicial AND :data_final  )";
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
        $dql = "SELECT count(func) total FROM Entity\Funcionario func JOIN func.cargo carg WHERE carg.id = :id_cargo  ";
        $parametros['id_cargo'] = $cargo;

        //Executa a query e retorna o resultado
        $query = $this->getEntityManager()->createQuery($dql);
        $query->setParameters($parametros);
        $result = $query->getResult();
        
        return $result[0]['total'] == 0 ? true : false;
    }
    
     /**
     * 
     * @param integer $vinculo
     * @return type
     */
    public function verificaVinculo($vinculo)
    {
        //monta o dql
        $dql = "SELECT count(func) total FROM Entity\Funcionario func JOIN func.vinculo vinc WHERE vinc.id = :id_vinculo  ";
        $parametros['id_vinculo'] = $vinculo;

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
    public function getArquivosFuncionario($ids)
    {
        $dql = "SELECT func.imagem FROM Entity\Funcionario func WHERE func.id IN(" . implode(',', $ids) . ")";
        $query = $this->getEntityManager()->createQuery($dql);
        return $query->getResult();
    }
    
    /**
     * 
     * @return \Doctrine\DBAL\Query\QueryBuilder
     */
    public function getConteudoInterna()
    {
        $query = $this->createQueryBuilder('a')
                        ->distinct()
                        ->leftJoin("a.cargo", "c")
                        ->andWhere('a.publicado = 1')
                        ->andWhere('a.diretoria = 1')
                        ->andWhere('a.dataInicial < :today')
                        ->andWhere('a.dataFinal > :today OR a.dataFinal IS NULL')
                        ->orderBy("a.cargo", "ASC");

        return $query->orderBy('a.dataInicial', 'DESC')
                     ->setParameter('today', new \DateTime('now'))
                     ->getQuery()
                     ->useQueryCache(TRUE)
                     ->useResultCache(TRUE, CACHE_LIFE_TIME);
    }

    /**
     * Retorna um item, caso o mesmo esteja publicado.
     *
     * @param integer $id
     * @return object|boolean
     */
    public function getPublicadoDetalhe($id)
    {
        try {
            $query = $this->createQueryBuilder('e')
                        ->select('e, u, v')
                        ->distinct()
                        ->leftJoin('e.unidade', 'u')
                        ->leftJoin('e.vinculo', 'v')
                        ->andWhere('e.id = :id')
                        ->andWhere('e.publicado = 1')
                        ->andWhere('e.dataInicial <= :today')
                        ->andWhere('e.dataFinal >= :today OR e.dataFinal IS NULL')
                        ->setParameter('today', new \DateTime('now'));

            return $query->setParameter('id', $id)
                         ->getQuery()
                         ->useQueryCache(true)
                         ->useResultCache(true, CACHE_LIFE_TIME)
                         ->getSingleResult();
        } catch (\Doctrine\ORM\NoResultExceptionption $e) {
            return false;
        }
    }

    /**
     * 
     * @param $id
     * @return integer
     */
    public function getFuncionarioVinculado($id)
    {
        try {
            $query = $this->createQueryBuilder('e')
                        ->select('e.nome')
                        ->distinct()
                        ->andWhere('e.vinculo = :id');

            return $query->setParameter('id', $id)
                        ->getQuery()
                        ->getResult();
        } catch (\Doctrine\ORM\NoResultExceptionption $e) {
            return false;
        }
    }
    
    /**
     * Verifica se existe uma unidade vinculada ao funcionário.
     * 
     * @param string $ids
     * @param string $tipo
     * @return boolean
     */
    public function verificaVinculoByRelation($ids, $tipo) {
        // Monta o DQL
        $dql = "SELECT COUNT(funcionario) total 
                  FROM Entity\Funcionario funcionario 
                       JOIN funcionario.{$tipo} vinculo 
                 WHERE vinculo.id IN (:ids)  ";
        $parametros['ids'] = $ids;

        // Executa a query e retorna o resultado
        $query = $this->getEntityManager()->createQuery($dql);
        $query->setParameters($parametros);
        $result = $query->getResult();
        
        return $result[0]['total'] > 0 ? true : false;
    }
}
