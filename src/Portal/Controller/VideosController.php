<?php

namespace Portal\Controller;

use Helpers\Pagination;

/**
 * Vídeos
 * 
 */
class VideosController extends PortalController
{

    protected $defaultAction = 'lista';

    /**
     * Listagem de vídeos.
     *
     * @return string
     */
    public function lista()
    {
        $pagMaximo = 10;
        $pagNumero = $this->getParam()->getString('pagina', 1);
        
        $repository = $this->getEm()->getRepository('Entity\Video');
        $id = $this->getSubsite();
        
        $result = $repository->getVideoDestaque($id);
        $videoDestaque = null;
        if ($result == NULL || empty($result)) {
            $result = $this->getEm()->getRepository('Entity\VideoSite')->getUltimoVideo($id);
            if ($result != 0) {
                $result = $result->getVideo();
            }
        } else {
            $videoDestaque = array($result->getId());
        }        
        
//        $video = $repository->getUltimosVideos($id, $pagNumero, $pagMaximo);
        $video = $this->getEm()->getRepository('Entity\VideoSite')->getUltimosVideosOrder($id, $pagNumero, $pagMaximo, $videoDestaque);
        $videoRelacionados = array();
        if($result != null) {
            $videoRelacionados = $this->getEm()->getRepository('Entity\VideoRelacionado')->getVideosRelacionadosByVideo($result->getId());
        }
        
        $pagination = new Pagination($video['pagina'], $pagMaximo);
        $bread = $this->getEm()->getRepository('Entity\Menu')->getBreadCrumbs("", null, "videos", $this->getSubsite());
        $this->getTpl()->setTitle('Vídeos');
        $this->getTpl()->renderView(array(
            'result' => $result,
            'videoPatrimonio' => $video['videos'],
            'results' => $pagination->results(),
            'pagination' => $pagination->render(),
            'videoRelacionados' => $videoRelacionados,
            'paginationObject' => $pagination->getPagerfanta(), // Classe da paginação
            'bread' => $bread,
            'site' => $this->getSubsite(),
        ));

        // Esconde a sidebar
        // $this->getTpl()->addGlobal('hide_sidebar', true);
        
        return $this->getTpl()->output();
    }

    /**
     * Detalhes de um vídeo.
     *
     * @return string
     */
    public function detalhes($id = null)
    {
        $pagMaximo = 10;
        $pagNumero = $this->getParam()->getString('pagina', 1);
        $idSite = $this->getSubsite();
        
        $video = $this->getEm()->getRepository('Entity\VideoSite')->getUltimosVideosOrder($idSite, 1, 20);
        
        $repository = $this->getEm()->getRepository('Entity\Video');
        $result = $repository->getPublicado($id);
        
        if (!$result) {
            throw new \Exception\NotFoundException;
        }
        $bread = $this->getEm()->getRepository('Entity\Menu')->getBreadCrumbs("", null, "videos", $this->getSubsite());
        $videoRelacionados = $this->getEm()->getRepository('Entity\VideoRelacionado')->getVideosRelacionadosByVideo($id);
        
        $this->getTpl()->setTitle('Vídeos');
        $this->getTpl()->renderView(array(
            'result' => $result,
            'videoRelacionados' => $videoRelacionados,
            'bread' => $bread,
            'site' => $this->getSubsite(),
            'videoPatrimonio' => $video['videos']
        ));
        // Esconde a sidebar
        // $this->getTpl()->addGlobal('hide_sidebar', true);

        return $this->getTpl()->output();
    }

}
