<?php

namespace Entity\Repository;

use \Doctrine\ORM\EntityRepository;

/**
 * PerguntaCategoriaRepository
 *
 * @author Luciano
 */
class PerguntaCategoriaRepository extends EntityRepository
{

    /**
     * Retorna todas as Categorias
     * 
     * @return array
     */
    public function getCategorias()
    {
        $query = $this->getEntityManager()
                        ->createQueryBuilder()
                        ->select("C")
                        ->from($this->getEntityName(), "C")
                        ->orderBy("C.categoria")
                ;

        
        return $query->getQuery()->getResult();
    }

    /**
     * 
     * @param array $findBy
     * @return boolean
     */
    public function verificaCategoriaExiste(array $findBy)
    {
        $query = $this->getEntityManager()
                        ->createQueryBuilder();
        
        $query->select("C.id")
        ->from($this->getEntityName(), "C")
        ->andWhere($query->expr()->eq("C.categoria", ":categoria"))
        ->andWhere($query->expr()->notIn("C.id", ":id"));
        $query->setParameter("categoria", $findBy['categoria']);
        $query->setParameter("id", $findBy['id']);
        
        $result = $query->getQuery()->getResult();
        return empty($result);
    }
    
}
