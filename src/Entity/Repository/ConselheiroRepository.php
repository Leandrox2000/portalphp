<?php

namespace Entity\Repository;

/**
 * Description of ConselheiroRepository
 *
 * @author Luciano
 */
class ConselheiroRepository extends BaseRepository
{

    /**
     *
     * @var array
     */
    private $where;

    public function getConteudoInterna()
    {
        $query = $this->createQueryBuilder('a')
                      ->distinct()
                      ->andWhere('a.publicado = 1')
                      ->andWhere('a.dataInicial < :today')
                      ->andWhere('a.dataFinal > :today OR a.dataFinal IS NULL');

        return $query->orderBy('a.dataInicial', 'DESC')
                    ->orderBy('a.ordem', 'asc')
                    ->setParameter('today', new \DateTime('now'))
                    ->getQuery()
                    ->useQueryCache(TRUE)
                    ->useResultCache(TRUE, CACHE_LIFE_TIME);
    }

    /**
     * Retorna a Query dos Conselheiros
     *
     * @return \Doctrine\ORM\QueryBuilder
     */
    private function getQueryConselheiros()
    {
        $query = $this->getEntityManager()
                        ->createQueryBuilder()
                        ->select("C")
                        ->from($this->getEntityName(), "C");


        return $query;
    }


    /**
     * Seta  o Where da Busca
     *
     * @param \Doctrine\ORM\QueryBuilder
     * @param array $where
     * @return \Doctrine\ORM\QueryBuilder
     */
    private function setWhere(\Doctrine\ORM\QueryBuilder $query, array $where = array())
    {
        if (empty($this->where)) {
            $this->where = $where;
        }

        $busca          = mb_strtolower($this->where['busca'], "UTF-8");
        $status         = $this->where['status'];
        $data_inicial   = $this->where['data_inicial'];
        $data_final     = $this->where['data_final'];

        if (!empty($busca)) {
            $query->andWhere(
                    $this->getEntityManager()
                            ->createQueryBuilder()
                            ->orWhere($query->expr()->like($query->expr()->lower("C.nome"), ":busca"))
                            ->orWhere($query->expr()->like($query->expr()->lower("C.instituicao"), ":busca"))
                            ->getDQLPart("where")
            );
            $query->setParameter("busca", "%{$busca}%");
        }

        if ($status !== "") {
            $query->andWhere($query->expr()->eq("C.publicado", ":status"));
            $query->setParameter("status", $status);
        }

        if (!empty($data_inicial) && !empty($data_final)) {
            $query->andWhere($query->expr()->between("C.dataCadastro", "'{$data_inicial} 00:00'", "'{$data_final} 23:59'"));
        }

        return $query;
    }

    /**
     *
     * @param type $limit
     * @param type $offset
     * @param array $filtro
     * @return type
     */
    public function getConselheiros($limit, $offset, array $filtro)
    {
        $query = $this->getQueryConselheiros();

        $query = $this->setWhere($query, $filtro);
//        $query->orderBy('C.dataCadastro', 'DESC');
        $query->orderBy('C.ordem', 'asc');
        if($limit>0){
        $query->setMaxResults($limit);
        }
        $query->setFirstResult($offset);

        return $query->getQuery()->getResult();
    }

    /**
     * Número total de Conselheiros com filtro
     *
     * @return int
     */
    public function getMaxResult()
    {
        $query = $this->getQueryConselheiros();

        $query->select($query->expr()->countDistinct("C"));

        $query = $this->setWhere($query);

        return $query->getQuery()->getSingleScalarResult();
    }

    /**
     * Numero total de Conselheiros do Banco
     *
     * @return int
     */
    public function countAll()
    {
        $query  = $this->getQueryConselheiros();

        $query->select($query->expr()->countDistinct("C"));


        return $query->getQuery()->getSingleScalarResult();
    }

    /**
     * Seta posição
     *
     * @return boolean
     */
    public function setOrdem($array)
    {
        foreach($array as $i => $val){
            $query = $this->getEntityManager()
                ->createQueryBuilder()
                ->update('Entity\Conselheiro', 'c')
                ->set('c.ordem', $val)
                ->where('c.id = '.$i)
                ->getQuery();
            $query->execute();
        }
    }

}
