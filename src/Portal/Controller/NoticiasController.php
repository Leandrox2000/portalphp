<?php

namespace Portal\Controller;

use Helpers\Pagination;
use CMS\Service\ServiceRepository\ComentarioNoticia as ComentarioNoticiaService;
use Entity\ComentarioNoticia;

/** 
 * Description of NoticiasController
 */
class NoticiasController extends PortalController
{

    protected $defaultAction = 'lista';

    /**
     *
     * @return \Entity\Repository\NoticiaRepository
     */
    private function getRepository()
    {
        return $this->getEm()->getRepository('Entity\Noticia');
    }

    /**
     * Listagem de notícias.
     *
     * @return string
     */
    public function lista()
    {
        $repository = $this->getRepository();
        $dateHelper = new \Helpers\DatetimeFormat();
        $paramPagina = $this->getParam()->getInt('pagina', 0);

        // Prepara os parâmetros
        $paramData = $this->getParam()->getString('data');
        $paramPalavraChave = $this->getParam()->getString('palavraChave');
        $filtros = array(
            'data' => $dateHelper->formatUs($paramData),
            'palavraChave' => $paramPalavraChave
        );

        $featuredResults = $repository->getQueryPortal($this->getSubsite(), 3, NULL, NULL, TRUE)->getResult();
        $notIn = array();
        foreach ($featuredResults as $result) { $notIn[] = $result->getId(); }

        //busca 
        $results = $repository->getQueryPortal($this->getSubsite(), NULL, $notIn, $filtros);
        $pagination = new Pagination($results);
        
        $bread = $this->getEm()->getRepository('Entity\Menu')->getBreadCrumbs("Entity\Noticia", null, null, $this->getSubsite());

        $this->getTpl()->setTitle('Notícias');
        $this->getTpl()->renderView(array(
            'paramData' => $paramData,
            'paramPagina' => $paramPagina,
            'paramPalavraChave' => $paramPalavraChave,
            'featuredResults' => $featuredResults,
            'results' => $pagination->results(),
            'pagination' => $pagination->render(),
            'paginationObject' => $pagination->getPagerfanta(), // Classe da paginação
            'bread' => $bread,
            'site' => $this->getSubsite(),
        ));

        return $this->getTpl()->output();
    }

    /**
     * Detalhes de uma Notícia.
     *
     * @param integer $id
     * @return string
     * @throws \Exception\NotFoundException
     */
    public function detalhes($id = NULL)
    {
        $hash = $this->getParam()->get('hash');
        $verificarPublicado = !empty($hash) ? false : true;
        $repository = $this->getEm()->getRepository('Entity\Noticia');
        
        if (!empty($hash)) {
            if ($this->verifyHash($hash)) {
                $result = $repository->getPublicado($id, $verificarPublicado);

            } else {
                throw new \Exception\NotFoundException;
            }
        } else {
            $result = $repository->getPublicado($id, $verificarPublicado);
        }

        if (!$result) {
            throw new \Exception\NotFoundException;
        }
        
        $gals = $this->getEm()->getRepository('Entity\NoticiaGaleria')->getGaleriasNoticia($id);
        
        $ordemImagensGalerias = array();
        
        foreach($gals as $g){
            $imagensGaleria = $this->getEm()->getRepository('Entity\Imagem')->getImagemIdsGaleria($g->getGaleria()->getId());
    
             if($imagensGaleria){
                 
	            foreach($imagensGaleria as $key => $imagemGaleria){
	            	$ordemImagensGalerias[$g->getGaleria()->getId()][$key] = $imagemGaleria['imagemId'];
	            }
            }
            
        }

//        foreach ($gals as $galeria) {
//            echo $galeria->getGaleria()->getPublicado();
//            echo "<hr>";
//        }

        // $galeriasIdsOrdem = $this->getEm()->getRepository('Entity\NoticiaGaleria')->findBy(array('galeria' => explode(',', $ids), 'noticia' => $idNoticia), array('ordemGaleria' => 'ASC'));

        $bread = $this->getEm()->getRepository('Entity\Menu')->getBreadCrumbs("Entity\Noticia", null, null, $this->getSubsite());
        $this->getTpl()->setTitle('Notícia: ' . $result->getTitulo());
        $this->getTpl()->renderView(array(
            'result'            => $result,
            'preVisualizacao'   => $hash ? true : false,
            'bread'             => $bread,
            'site'              => $this->getSubsite(),
            'galerias'          => $gals,
            'ordemImagensGalerias' => $ordemImagensGalerias
        ));

        return $this->getTpl()->output();
    }


    /**
     * Cadastra um comentário na Notícia.
     *
     * @return string
     */
    public function enviaComentario()
    {
        $param = $this->getParam();
        $entity = new ComentarioNoticia();
        $service = new ComentarioNoticiaService($this->getEm(), $entity, $this->getSession());
        $noticiaReference = $this->getEm()->getReference('Entity\Noticia', $param->get('noticia'));
        $dados = array(
            'dataInicial' => new \DateTime('now'),
            'autor' => $param->get('nome'),
            'email' => $param->get('email'),
            'comentario' => $param->get('comentario'),
            'noticia' => $noticiaReference,
        );

        try {
            $service->save($dados);
            $retorno = array('resultado' => 1);
        } catch (\Exception $e) {
            $retorno = array('resultado' => 0);
        }

        return json_encode($retorno);
    }

    /**
     * Retorna a lista de comentários de uma notícia.
     *
     * @param integer $id ID da notícia.
     * @return string
     */
    public function listaComentarios($id = NULL)
    {
        $repository = $this->getEm()->getRepository('Entity\ComentarioNoticia');
        $results = $repository->listarComentarios($id);
        $count = $repository->countComentariosPublicados($id);
        $html = $this->getTpl()->renderView(
            array('results' => $results),
            'noticias/listaComentarios.html.twig'
        );

        header('Content-Type: application/json');
        return json_encode(array(
            'count' => $count,
            'html' => $html
        ));
    }

}

