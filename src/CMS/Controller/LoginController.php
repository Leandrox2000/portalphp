<?php

namespace CMS\Controller;


use LibraryController\AbstractController;
use CMS\Service\ServiceUser;

/**
 * Description of LoginController
 *
 * @author Luciano
 */
class LoginController extends AbstractController
{
    
    private $defaultAction = "index";
    private $title;

    /**
     * 
     * @return string
     */
    public function getDefaultAction()
    {
        return $this->defaultAction;
    }

    /**
     * 
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }
    
    /**
     * 
     * @return \Template\TemplateAmanda
     */
    public function index()
    {
        $this->getTpl()->setLogin();
        return $this->getTpl();
    }
    
    /**
     * 
     * @return String
     */
    public function autenticar()
    {
        //Instancia a classe USER
        $login      = new \Security\User($this->getSession());
        $retorno    = array();
        
        //Faz a 
        if ($login->verifyUser($this->getParam()->get("username"), $this->getParam()->get("password"))) {
            //$login->setUser();
            $retorno = array('logado'=>true, 'page'=>'index/');
        } else {
            $retorno = array('error' => $login->getError());
        }
        
        return json_encode($retorno);
    }
    
    public function alterarSite($idSite)
    {
        $user = $this->getSession()->get("user");
        $site = $this->getEm()->find("Entity\Site", $idSite);
        $user->setSite($site);
        $this->getSession()->set("user", $user);
    }
    
    public function logoff(){
        if($this->getSession()->get('user')){
            $this->getSession()->destroy('user');
        }
        
        \Helpers\Http::redirect("/login/index");
    }
}
