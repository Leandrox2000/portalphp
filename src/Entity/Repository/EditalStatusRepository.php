<?php

namespace Entity\Repository;


/**
 * Description of EditalStatusRepository
 *
 * @author Luciano
 */
class EditalStatusRepository extends BaseRepository
{

    const ORDER_DEFAULT = 1;
    const ORDER_FINISHED = 2;
    
    public function getColumnLabel($int = 0){
        
        if(intval($int) > 0) {
            
            switch($int){
                
                case self::ORDER_DEFAULT:
                    return 'Padrão';
                    break;
                
                case self::ORDER_FINISHED:
                    return 'Data de despublicação';
                    break;
            }
        }
        else {
            
            return array(
                
                self::ORDER_DEFAULT => 'Padrão',
                self::ORDER_FINISHED => 'Data de despublicação',
            );
        }
    }
    
    /**
     *
     * @return array
     */
    public function getStatus()
    {
        $query = $this->getEntityManager()
                        ->createQueryBuilder()
                        ->select("C")
                        ->from($this->getEntityName(), "C")
                        ->orderBy('C.nome', 'ASC');

        return $query->getQuery()->getResult();
    }


    /**
     *
     * @param array $findBy
     * @return boolean
     */
    public function verificaStatusExiste(array $findBy)
    {
        $query = $this->getEntityManager()
                        ->createQueryBuilder();

        $query->select("C.id")
                ->from($this->getEntityName(), "C")
                ->andWhere($query->expr()->eq("C.nome", ":status"))
                ->andWhere($query->expr()->notIn("C.id", ":id"));

        $query->setParameter("status", $findBy['nome']);
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
                ->update('Entity\EditalStatus', 'p')
                ->set('p.ordem', $val)
                ->where('p.id = '.$i)
                ->getQuery()
                ->execute();
        }
    }
}
