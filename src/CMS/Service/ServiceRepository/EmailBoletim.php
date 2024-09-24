<?php

namespace CMS\Service\ServiceRepository;

use \Doctrine\ORM\EntityManager as EntityManager;
use \Entity\EmailBoletim as EmailBoletimEntity;
use Helpers\Session;

/**
 * EmailBoletim
 *
 * @author join-ti
 */
class EmailBoletim extends BaseService
{

    /**
     * 
     * @param \Doctrine\ORM\EntityManager $em
     * @param string $entity
     */
    public function __construct(EntityManager $em, EmailBoletimEntity $entity, Session $session)
    {
        parent::__construct($em);
        $this->setEntity($entity);
        $this->setSession($session);
    }

}
