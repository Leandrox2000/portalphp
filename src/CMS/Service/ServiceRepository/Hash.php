<?php

namespace CMS\Service\ServiceRepository;

use Doctrine\ORM\EntityManager;
use Entity\Hash as HashEntity;

/**
 * Description of Jointi
 *
 * @author Luciano
 */
class Hash extends BaseService
{

    /**
     * 
     * @param \Doctrine\ORM\EntityManager $em
     * @param \Entity\Ha $entity
     */
    public function __construct(EntityManager $em, HashEntity $entity, \Helpers\Session $session)
    {
        parent::__construct($em);
        $this->setEntity($entity);
        $this->setSession($session);
    }

    /**
     * 
     * @param string $hash
     * @return boolean
     */
    public function save($hash)
    {
        try {
            parent::save(array('value' => $hash));
            return true;
        } catch (\Exception $exc) {
            return false;
        }
    }

    /**
     * 
     * @param type $hash
     * @return boolean
     */
    public function deleteHash($hash)
    {
        try {
            $this->getEm()->createQuery("DELETE FROM Entity\Hash h WHERE h.value = '{$hash}' ")->execute();
            
            return true;
        } catch (\Exception $exc) {
            return false;
        }
    }

}
