<?php

namespace Entity\Repository;

use \Doctrine\ORM\EntityRepository;

/**
 * Description of CargoRepository
 *
 * @author Join
 */
class CargoRepository extends EntityRepository
{
    /**
     * Retorna todos os Cargos
     * 
     * @return array
     */
    public function getCargos()
    {
        $query = $this->getEntityManager()
                        ->createQueryBuilder()
                        ->select("C")
                        ->from($this->getEntityName(), "C")
                        ->orderBy("C.cargo");

        
        return $query->getQuery()->getResult();
    }

    /**
     * 
     * @param array $findBy
     * @return boolean
     */
    public function verificaCargoExiste(array $findBy)
    {
        $query = $this->getEntityManager()
                        ->createQueryBuilder();
        
        $query->select("C.id")
        ->from($this->getEntityName(), "C")
        ->andWhere($query->expr()->eq("C.cargo", ":cargo"))
        ->andWhere($query->expr()->notIn("C.id", ":id"));
        
        $query->setParameter("cargo", $findBy['cargo']);
        $query->setParameter("id", $findBy['id']);
        
        $result = $query->getQuery()->getResult();
        return empty($result);
    }

    
    
}
