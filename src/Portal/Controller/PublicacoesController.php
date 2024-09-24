<?php

namespace Portal\Controller;

use Helpers\Pagination;

/**
 * Publicações
 * 
 */
class PublicacoesController extends PortalController
{

    protected $defaultAction = 'index';

    /**
     * Capa de Publicações.
     *
     * @return string
     */
    public function index()
    {
        try {
            $introducao = $this->getEm()
                               ->getRepository('Entity\PublicacaoIntroducao')
                               ->createQueryBuilder('e')
                               ->getQuery()
                               ->setMaxResults(1)
                               ->getSingleResult();
        } catch (\Doctrine\ORM\NoResultException $e) {
            $introducao = null;
        }
        $categorias = $this->getEm()
                           ->getRepository('Entity\PublicacaoCategoria')
                           ->findBy(array(), array('ordem' => 'ASC'));
        $bread = $this->getEm()->getRepository('Entity\Menu')->getBreadCrumbs("", null, "publicacoes", $this->getSubsite());
        $this->getTpl()->setTitle('Publicações');
        $this->getTpl()->renderView(array(
            'introducao' => $introducao,
            'categorias' => $categorias,
            'bread' => $bread,
            'site' => $this->getSubsite(),
        ));

        return $this->getTpl()->output();
    }

    /**
     * Listagem de Publicações.
     *
     * @return string
     */
    public function lista()
    {
        $param = $this->getParam();
        $repository = $this->getEm()->getRepository('Entity\Publicacao');
        $paramCategoria = $param->get('categoria');
        $paramBusca = $param->get('busca');
        $results = $repository->getConteudoInternaOrder($paramCategoria, $paramBusca, 'publicacao');
        $pagination = new Pagination($results, 10);
        
        $categorias = $this->getEm()
                           ->getRepository('Entity\PublicacaoCategoria')
                           ->findBy(array(), array('ordem' => 'ASC'));
        
        if(!empty($paramCategoria)){
            $categoria = $this->getEm()
                        ->getRepository('Entity\PublicacaoCategoria')
                        ->find($paramCategoria);
        } else {
            $categoria = null;
        }
        $bread = $this->getEm()->getRepository('Entity\Menu')->getBreadCrumbs("", null, "publicacoes", $this->getSubsite());
        $this->getTpl()->setTitle('Publicações');
        $this->getTpl()->renderView(array(
            'busca' => $paramBusca,
            'categoria' => $categoria,
            'paramCategoria' => $paramCategoria,
            'categorias' => $categorias,
            'results' => $pagination->results(),
            'pagination' => $pagination->render(),
            'paginationObject' => $pagination->getPagerfanta(), // Classe da paginação
            'bread' => $bread,
            'site' => $this->getSubsite(),
        ));

        return $this->getTpl()->output();
    }
}
