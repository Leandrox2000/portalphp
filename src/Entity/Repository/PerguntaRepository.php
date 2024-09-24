<?php

namespace Entity\Repository;

use \Doctrine\ORM\EntityRepository;

/**
 * Description of PerguntaRepository
 *
 * @author Luciano
 */
class PerguntaRepository extends BaseRepository
{

    /**
     *
     * @var array
     */
    private $where;

    public function getConteudoInterna($categoria = null)
    {
        $query = $this->createQueryBuilder('a')
                      ->distinct()
                      ->andWhere('a.publicado = 1')
                      ->andWhere('a.dataInicial < :today')
                      ->andWhere('a.dataFinal > :today OR a.dataFinal IS NULL');

        if (!empty($categoria)) {
            $query->andWhere('a.categoria = :categoria')
                  ->setParameter('categoria', $categoria);
        }

        return $query->orderBy('a.dataInicial', 'DESC')
                     ->setParameter('today', new \DateTime('now'))
                     ->getQuery()
                     ->useQueryCache(TRUE)
                     ->useResultCache(TRUE, CACHE_LIFE_TIME);
    }

    public function getById($id)
    {
        $query = $this->createQueryBuilder('a')
                      ->andWhere('a.id = :id');

        return $query->setParameter('id', $id)
                     ->getQuery()
                     ->useQueryCache(TRUE)
                     ->useResultCache(TRUE, CACHE_LIFE_TIME);
    }
    
    
     public function getConteudoInternaOrder($categoria = null)
    {
        $query = $this->createQueryBuilder('a')
                      ->distinct()
                      ->andWhere('a.publicado = 1')
                      ->andWhere('a.dataInicial < :today')
                      ->andWhere('a.dataFinal > :today OR a.dataFinal IS NULL');

        if (!empty($categoria)) {
            $query->andWhere('a.categoria = :categoria')
                  ->setParameter('categoria', $categoria);
        }

        return $query->orderBy('a.ordem', 'ASC')
                     ->setParameter('today', new \DateTime('now'))
                     ->getQuery()
                     ->useQueryCache(TRUE)
                     ->useResultCache(TRUE, CACHE_LIFE_TIME);
    }

    /**
     * Verifica se a categoria está vinculada com alguma pergunta
     *
     * @param int $idCategoria
     * @return int O número de perguntas vinculadas a categoria
     */
    public function verificaCategoriaTemVinculo($idCategoria)
    {
        $query = $this->getEntityManager()
                ->createQueryBuilder();
        $query->select("count(p)")
                ->from("Entity\\Pergunta", "p")
                ->where($query->expr()->eq("p.categoria", $idCategoria));

        return $query->getQuery()->getSingleScalarResult();
    }

    /**
     * Retorna a Query das Perguntas
     *
     * @return \Doctrine\ORM\QueryBuilder
     */
    private function getQueryPerguntas()
    {
       return $this->getEntityManager()
                ->createQueryBuilder()
                ->select("P")
                ->from($this->getEntityName(), "P")
                ->join("Entity\PerguntaCategoria", "PC", "WITH", "PC.id=P.categoria");

    }

    /**
     *
     * @param int $limit
     * @param int $offset
     * @param array $filtro
     * @return array
     */
    public function getPerguntas($limit, $offset, array $filtro = array())
    {
        $query = $this->getQueryPerguntas();

        $query = $this->setWhere($query, $filtro);

        $query->orderBy('P.dataCadastro', 'DESC');

        if($limit > 0){
            $query->setMaxResults($limit);
        }

        $query->setFirstResult($offset);

        return $query->getQuery()->getResult();
    }
    
    
    
    /**
     *
     * @param int $limit
     * @param int $offset
     * @param array $filtro
     * @return array
     */
    public function getPerguntasOrder($limit, $offset, array $filtro = array())
    {
        $query = $this->getQueryPerguntas();

        $query = $this->setWhere($query, $filtro);

        $query->orderBy('P.ordem', 'ASC');

        if($limit > 0){
            $query->setMaxResults($limit);
        }

        $query->setFirstResult($offset);

        return $query->getQuery()->getResult();
    }
    
    
    

    /**
     * Seta  o Where da Busca
     *
     * @param \Doctrine\ORM\QueryBuilder
     * @param array $where
     * @return \Doctrine\ORM\QueryBuilder
     */
    private function setWhere($query, array $where = array())
    {
        if (empty($this->where)) {
            $this->where = $where;
        }

        $busca = $this->where['busca'];
        $categoria = $this->where['categoria'];
        $data_inicial = $this->where['data_inicial'];
        $data_final = $this->where['data_final'];

        if (!empty($busca)) {
            $query->andWhere(
                    $this->getEntityManager()
                            ->createQueryBuilder()
                            ->orWhere("P.pergunta LIKE '%{$busca}%' ")
                            ->orWhere("P.resposta LIKE '%{$busca}%' ")
                            ->orWhere("PC.categoria LIKE '%{$busca}%' ")
                            ->getDQLPart("where")
            );
        }

        if (!empty($categoria)) {
            $query->andWhere("P.categoria = $categoria");
        }

        if (!empty($data_inicial) && !empty($data_final)) {
            $query->andWhere("P.dataCadastro BETWEEN '{$data_inicial} 00:00'  AND '{$data_final} 23:59' ");
        }

        return $query;
    }

    /**
     * Número total de pergutna com filtro
     *
     * @return int
     */
    public function getMaxResult()
    {
        $query = $this->getQueryPerguntas();

        $query->select("count(P)");

        $query = $this->setWhere($query);

        return $query->getQuery()->getSingleScalarResult();
    }

    /**
     * Numerototal de Pergutna do Banco
     *
     * @return int
     */
    public function countAll()
    {
        $sql = "SELECT count(p.id) FROM Entity\\Pergunta p";

        $query = $this->getEntityManager()->createQuery($sql);

        return $query->getSingleScalarResult();
    }

    /**
     *
     * @param String $ids
     */
    public function getPerguntaPublicacao(array $ids)
    {
        //Monta a dql
        $dql = "SELECT p.id  FROM Entity\Pergunta p WHERE p.id IN(" . implode(',', $ids) . ") AND CURRENT_TIMESTAMP() >= p.dataInicial";
        $query = $this->getEntityManager()->createQuery($dql);

        //Monta a string com os ids
        $stringIds = "";
        foreach ($query->getResult() as $value) {
            $stringIds .= $value['id'] . ",";
        }

        $stringIds = substr($stringIds, 0, -1);
        return $stringIds;
    }

    /**
     *
     * @param array $ids
     * @return boolean
     */
    public function verificaPeriodoPergunta(array $ids)
    {
        //Monta a dql
        $dql = "SELECT count(p) total FROM Entity\Pergunta p WHERE p.id IN(" . implode(',', $ids) . ") AND CURRENT_TIMESTAMP() >= p.dataInicial";
        $query = $this->getEntityManager()->createQuery($dql);
        $result = $query->getResult();

        if (count($ids) == $result[0]['total'])
            return true;
        else
            return false;
    }
    
    
       
    public function buscarUltimaOrdem(){
        
        $dql = "SELECT (max(p.ordem)+1) as ordem FROM Entity\Pergunta p";
        $query = $this->getEntityManager()->createQuery($dql);
        $aux = $query->getResult();
        
        foreach ($aux as $a){
            $ordem = $a["ordem"];
        }
        
       return $ordem;
    }
    

    /**
     * 
     * @param $id
     * @return integer
     */
    public function getPerguntaVinculado($id)
    {
        try {
            $query = $this->createQueryBuilder('e')
                        ->select('e.pergunta')
                        ->distinct()
                        ->andWhere('e.categoria = :id');

            return $query->setParameter('id', $id)
                        ->getQuery()
                        ->getResult();
        } catch (\Doctrine\ORM\NoResultExceptionption $e) {
            return false;
        }
    }
}
