<?php

namespace Entity\Repository;

use Helpers\Session;

/**
 * Description of EnderecoRodapeRepository
 *
 * @author Luciano
 */
class EnderecoRodapeRepository extends BaseRepository
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
     * Retorna a Query dos Conselheiros
     *
     * @return \Doctrine\ORM\QueryBuilder
     */
    private function getQueryEnderecos()
    {
        $query = $this->getEntityManager()
                        ->createQueryBuilder()
                        ->distinct()
                        ->select("E")
                        ->from($this->getEntityName(), "E");
//                        ->join("E.sites", "sites");

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

        $user           = $this->session->get("user");
        $busca          = mb_strtolower($this->where['busca'], "UTF-8");
        $status         = $this->where['status'];
        $site           = $this->where['site'];
        $data_inicial   = $this->where['data_inicial'];
        $data_final     = $this->where['data_final'];

        if (!empty($busca)) {
            $query->andWhere(
                    $this->getEntityManager()
                            ->createQueryBuilder()
                            ->orWhere($query->expr()->like($query->expr()->lower("E.endereco"), ":busca"))
                            ->getDQLPart("where")
            );
            $query->setParameter("busca", "%{$busca}%");
        }

        if ($status !== "") {
            $query->andWhere($query->expr()->eq("E.publicado", ":status"));
            $query->setParameter("status", $status);
        }

        if (!empty($data_inicial) && !empty($data_final)) {
            $query->andWhere($query->expr()->between("E.dataCadastro", "'{$data_inicial} 00:00'", "'{$data_final} 23:59'"));
        }

//        if ($user['sede']) {
//            if ($site > 0) {
//                $query->andWhere($query->expr()->eq("sites.id", ":site"));
//                $query->setParameter("site", $site);
//            }
//        } else {
//            $query->andWhere("sites.id IN(:sites)");
//            $query->setParameter("sites", $user['subsites']);
//        }

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
    public function getEnderecos($limit, $offset, array $filtro, Session $session)
    {
        $this->session = $session;

        $query = $this->getQueryEnderecos();
        $query = $this->setWhere($query, $filtro);
        $query->orderBy('E.dataCadastro', 'DESC');

        if ($limit > 0) {
            $query->setMaxResults($limit);
        }
        if ($offset > 0) {
            $query->setFirstResult($offset);
        }

        return $query->getQuery()->getResult();
    }

    public function getEndereco()
    {
        //, $offset = 0, array $filtro, Session $session
        //$this->session = $session;

        $query = $this->getQueryEnderecos();
        $query->select("E.endereco");
        //$query = $this->setWhere($query);//, $filtro
        $query->andWhere('E.publicado = 1')
                        ->andWhere('E.dataInicial < :today')
                        ->andWhere('E.dataFinal > :today OR E.dataFinal IS NULL');

//        if ($limit > 0) {
//            $query->setMaxResults($limit);
//        }
//        if ($offset > 0) {
//            $query->setFirstResult($offset);
//        }
        $today = new \DateTime('now');
        $query->setMaxResults(1)->setParameter('today', $today);
        
        $a = $query->getQuery()->getArrayResult();
        return $a[0];
    }

    /**
     * Número total de Notícias com filtro
     *
     * @return int
     */
    public function getMaxResult()
    {
        $query = $this->getQueryEnderecos();

        $query->select($query->expr()->countDistinct("E"));

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
//        $user = $this->session->get("user");
        $query = $this->getQueryEnderecos();

        $query->select($query->expr()->countDistinct("E"));

//        if (!$user['sede']) {
//            $query->andWhere("sites.id IN(:sites)");
//            $query->setParameter("sites", $user['subsites']);
//        }

        return $query->getQuery()->getSingleScalarResult();
    }

}
