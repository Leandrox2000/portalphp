<?php

namespace Entity\Repository;

use \Doctrine\ORM\EntityRepository;

/**
 * Description of LogoRodapeRepository
 *
 * @author Luciano
 */
class LogoRodapeRepository extends EntityRepository
{

    /**
     *
     * @var array
     */
    private $where;

    /**
     * 
     * @return int
     */
    public function getMaxOrder()
    {
        $query = $this->getEntityManager()
                ->createQueryBuilder()
                ->select("MAX(l.ordem)")
                ->from("Entity\\LogoRodape", "l");

        return $query->getQuery()->getSingleScalarResult();
    }

    /**
     * Cria a Query que retorna os Logos
     * 
     * @return \Doctrine\ORM\QueryBuilder
     */
    private function getQueryLogos()
    {
        return $this->getEntityManager()->createQueryBuilder()->select("l")->from("Entity\LogoRodape", "l");
    }

    /**
     * 
     * @param int $limit
     * @param int $offset
     * @param array $filtro
     * @return array
     */
    public function getLogos($limit, $offset, array $filtro = array())
    {
        $query = $this->getQueryLogos();

        $query = $this->setWhere($query, $filtro);
        $query->orderBy('l.dataCadastro', 'DESC');
        $query->setMaxResults($limit);
        $query->setFirstResult($offset);

        return $query->getQuery()->getResult();
    }

    /**
     * 
     * @param \Doctrine\ORM\QueryBuilder $query
     * @param array $where
     * @return \Doctrine\ORM\QueryBuilder
     */
    private function setWhere($query, array $where = array())
    {
        if (empty($this->where)) {
            $this->where = $where;
        }

        $busca = strtolower($this->where['busca']);
        $status = $this->where['status'];
        $data_inicial = $this->where['data_inicial'];
        $data_final = $this->where['data_final'];

        if (!empty($busca)) {
            $query->andWhere(
                    $this->getEntityManager()->createQueryBuilder()
                            ->orWhere("LOWER(l.nome) LIKE '%{$busca}%' ")
                            ->orWhere("LOWER(l.link) LIKE '%{$busca}%' ")
                            ->getDQLPart("where")
            );
        }

        if ($status !== "") {
            $query->andWhere("l.publicado=" . $status);
        }


        if (!empty($data_inicial) && !empty($data_final)) {
            $query->andWhere("l.dataCadastro BETWEEN '{$data_inicial} 00:00'  AND '{$data_final} 23:59' ");
        }

        return $query;
    }

    /**
     * Número total de logos com filtro
     * 
     * @return int
     */
    public function getMaxResult()
    {
        $query = $this->getQueryLogos();

        $query->select("count(l)");

        $query = $this->setWhere($query);

        return $query->getQuery()->getSingleScalarResult();
    }

    /**
     * Numero total de Logos do Banco
     * 
     * @return int
     */
    public function countAll()
    {
        $sql = "SELECT count(l.id) FROM Entity\\LogoRodape l";

        $query = $this->getEntityManager()->createQuery($sql);

        return $query->getSingleScalarResult();
    }

    /**
     * 
     * @param String $ids
     */
    public function getLogoPublicacao(array $ids)
    {
        //Monta a dql
        $dql = "SELECT l.id  FROM Entity\LogoRodape l WHERE l.id IN(" . implode(',', $ids) . ") AND CURRENT_TIMESTAMP() >= l.dataInicial";
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
    public function verificaPeriodoLogo(array $ids)
    {
        //Monta a dql
        $dql = "SELECT count(l) total FROM Entity\LogoRodape l WHERE l.id IN(" . implode(',', $ids) . ") AND CURRENT_TIMESTAMP() >= l.dataInicial";
        $query = $this->getEntityManager()->createQuery($dql);
        $result = $query->getResult();

        if (count($ids) == $result[0]['total'])
            return true;
        else
            return false;
    }

}
