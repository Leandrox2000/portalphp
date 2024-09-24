<?php

namespace Portal\Controller;

use Helpers\Pagination;

/**
 * Livraria Virtual
 */
class LivrariaVirtualController extends PortalController
{

    protected $defaultAction = 'lista'; 

    /**
     * Listagem de Livraria Virtual.
     *
     * @return string
     */
    public function lista()
    {
        $param = $this->getParam();
        $categorias = $this->getEm()->getRepository('Entity\PublicacaoCategoria')->findBy(array(), array('ordem' => 'ASC'));
        
        $paramCategoria = $param->get('categoria');
        $paramBusca = $param->get('busca');
        
        $repository = $this->getEm()->getRepository('Entity\Publicacao');
        $results = $repository->getConteudoInterna($paramCategoria, $paramBusca, 'livraria');
        $pagination = new Pagination($results, 4);
        $bread = $this->getEm()->getRepository('Entity\Menu')->getBreadCrumbs("", null, "livrariaVirtual", $this->getSubsite());

        $this->getTpl()->setTitle('Livraria');
        $this->getTpl()->renderView(array(
            'categoria' => $paramCategoria,
            'busca' => $paramBusca,
            'results' => $pagination->results(),
            'pagination' => $pagination->render(),
            'paginationObject' => $pagination->getPagerfanta(), // Classe da paginação
            'categorias' => $categorias,
            'bread' => $bread,
            'site' => $this->getSubsite(),
        ));

        return $this->getTpl()->output();
    }
}
