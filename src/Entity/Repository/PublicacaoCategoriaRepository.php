<?php

namespace Entity\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * Description of PublicacaoCategoriaRepository
 *
 * @author Luciano
 */
class PublicacaoCategoriaRepository extends EntityRepository
{
    
    public function getCategorias()
    {
        $query = $this->getEntityManager()
                        ->createQueryBuilder()
                        ->select("C")
                        ->from($this->getEntityName(), "C")
                        ->orderBy('C.ordem', 'ASC');
        /*
        $query = $this->getEntityManager()
                        ->createQueryBuilder()
                        ->select("C")
                        ->from($this->getEntityName(), "C")
                        ->orderBy('C.nome', 'ASC');*/
        
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
                ->andWhere($query->expr()->eq("C.nome", ":categoria"))
                ->andWhere($query->expr()->notIn("C.id", ":id"));
        
        $query->setParameter("categoria", $findBy['nome']);
        $query->setParameter("id", $findBy['id']);
        
        $result = $query->getQuery()->getResult();
        return empty($result);
    }

    /**
     * Seta posição
     *
     * @return boolean
     */
    public function setOrdem($array)
    {
        foreach($array as $i => $val){
            $this->getEntityManager()
                ->createQueryBuilder()
                ->update('Entity\PublicacaoCategoria', 'p')
                ->set('p.ordem', $val)
                ->where('p.id = '.$i)
                ->getQuery()
                ->execute();
        }
    }
}
