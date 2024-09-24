<?php

namespace Portal\Controller;

use Helpers\Pagination;

/**
 * Legislação
 *
 */
class LegislacaoController extends PortalController
{
    const PAGE_TITLE = "Legislação";
    const DEFAULT_ACTION = "lista";

    protected $title = self::PAGE_TITLE;
    protected $defaultAction = self::DEFAULT_ACTION;

    /**
     * Listagem registros.
     *
     * @return string
     */
    public function lista()
    {
        $categoria = $this->getParam()->getInt('categoria');
        $busca = $this->getParam()->getString('busca');
        $deData = $this->getParam()->getString('de_data');
        $ateData = $this->getParam()->getString('ate_data');

        /*$categorias = $this->getEm()
                           ->getRepository('Entity\CategoriaLegislacao')
                           ->findAll();*/
        $categorias = $this->getEm()
                           ->getRepository('Entity\CategoriaLegislacao')
                           ->getBuscaCategoriaLegislacao();
        $query = $this->getEm()
                      ->getRepository('Entity\Legislacao')
                      ->getQueryPortalASC($this->getSubsite(), $categoria, $busca, $deData, $ateData);

        $pagination = new Pagination($query);

        $bread = $this->getEm()->getRepository('Entity\Menu')->getBreadCrumbs("Entity\Legislacao", null, null, $this->getSubsite());
        
        $this->tpl->setTitle($this->getTitle());
        $this->getTpl()->renderView(array(
            'categorias' => $categorias,
            'categoria' => $categoria,
            'busca' => $busca,
            'deData' => $deData,
            'ateData' => $ateData,
            'pagination' => $pagination->render(),
            'paginationObject' => $pagination->getPagerfanta(), // Classe da paginação
            'legislacao' => $pagination->results(),
            'bread' => $bread,
            'site' => $this->getSubsite(),
        ));

        return $this->tpl->output();
    }

}
