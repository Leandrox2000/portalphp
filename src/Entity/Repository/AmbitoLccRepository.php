<?php

namespace Entity\Repository;

use \Doctrine\ORM\EntityRepository;

/**
 * Description of AmbitoLccRepository
 *
 * @author Join
 */
class AmbitoLccRepository extends EntityRepository
{

    /**
     *
     * @param String $nome
     * @return boolean
     */
    public function verificaNomeAmbito($nome, $id)
    {
        //Monta a dql, executa e verifica se algum registro foi encontrado
        $dql = "SELECT count(amb) total FROM Entity\AmbitoLcc amb WHERE amb.nome = :nome AND amb.id != :id ";
        $query = $this->getEntityManager()->createQuery($dql);
        $query->setParameters(array('nome' => $nome, 'id'=>$id));
        $result = $query->getResult();

        return $result[0]['total'] == 0 ? true : false;
    }

}
