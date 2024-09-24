<?php

namespace Entity\Repository;

use Helpers\Session;

/**
 * Description of DiretoriaRepository
 */
class DiretoriaRepository extends BaseRepository
{

    /**
     *
     * @var array
     */
    private $where;

    /**
     *
     * @var Session
     */
    private $session;

    /**
     * Retorna a query
     *
     * @return \Doctrine\ORM\QueryBuilder
     */
    private function getQueryDiretores()
    {
        $query = $this->getEntityManager()
                      ->createQueryBuilder()
                      ->distinct()
                      ->select('n, d')
                      ->from('Entity\Funcionario', 'n')
                      ->leftJoin('n.diretor', 'd');

        return $query;
    }

    /**
     * Seta o where da busca
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

        if (!empty($busca)) {
            $query->andWhere(
                $this->getEntityManager()
                     ->createQueryBuilder()
                     ->orWhere($query->expr()
                                     ->like($query->expr()->lower("n.nome"), ":busca"))
                     ->getDQLPart("where")
            );
            $query->setParameter("busca", "%{$busca}%");
        }

        if ($status !== "") {
            if ($status == '0') {
                $conditions = $query->expr()->orX(
                    'd.publicado = :status',
                    'd.publicado IS NULL'
                );
                $query->andWhere($conditions);
            } else {
                $query->andWhere($query->expr()->eq("d.publicado", ":status"));
            }
            $query->setParameter("status", $status);
        }

        $query->andWhere($query->expr()->eq("n.publicado", 1));
        $query->andWhere($query->expr()->eq("n.exibirPortal", 1));
        $query->andWhere($query->expr()->eq("n.diretoria", 1));

        return $query;
    }

    /**
     *
     * @param type $limit
     * @param type $offset
     * @param array $filtro
     * @param \Helpers\Session $session
     * @return type
     */
    public function getDiretores($limit, $offset, array $filtro, Session $session)
    {
        $this->session = $session;

        $query = $this->getQueryDiretores();
        $query = $this->setWhere($query, $filtro);
        $query->orderBy('d.ordem', 'ASC');
        if($limit>0){
        $query->setMaxResults($limit);
        }
        $query->setFirstResult($offset);

        return $query->getQuery()->getResult();
    }

    /**
     * Número total de Notícias com filtro
     *
     * @return int
     */
    public function getMaxResult()
    {
        $query = $this->getQueryDiretores();
        $query->select($query->expr()->countDistinct("n"));
        $query = $this->setWhere($query);

        return $query->getQuery()->getSingleScalarResult();
    }

    /**
     * Numero total de Notícias do Banco
     *
     * @return int
     */
    public function countAll()
    {
        $query = $this->getQueryDiretores();
        $query->select($query->expr()->countDistinct("n"));
        $query = $this->setWhere($query);

        return $query->getQuery()->getSingleScalarResult();
    }

    /**
     *
     * @return \Doctrine\DBAL\Query\QueryBuilder
     */
    public function getConteudoInterna()
    {
        $query = $this->getEntityManager()
                      ->createQueryBuilder()
                      ->select('a, d')
                      ->from('Entity\Funcionario', 'a')
                      ->distinct()
                      ->leftJoin('a.cargo', 'c')
                      ->leftJoin('a.diretor', 'd')
                      ->andWhere('d.publicado = 1')
                      ->andWhere('a.publicado = 1')
                      ->andWhere('a.exibirPortal = 1')
                      ->andWhere('a.diretoria = 1')
                      ->andWhere('a.dataInicial < :today')
                      ->andWhere('a.dataFinal > :today OR a.dataFinal IS NULL');

        return $query->orderBy('d.ordem', 'ASC')
                     ->setParameter('today', new \DateTime('now'))
                     ->getQuery()
                     ->useQueryCache(TRUE)
                     ->useResultCache(TRUE, CACHE_LIFE_TIME);
    }

}
