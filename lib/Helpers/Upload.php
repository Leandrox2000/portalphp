<?php

namespace Helpers;

class Upload
{

    private $file;
    private $fieldFile;
    private $name;
    private $maxSize;
    private $extensions;
    private $fileExtension;
    private $fileSize;
    private $quant;
    private $isArray;
    private $diretory;
    private $fileInfo;
    private $isImage;
    private $width;
    private $height;
    private $encryptName = false;

    public function __construct($fieldFile = "")
    {
        if (!empty($fieldFile)) {
            $this->setFieldFile($fieldFile);
        }
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

    public function set()
    {
        $file = $this->getFile();

        if (is_array($file['name'])) {

            $this->setIsArray(TRUE);
            $this->setQuant(count($file['name']));

            foreach ($file['name'] as $key => $value) {
                $this->setFileInfo($this->getPathInfo($file['name'][$key]));
                $this->setFileSize($file['size'][$key]);
            }
        } else {

            $this->setIsArray(FALSE);
            $this->setFileInfo($this->getPathInfo($file['name']));
            $this->setFileSize($file['size']);
        }
    }

    /**
     *
     * @param string $fileName
     * @return pathinfo()
     */
    private function getPathInfo($fileName)
    {
        return pathinfo($fileName);
        ;
    }

    /**
     *
     * @return string
     */
    private function getFieldFile()
    {
        return $this->fieldFile;
    }

    /**
     *
     *
     * @return $_FILES
     * @throws Exception
     */
    public function getFile()
    {
        if (empty($this->fieldFile)) {
            throw new \Exception("nome do campo de arquivo deve ser informado");
        }
        if (empty($this->file)) {
            $this->setFile($this->fieldFile);
        }

        return $this->file;
    }

    /**
     *
     * @return int
     */
    public function getQuant()
    {
        return $this->quant;
    }

    /**
     *
     * @return array
     */
    private function getFileInfo()
    {
        return $this->fileInfo;
    }

    /**
     * Retorna o nome do arquivo que foi Enviado
     *
     * @param int $index
     * @return string
     */
    public function getRealName($index)
    {
        $file = $this->getFile();
        if ($this->isArray()) {
            return $file['name'][$index];
        }
        return $file['name'];
    }

    /**
     *
     * @param int $i
     * @return int
     */
    private function getSize($i = 0)
    {
        if ($this->isArray()) {
            return $this->fileSize[$i];
        }

        return $this->fileSize;
    }

    /**
     *
     * @param int $i
     * @return string
     */
    public function getExtension($i = 0)
    {
        if ($this->isArray()) {
            return $this->fileExtension[$i];
        }
        return strtolower($this->fileExtension);
    }

    public function getFileName($index = 0)
    {
        if ($this->isArray()) {
            return $this->name[$index];
        }
        return $this->name;
    }

    /**
     *
     * @return string
     */
    private function getDiretory()
    {
        return $this->diretory;
    }

    /**
     *
     * @return int
     */
    private function getMaxSize()
    {
        return $this->maxSize;
    }

    /**
     *
     * @param string $fileInfo
     */
    private function setFileInfo($fileInfo)
    {
        if ($this->isArray()) {
            $this->fileInfo[] = $fileInfo;
        } else {
            $this->fileInfo = $fileInfo;
        }
        $this->setFileExtension($fileInfo['extension']);
    }

    /**
     *
     * @param int $size
     */
    public function setFileSize($size)
    {
        if ($this->isArray()) {
            $this->fileSize[] = $size;
        } else {
            $this->fileSize = $size;
        }
    }

    /**
     *
     * @param int $size
     */
    public function setFileExtension($extension)
    {
        if ($this->isArray()) {
            $this->fileExtension[] = $extension;
        } else {
            $this->fileExtension = $extension;
        }
    }

    /**
     *
     * @param string $fieldFile
     * @throws Exception
     */
    public function setFieldFile($fieldFile)
    {
        if (empty($_FILES[$fieldFile])) {
            throw new \Exception("Nenhum arquivo enviado");
        }
        $this->fieldFile = $fieldFile;
    }

    /**
     * Seta a variavel de arquivo
     *
     */
    public function setFile()
    {
        $this->file = $_FILES[$this->getFieldFile()];
    }

    /**
     *
     * @param boolean $isArray
     */
    public function setIsArray($isArray)
    {
        $this->isArray = $isArray;
    }

    /**
     *
     * @param int $quant
     */
    public function setQuant($quant)
    {
        $this->quant = $quant;
    }

    /**
     *
     * @return boolean
     */
    public function isArray()
    {
        return $this->isArray;
    }

    /**
     *
     * @param int $i
     */
    public function setFileName($i = 0)
    {

        if ($this->isArray()) {
            if ($this->encryptName) {
                $this->name[$i] = $this->generateName($this->file['name'][$i]);
            } else {
                $this->name[$i] = $this->trataNome($this->file['name'][$i]);
            }
        } else {
            if ($this->encryptName) {
                $this->name = $this->generateName($this->file['name']);
            } else {
                $this->name = $this->trataNome($this->file['name']);
            }
        }
    }

    /**
     *
     * @param int $size
     */
    public function setMaxSize($size)
    {
        $this->maxSize = $size;
    }

    /**
     *
     * @param array $extension
     */
    public function setExtensions($extension)
    {
        if (!is_array($extension)) {
            if (empty($extension)) {
                $extension = array();
            } else {
                $extension = explode(",", $extension);
            }
        }
        $this->extensions = $extension;
    }

    /**
     *
     * @param string $directory
     */
    public function setDiretory($directory)
    {
        $this->diretory = rtrim($directory, '/');
    }

    /**
     *
     * @param int $index
     * @return string
     */
    private function getTmpName($index = 0)
    {
        $file = $this->getFile();
        if ($this->isArray()) {
            return $file['tmp_name'][$index];
        }
        return $file['tmp_name'];
    }

    /**
     * Retorna o Caminhodo Arquivo
     *
     * @param int $index
     * @return string
     */
    public function getFinalPath($index = 0)
    {
        return $this->getDiretory() . "/" . $this->getFileName($index);
    }

    /**
     *
     * @param int $index
     * @return boolena
     */
    private function testExtension($index = 0)
    {
        return !empty($this->extensions) ? in_array($this->getExtension($index), $this->extensions) : TRUE;
    }

    /**
     *
     * @param int $index
     * @return boolena
     */
    private function testSize($index = 0)
    {
        return $this->getSize($index) >= $this->getMaxSize();
    }

    /**
     * Move o arquivo da memória para a pasta temporária
     *
     * @param string $filename
     * @param string $destination
     * @return boolean
     */
    private function moveUploadFile($filename, $destination)
    {
        return move_uploaded_file($filename, $destination);
    }

    public function upload()
    {
        $dados = array();
        if ($this->isArray()) {
            for ($index = 0; $index < $this->getQuant(); $index++) {
                $dados[$index] = $this->makeUpload($index);
            }
        } else {
            $dados[0] = $this->makeUpload();
        }
        return $dados;
    }

    /**
     *
     * @param type $index
     * @return type
     */
    private function makeUpload($index = 0)
    {
        $retorno['error'] = array();
        if (!$this->testExtension($index)) {
            $retorno['error'][] = "Formato do arquivo {$this->getRealName($index)} não é válido.";
        } elseif (!$this->testSize($index)) {
            $retorno['error'][] = "Arquivo {$this->getRealName($index)} é muito grande.";
        } elseif ($this->moveUploadFile($this->getTmpName($index), $this->getFinalPath($index))) {
            $retorno['temp_name'] = str_replace("." . $this->getExtension($index), "", $this->getFileName($index));
            $retorno['extensao'] = $this->getExtension($index);
//            $retorno['real_name'] = $this->getRealName($index);
            $retorno['real_name'] = basename($this->getFinalPath($index));
        }

        return $retorno;
    }

    /**
     *
     * @param string $fileLocate
     * @param string $newLocate
     * @return bool
     */
    public function rename($fileLocate, $newLocate)
    {
        return rename($fileLocate, $newLocate);
    }

    /**
     * Remove o arquivo
     *
     * @param string $filename
     * @return boolean
     */
    public function remove($filename)
    {
        if ($this->fileExists($filename)) {
            return unlink($filename);
        }
        return true;
    }

    public function fileExists($filename)
    {
        return file_exists($filename);
    }

    public static function testaNome($arquivo, $diretorio)
    {
        $arrayExtensao = explode('.', $arquivo);
        $extensao = '.' . end($arrayExtensao);
        $nomeTeste = substr_replace($arquivo, '', strlen($extensao) * (-1));
        $nome = $nomeTeste;
        $diretorio = rtrim($diretorio, '/') . '/';
        $cont = 1;
        while (file_exists($diretorio . $nomeTeste . $extensao)) {
            $cont++;
            $nomeTeste = $nome . "(" . $cont . ")";
        }
        return $nomeTeste . $extensao;
    }

    /**
     *
     * @param string $string
     * @return string
     */
    private function trataNome($string)
    {
        // Remove special accented characters - ie. sí.
        $cleanName = strtr($string, array(
            'Š' => 'S', 'Ž' => 'Z', 'š' => 's', 'ž' => 'z', 'Ÿ' => 'Y',
            'À' => 'A', 'Á' => 'A', 'Â' => 'A', 'Ã' => 'A', 'Ä' => 'A',
            'Å' => 'A', 'Ç' => 'C', 'È' => 'E', 'É' => 'E', 'Ê' => 'E',
            'Ë' => 'E', 'Ì' => 'I', 'Í' => 'I', 'Î' => 'I', 'Ï' => 'I',
            'Ñ' => 'N', 'Ò' => 'O', 'Ó' => 'O', 'Ô' => 'O', 'Õ' => 'O',
            'Ö' => 'O', 'Ø' => 'O', 'Ù' => 'U', 'Ú' => 'U', 'Û' => 'U',
            'Ü' => 'U', 'Ý' => 'Y', 'à' => 'a', 'á' => 'a', 'â' => 'a',
            'ã' => 'a', 'ä' => 'a', 'å' => 'a', 'ç' => 'c', 'è' => 'e',
            'é' => 'e', 'ê' => 'e', 'ë' => 'e', 'ì' => 'i', 'í' => 'i',
            'î' => 'i', 'ï' => 'i', 'ñ' => 'n', 'ò' => 'o', 'ó' => 'o',
            'ô' => 'o', 'õ' => 'o', 'ö' => 'o', 'ø' => 'o', 'ù' => 'u',
            'ú' => 'u', 'û' => 'u', 'ü' => 'u', 'ý' => 'y', 'ÿ' => 'y'
        ));
        $cleanName = strtr($cleanName, array(
            'Þ' => 'TH', 'þ' => 'th', 'Ð' => 'DH', 'ð' => 'dh', 'ß' => 'ss',
            'Œ' => 'OE', 'œ' => 'oe', 'Æ' => 'AE', 'æ' => 'ae', 'µ' => 'u'
        ));
        $cleanName = preg_replace(
                array('/\s/', '/\.[\.]+/', '/[^\w_\.\-]/'),
                array('_', '.', ''),
                $cleanName
        );

        return preg_replace('/([^0-9a-zA-Z_.]+)/', '', strtolower($cleanName));
    }

    /**
     *
     * @param String $fileName
     * @return String
     */
    private function generateName($fileName){
        //Cria o nome randomico
        $string = date('YmdHis') . rand(1, 1000);

        //Busca a extensão
        $arrayNome = explode('.', $fileName);
        $ext = $arrayNome[count($arrayNome) - 1];

        return $string.".".$ext;
    }

}
