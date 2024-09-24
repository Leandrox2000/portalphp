<?php

namespace Entity\Repository;

/**
 * Description of BibliografiaRepository
 *
 * @author Luciano
 */
class BibliografiaRepository extends BaseRepository
{

    /**
     *
     * @var array
     */
    private $where;

    public function getConteudoInterna($letra = NULL)
    {
        $query = $this->createQueryBuilder('a')
                      ->distinct()
                      ->andWhere('a.publicado = 1')
                      ->andWhere('a.dataInicial < :today')
                      ->andWhere('a.dataFinal > :today OR a.dataFinal IS NULL');

        if (!empty($letra)) {
            $query->andWhere('LOWER(a.conteudo) LIKE LOWER(:primeiraLetra)')
                  ->setParameter('primeiraLetra', $letra. '%');
        }

        return $query->orderBy('a.conteudo', 'ASC')
                     ->setParameter('today', new \DateTime('now'))
                     ->getQuery()
                     ->useQueryCache(TRUE)
                     ->useResultCache(TRUE, CACHE_LIFE_TIME);
    }

    /**
     * Retorna a Query dos Bibliografias
     *
     * @return \Doctrine\ORM\QueryBuilder
     */
    private function getQueryBibliografias()
    {
        $query = $this->getEntityManager()
                ->createQueryBuilder()
                ->select("B")
                ->from($this->getEntityName(), "B");

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
                            ->orWhere($query->expr()->like($query->expr()->lower("B.titulo"), ":busca"))
                            ->orWhere($query->expr()->like($query->expr()->lower("B.conteudo"), ":busca"))
                            ->getDQLPart("where")
            );
            $query->setParameter("busca", "%{$busca}%");
        }

        if ($status !== "") {
            $query->andWhere($query->expr()->eq("B.publicado", ":status"));
            $query->setParameter("status", $status);
        }

        if (!empty($data_inicial) && !empty($data_final)) {
            $query->andWhere($query->expr()->between("B.dataCadastro", "'{$data_inicial} 00:00'", "'{$data_final} 23:59'"));
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
    public function getBibliografias($limit, $offset, array $filtro)
    {

        $query = $this->getQueryBibliografias();

        $query = $this->setWhere($query, $filtro);
        $query->orderBy('B.dataCadastro', 'DESC');
        if($limit>0){
        $query->setMaxResults($limit);
        }
        $query->setFirstResult($offset);

        return $query->getQuery()->getResult();
    }

    /**
     * Número total de Bibliografias com filtro
     *
     * @return int
     */
    public function getMaxResult()
    {
        $query = $this->getQueryBibliografias();

        $query->select($query->expr()->countDistinct("B"));

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
        $query = $this->getQueryBibliografias();

        $query->select($query->expr()->countDistinct("B"));

        return $query->getQuery()->getSingleScalarResult();
    }

}
