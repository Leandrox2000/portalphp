<?php
namespace CMS\Service\ServiceRepository;

use Doctrine\ORM\EntityManager;
use Entity\ArquivoLcc as ArquivoEntity;
use Helpers\Session;

/**
 * Description of Arquivo
 *
 * @author Join
 */
class ArquivoLcc extends BaseService
{
    /**
     * 
     * @param \Doctrine\ORM\EntityManager $em
     * @param ArquivoEntity $entity
     */
    public function __construct(EntityManager $em, ArquivoEntity $entity, Session $session)
    {
        parent::__construct($em);
        $this->setEntity($entity);
        $this->setSession($session);
    }

    
}
