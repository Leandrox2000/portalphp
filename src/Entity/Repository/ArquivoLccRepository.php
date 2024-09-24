<?php

namespace Entity\Repository;

use \Doctrine\ORM\EntityRepository;

/**
 * Description of AmbitoLccRepository
 *
 * @author Join
 */
class ArquivoLccRepository extends EntityRepository
{

    /**
     * 
     * @param array $ids
     * @return array
     */
    public function getArquivosLcc($ids)
    {
        //Monta a dql, executa e verifica se algum registro foi encontrado
        $dql = "SELECT arq.nome FROM Entity\ArquivoLcc arq WHERE arq.id IN(".implode(',', $ids).") ";
        $query = $this->getEntityManager()->createQuery($dql);
        return $query->getResult();

    }
    
    
    /**
     * 
     * @param array $id
     * @return array
     */
    public function getArquivosLccIds($ids)
    {
        //Monta a dql, executa e verifica se algum registro foi encontrado
        $dql = "SELECT arq FROM Entity\ArquivoLcc arq JOIN arq.licitacaoConvenioContrato lcc WHERE lcc.id IN(".implode(',', $ids).") ";
        $query = $this->getEntityManager()->createQuery($dql);
        return $query->getResult();
    }

}
