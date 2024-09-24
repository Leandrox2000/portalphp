<?php

namespace CMS\Service\ServiceYoutube;

use Logger;
use CMS\StaticMethods\StaticMethods;
use Helpers\Youtube;

/**
 * Description of ServiceYoutube
 *
 * @autor join-ti
 */
class ServiceYoutube
{

    /**
     *
     * @var Logger
     */
    private $logger;

    /**
     *
     * @var StaticMethods
     */
    private $staticMethods;

    /**
     *
     * @param StaticMethods $staticMethods
     */
    public function setStaticMethods(StaticMethods $staticMethods)
    {
        $this->staticMethods = $staticMethods;
    }

    /**
     *
     * @return type new \CMS\StaticMethods\StaticMethods()
     */
    public function getStaticMethods()
    {
        if (!isset($this->staticMethods)) {
            $this->staticMethods = new StaticMethods();
        }
        return $this->staticMethods;
    }

    /**
     *
     * @param Logger $logger
     */
    public function setLogger(Logger $logger)
    {
        $this->logger = $logger;
    }

    /**
     *
     * @return Logger
     */
    public function getLogger()
    {
        if (!isset($this->logger)) {
            $this->logger = $this->getStaticMethods()->getLoggerFactory();
        }
        return $this->logger;
    }

    /**
     *
     * @param String $link
     * @return array
     */
    public function getVideoLink($link)
    {
        //Cria o array que armazenará os dados do vídeo
        $dadosVideo = array('views' => '', 'titulo' => '', 'descricao' => '', 'embed' => '');

        try {
            //Verifica se o link do vídeo é vazio
            if (!empty($link)) {
                //Busca os dados do vídeo
                $youtubeDetails = new Youtube($link);
                $info = $youtubeDetails->getInfor();

                if ($info) {
                    //Organiza os dados do vídeo
                    $dadosVideo['key'] = $info['key'];
                    $dadosVideo['titulo'] = $info['title'];
                    $dadosVideo['descricao'] = $info['description'];
                    $dadosVideo['autor'] = $info['author'];
                    $dadosVideo['embed'] = $info['htmlEmbed']; 
                }
            }
        } catch (\Exception $ex) {
            $this->getLogger()->error("Ao ao validar link do youtube " . $ex->getMessage());
        }

        return $dadosVideo;
    }

    /**
     *
     * @param String $link
     * @return boolean
     */
    public function validarLink($link)
    {
        //Verifica se o link do vídeo é vazio
        if (!empty($link)) {
            //Busca os dados do vídeo
            $youtubeDetails = new Youtube($link);
            $info = $youtubeDetails->getInfor();

            //Verifica se algo foi encontrado
            if (!empty($info['key'])) {
                return true;
            } else {
                return false;
            }
        }

        return false;
    }

}
