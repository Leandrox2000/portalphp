<?php

namespace CMS\Service\ServiceUpload;

use Helpers\Upload;
use Canvas\Canvas;

abstract class AbstractUpload {

    /**
     *
     * @var string
     */
    private $file;

    /**
     *
     * @var Upload
     */
    private $upload;
    
    /**
     *
     * @var Canvas
     */
    private $canvas;

    /**
     * 
     * @param string $file
     */
    function __construct($file = "") {
        $this->setFile($file);
    }

    /**
     * 
     * @return string
     */
    public function getFile() {
        return $this->file;
    }

    /**
     * 
     * @return Upload
     */
    public function getUpload() {
        if (empty($this->upload)) {
            $this->setUpload(new Upload());
        }
        return $this->upload;
    }
    
    /**
     * 
     * @return Canvas
     */
    public function getCanvas()
    {
        if (empty($this->canvas)) {
            $this->setCanvas(Canvas::Instance());
        }
        
        return $this->canvas;
    }

    /**
     * 
     * @param \Canvas\Canvas $canvas
     */
    public function setCanvas(Canvas $canvas)
    {
        $this->canvas = $canvas;
    }

    
    /**
     * 
     * @param string $file
     */
    public function setFile($file) {
        $this->file = $file;
    }

    /**
     * 
     * @param Upload $upload
     */
    public function setUpload(Upload $upload) {
        $this->upload = $upload;
    }

    /**
     * 
     * @return bool
     */
    public function delete() {
        if (!$this->getUpload()->remove($this->getFile())) {
            throw new \Exception("Não conseguiu remover o arquivo " . $this->getFile());
        }
    }

    /**
     * 
     * @param string $newLocate
     * @return bool
     */
    public function rename($newLocate) {
        if (!$this->getUpload()->rename($this->getFile(), $newLocate)) {
            throw new \Exception("Não conseguiu renomear o arquivo");
        }
    }

    /**
     * 
     * @param String $file
     * @return boolean
     */
    public function fileExist($file) {
        return $this->getUpload()->fileExists($file);
    }

    /**
     * 
     * @param string $location
     * @param string $name
     */
    public function createDir($location, $name) {
        if (!$this->fileExist($location . $name)) {
            mkdir($location . $name, 0777);
        }
    }

    /**
     * 
     * @param string $oldName
     * @param string $newName
     */
    public function renameDir($oldName, $newName) {
        rename($oldName, $newName);
    }

    /**
     * 
     * @param string $dir
     */
    public function removeDir($dir) {
        rmdir($dir);
    }

    /**
     * 
     * @param string $old
     * @param string $new
     */
    public function cut($old, $new) {
        if ($this->fileExist($old)) {
            copy($old, $new);
            unlink($old);
        }
    }

}
