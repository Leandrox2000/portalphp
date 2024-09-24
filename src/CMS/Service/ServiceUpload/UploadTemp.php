<?php

namespace CMS\Service\ServiceUpload;

use Helpers\Upload;

/**
 * Description of UploadTemp
 *
 * @author Luciano
 */
class UploadTemp
{

    private $fileField;
    private $isImage = FALSE;
    private $width;
    private $height;
    private $extensions;
    private $maxSize;
    private $directory;
    private $upload;
    private $encryptName = false;

    public function __construct()
    {
        
    }

    /**
     * 
     * @return boolean
     */
    public function getEncryptName()
    {
        return $this->encryptName;
    }

    /**
     * 
     * @param boolean $encryptName
     */
    public function setEncryptName($encryptName)
    {
        $this->encryptName = $encryptName;
    }

    /**
     * 
     * @return Upload
     */
    private function getUpload()
    {
        if (empty($this->upload)) {
            $this->setUpload(new Upload());
        }
        return $this->upload;
    }

    public function getFileField()
    {
        return $this->fileField;
    }

    /**
     * 
     * @param type $extensions
     * @return \CMS\Service\ServiceUpload\UploadTemp
     */
    public function setExtensions($extensions)
    {
        $this->extensions = $extensions;

        return $this;
    }

    public function getExtensions()
    {
        return $this->extensions;
    }

    public function getMaxSize()
    {
        return $this->maxSize;
    }

    public function getDirectory()
    {
        return $this->directory;
    }

    /**
     * 
     * @param string $directory
     * @return \CMS\Service\ServiceUpload\UploadTemp
     */
    public function setDirectory($directory)
    {
        $this->directory = $directory;

        return $this;
    }

    /**
     * 
     * @param Upload $upload
     */
    public function setUpload($upload)
    {
        $this->upload = $upload;
    }

    /**
     * 
     * @param type $fileField
     * @return \CMS\Service\ServiceUpload\UploadTemp
     */
    public function setFileField($fileField)
    {
        $this->fileField = $fileField;

        return $this;
    }

    /**
     * 
     * @param type $maxSize
     * @return \CMS\Service\ServiceUpload\UploadTemp
     */
    public function setMaxSize($maxSize)
    {
        $this->maxSize = $maxSize;

        return $this;
    }

    /**
     * 
     * @return type
     */
    public function upload()
    {
        $upload = $this->getUpload();
        $upload->setFieldFile($this->getFileField());
        $upload->setDiretory($this->getDirectory());
        $upload->setExtensions($this->getExtensions());
        $upload->setMaxSize($this->getMaxSize());
        $upload->setEncryptName($this->getEncryptName());
        $upload->set();
        if ($upload->isArray()) {
            for ($i = 0; $i < $upload->getQuant(); $i++) {
                $upload->setFileName($i);
            }
        } else {
            $upload->setFileName();
        }


        try {
            $retorno = $upload->upload();
        } catch (\Exception $e) {
            $retorno['error'][] = $e->getMessage();
        }

        return $retorno;
    }

    /**
     * 
     * @param string $file
     * @return bool
     */
    public function remove($file)
    {
        return $this->getUpload()->remove($this->getDirectory() . $file);
    }

}
