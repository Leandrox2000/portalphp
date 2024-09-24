<?php

namespace Entity\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * Description of AgendaDirecaoSiteRepository
 */
class AgendaDirecaoSiteRepository extends EntityRepository {
    
    /**
     * Atualiza a ordenação dos registros.
     * 
     * @param array $novaOrdenacao
     * @param integer $idSite
     */
    public function setOrdem($novaOrdenacao, $idSite) {
        foreach($novaOrdenacao as $idAgendaDirecao => $ordem){
            $this->getEntityManager()
                ->createQueryBuilder()
                ->update('Entity\AgendaDirecaoSite', 'a')
                ->set('a.ordem', $ordem)
                ->where('a.agendaDirecao = ' . $idAgendaDirecao)
                ->andWhere('a.site = ' . $idSite)
                ->getQuery()
                ->execute();
        }
    }
}
