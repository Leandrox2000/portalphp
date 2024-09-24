<?php
namespace Entity\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * Description of VideoSiteRepository
 *
 * @author Henry
 */
class AgendaSiteRepository extends BaseRepository
{
    public function getRegisterByAgenda($idagenda)
    {
        
        $query = $this->createQueryBuilder('v')
                        ->where('v.id = :id')
                        ->setParameter('id', $idagenda);
        
        $retorno = $query->getQuery()->getResult();
        
        return $retorno;
    }
}
