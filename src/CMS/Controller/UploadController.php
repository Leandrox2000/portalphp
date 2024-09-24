<?php

namespace CMS\Controller;


use LibraryController\AbstractController;
use CMS\Service\ServiceUpload\UploadTemp;

/**
 * Description of Upload
 *
 * @author Luciano
 */
class UploadController extends AbstractController
{
    
    const PAGE_TITLE            = "";
    const DEFAULT_ACTION        = "adicionar";
    
    protected $title            = self::PAGE_TITLE;
    protected $defaultAction    = self::DEFAULT_ACTION;

    /**
     *
     * @var UploadTemp
     */
    private $service;


    public function getDefaultAction()
    {
        return $this->defaultAction;
    }

    public function getTitle()
    {
        return $this->title;
    }

    /**
     * 
     * @return UploadTemp
     */
    public function getService()
    {
        if (is_null($this->service)) {
            $this->setService(new UploadTemp());
        }
        return $this->service;
    }

    /**
     * 
     * @param \CMS\Service\ServiceUpload\UploadTemp $service
     */
    public function setService(UploadTemp $service)
    {
        $this->service = $service;
    }

        
    public function adicionar($encryptName = false)
    {
        $encryptName = $encryptName == 1 ? true : false;
        
        $upload = $this->getService();
        $upload->setFileField($this->getParam()->get("field"))
                ->setExtensions($this->getParam()->get("extensions"))
                ->setMaxSize($this->getParam()->getInt("maxSize"))
                ->setDirectory(getcwd()."/uploads/temp/")
                ->setEncryptName($encryptName);

            
        
        $result = $upload->upload();
        
        return json_encode($result);
        

    }
    
    public function remover($file)
    {
        $upload = $this->getService();
        
        $upload->setDirectory(getcwd()."/uploads/temp/");
        $upload->remove($file);
        
        return json_encode(array($file));
        
    }
            
    
    
}
