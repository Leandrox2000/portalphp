<?php

 namespace CMS\Service\ServiceUser;

 use Doctrine\ORM\EntityManager;
 use Entity\Usuario;
 use Factory\EntityManagerFactory as EM;
 
/**
 * Description of User
 *
 * @author Luciano
 */
class User
{
    /**
     *
     * @var \Doctrine\ORM\EntityManager
     */
    private $em;
    
    public function __construct(EntityManager $em = null)
    {
        if ($em === null) {
            $this->setEm(EM::getEntityManger());
        } else {
            $this->setEm($em);
        }
        
    }
    
    /**
     * 
     * @return \Doctrine\ORM\EntityManager
     */
    public function getEm()
    {
        return $this->em;
    }

    /**
     * 
     * @param \Doctrine\ORM\EntityManager $em
     */
    public function setEm(EntityManager $em)
    {
        $this->em = $em;
    }

    /**
     * Retorna  o Usuário carregado
     * 
     * @return \Entity\Usuario
     */
    public function getUser($id=0)
    {
        $user = new Usuario();
        $user->setNome("usuario");
        $site = $this->getEm()->getRepository("Entity\Site")->findOneBy(array('sede'=>1));
        $user->setSite($site);
        
        return $user;
    }
    
    
}
