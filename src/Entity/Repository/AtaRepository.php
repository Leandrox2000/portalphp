<?php

namespace Entity\Repository;

/**
 * Description of AtaRepository
 *
 * @author Luciano
 */
class AtaRepository extends BaseRepository
{

    /**
     *
     * @var array
     */
    private $where;

    public function getConteudoInterna($dataInicio = NULL, $dataFim = NULL)
    {
        $query = $this->createQueryBuilder('a')
                      ->distinct()
                      ->andWhere('a.publicado = 1')
                      ->andWhere('a.dataInicial < :today')
                      ->andWhere('a.dataFinal > :today OR a.dataFinal IS NULL');

        if (!empty($dataInicio) && !empty($dataFim)) {
            $dataFinal = \DateTime::createFromFormat('Y-m-d H:i:s', $dataFim . '-12-30 00:00:00');
            $dataInicial = \DateTime::createFromFormat('Y-m-d H:i:s', $dataInicio . '-01-01 00:00:00');
            $query->andWhere('a.dataReuniao >= :inicioData AND a.dataReuniao <= :fimData')
                  ->setParameter('inicioData', $dataInicial)
                  ->setParameter('fimData', $dataFinal);
        }

        return $query->orderBy('a.dataReuniao', 'DESC')
                     ->setParameter('today', new \DateTime('now'))
                     ->getQuery()
                     ->useQueryCache(TRUE)
                     ->useResultCache(TRUE, CACHE_LIFE_TIME);
    }


    /**
     * Retorna a Query das Atas
     *
     * @return \Doctrine\ORM\QueryBuilder
     */
    private function getQueryAtas()
    {
        $query = $this->getEntityManager()
                ->createQueryBuilder()
                ->select("A")
                ->from($this->getEntityName(), "A");


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

        $busca = mb_strtolower($this->where['busca'], "UTF-8");
        $status = $this->where['status'];
        $data_inicial = $this->where['data_inicial'];
        $data_final = $this->where['data_final'];

        if (!empty($busca)) {
            $query->andWhere(
                    $this->getEntityManager()
                            ->createQueryBuilder()
                            ->orWhere($query->expr()->like($query->expr()->lower("CONCAT(A.nome, A.descricao)"), ":busca"))
                            ->getDQLPart("where")
            );
            $query->setParameter("busca", "%{$busca}%");
        }

        if ($status !== "") {
            $query->andWhere($query->expr()->eq("A.publicado", ":status"));
            $query->setParameter("status", $status);
        }

        if (!empty($data_inicial) && !empty($data_final)) {
            $query->andWhere($query->expr()->between("A.dataCadastro", "'{$data_inicial} 00:00'", "'{$data_final} 23:59'"));
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
    public function getAtas($limit, $offset, array $filtro)
    {
        $query = $this->getQueryAtas();

        $query = $this->setWhere($query, $filtro);

        $query->orderBy('A.dataCadastro', 'DESC');
        if($limit>0){
        $query->setMaxResults($limit);
        }
        $query->setFirstResult($offset);

        return $query->getQuery()->getResult();
    }

    /**
     * Número total de Atas com filtro
     *
     * @return int
     */
    public function getMaxResult()
    {
        $query = $this->getQueryAtas();

        $query->select($query->expr()->countDistinct("A"));

        $query = $this->setWhere($query);

        return $query->getQuery()->getSingleScalarResult();
    }

    /**
     * Numero total de Atas do Banco
     *
     * @return int
     */
    public function countAll()
    {
        $query = $this->getQueryAtas();

        $query->select($query->expr()->countDistinct("A"));


        return $query->getQuery()->getSingleScalarResult();
    }

}
