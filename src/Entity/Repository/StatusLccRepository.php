<?php

namespace Entity\Repository;

use \Doctrine\ORM\EntityRepository;

/**
 * Description of StatusLccRepository
 *
 * @author Join
 */
class StatusLccRepository extends EntityRepository
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
     * @param String $nome
     * @return boolean
     */
    public function verificaNomeStatus($nome, $id = 0)
    {
        //Monta a dql, executa e verifica se algum registro foi encontrado
        $dql = "SELECT count(st) total FROM Entity\StatusLcc st WHERE st.nome = :nome ";
        
        if(!empty($id)) {
            
            $dql .= ' AND st.id <> '.$id;
        }
        $query = $this->getEntityManager()->createQuery($dql);
        $query->setParameters(array('nome' => $nome));
        $result = $query->getResult();

        return $result[0]['total'] == 0 ? true : false;
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
                ->update('Entity\StatusLcc', 'c')
                ->set('c.ordem', $val)
                ->where('c.id = '.$i)
                ->getQuery()
                ->execute();
        }
    }
}
