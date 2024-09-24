<?php
namespace CMS\Controller;

use LibraryController\AbstractController;

/**
 * Description of IndexController
 * 
 * @author Luciano
 */
class IndexController extends AbstractController
{

    const PAGE_TITLE = "Bem vindo ao CMS do Portal do IPHAN!";
    const DEFAULT_ACTION = "index";

    protected $title = self::PAGE_TITLE;
    protected $defaultAction = self::DEFAULT_ACTION;

    /**
     * 
     * @param string $title
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }

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
    
    public function index(){
        $this->tpl->renderView(array("titlePage" => $this->getTitle()));        
        return $this->tpl->output();
    }
 

}
