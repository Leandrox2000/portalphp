<?php

namespace CMS\Controller;


use LibraryController\AbstractController;

/**
 * Description of AjudaController
 *
 * @author Luciano
 */
class AjudaController extends AbstractController
{
    
    const PAGE_TITLE = "Ajuda do Sistema";
    const DEFAULT_ACTION = "index";

    /**
     * @var String 
     */
    protected $title = self::PAGE_TITLE;

    /**
     * @var String 
     */
    protected $defaultAction = self::DEFAULT_ACTION;

    
    /**
     * 
     * @return String
     */
    public function getDefaultAction()
    {
        return $this->defaultAction;
    }

    /**
     * 
     * @return String
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
        return $this->getTpl()->renderView(array());
    }
}
