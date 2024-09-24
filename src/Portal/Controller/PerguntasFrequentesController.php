<?php

namespace Portal\Controller;

use Helpers\Pagination;

/**
 * Perguntas Frequentes
 * 
 */
class PerguntasFrequentesController extends PortalController
{

    protected $defaultAction = 'lista';

    /**
     * Listagem de Perguntas Frequentes.
     *
     * @return string
     */
    public function lista()
    {
        $paramCategoria = $this->getParam()->getInt('categoria');
        $repository = $this->getEm()->getRepository('Entity\Pergunta');
        //$results = $repository->getConteudoInterna($paramCategoria);
        $results = $repository->getConteudoInternaOrder($paramCategoria);
        $pagination = new Pagination($results, 20);
        /*$categorias = $this->getEm()->getRepository('Entity\PerguntaCategoria')
                                    ->findAll();*/
        $categorias = $this->getEm()->getRepository('Entity\PerguntaCategoria')->getCategorias();
        $bread = $this->getEm()->getRepository('Entity\Menu')->getBreadCrumbs("Entity\Pergunta", null, null, $this->getSubsite());

        
        $this->getParam()->getInt('pagina');
        
        $indicePaginacao = 1;
        if($this->getParam()->getInt('pagina') > 1) $indicePaginacao = (round(count($pagination->results()))/$this->getParam()->getInt('pagina'))+1;
        
        $this->getTpl()->setTitle('Perguntas Frequentes');
        $this->getTpl()->renderView(array(
            'results' => $pagination->results(),
            'pagination' => $pagination->render(),
            'categorias' => $categorias,
            'paramCategoria' => $paramCategoria,
            'bread' => $bread,
            'site' => $this->getSubsite(),
            'indicePaginacao' => $indicePaginacao,
        ));

        return $this->getTpl()->output();
    }

    public function detalhes($id)
    {
        $paramCategoria = $this->getParam()->getInt('categoria');
        $allResults = $this->getEm()->getRepository('Entity\Pergunta')->findAll();
        $repository = $this->getEm()->getRepository('Entity\Pergunta');
        $results = $repository->getConteudoInternaOrder($paramCategoria);
        $page = 1;
        foreach ($allResults as $key => $value) {
            if($value->getId() == $id)
                $page = $key; 
        }

        $page = ceil($page/20);
        $_GET['pagina'] = $page;

        $pagination = new Pagination($results, 20);

        $categorias = $this->getEm()->getRepository('Entity\PerguntaCategoria')->getCategorias();
        $bread = $this->getEm()->getRepository('Entity\Menu')->getBreadCrumbs("Entity\Pergunta", null, null, $this->getSubsite());

        
        $this->getParam()->getInt('pagina');


        
        $indicePaginacao = 1;
        if($this->getParam()->getInt('pagina') > 1) $indicePaginacao = (round(count($pagination->results()))/$this->getParam()->getInt('pagina'))+1;
        
        $this->getTpl()->setTitle('Perguntas Frequentes');
        $this->getTpl()->renderView(array(
            'results' => $pagination->results(),
            'pagination' => $pagination->render(),
            'categorias' => $categorias,
            'paramCategoria' => $paramCategoria,
            'bread' => $bread,
            'site' => $this->getSubsite(),
            'indicePaginacao' => $indicePaginacao,
            'id' => $id,
        ));

        return $this->getTpl()->output();


    }

}
