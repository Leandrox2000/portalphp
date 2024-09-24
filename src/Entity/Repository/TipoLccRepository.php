<?php

namespace Entity\Repository;

use \Doctrine\ORM\EntityRepository;

/**
 * Description of TipoLccRepository
 *
 * @author Join
 */
class TipoLccRepository extends EntityRepository
{

    /**
     * 
     * @param String $nome
     * @return boolean
     */
    public function verificaNomeTipo($nome)
    {
        //Monta a dql, executa e verifica se algum registro foi encontrado
        $dql = "SELECT count(tip) total FROM Entity\TipoLcc tip WHERE tip.nome = :nome ";
        $query = $this->getEntityManager()->createQuery($dql);
        $query->setParameters(array('nome' => $nome));
        $result = $query->getResult();

        return $result[0]['total'] == 0 ? true : false;
    }

    
    public function getTipos()
    {
        $query = $this->getEntityManager()
                        ->createQueryBuilder()
                        ->select("tip")
                        ->from($this->getEntityName(), "tip")
                        ->orderBy('tip.nome', 'ASC');

        return $query->getQuery()->getResult();
    }
    
}
