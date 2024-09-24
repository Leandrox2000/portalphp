<?php
namespace Entity\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * Description of VinculoRepository
 *
 * @author Join
 */
class VinculoRepository extends EntityRepository
{

    /**
     * Retorna todos os Vinculos
     * 
     * @return array
     */
    public function getVinculos()
    {
        $query = $this->getEntityManager()
                ->createQueryBuilder()
                ->select("V")
                ->from($this->getEntityName(), "V")
                ->orderBy("V.nome");

        return $query->getQuery()->getResult();
    }

    /**
     * 
     * @param array $findBy
     * @return boolean
     */
    public function verificaVinculoExiste(array $findBy)
    {
        $query = $this->getEntityManager()
                ->createQueryBuilder();

        $query->select("V.id")
                ->from($this->getEntityName(), "V")
                ->andWhere($query->expr()->eq("V.nome", ":nome"))
                ->andWhere($query->expr()->notIn("V.id", ":id"));

        $query->setParameter("nome", $findBy['nome']);
        $query->setParameter("id", $findBy['id']);

        $result = $query->getQuery()->getResult();
        return empty($result);
    }

}