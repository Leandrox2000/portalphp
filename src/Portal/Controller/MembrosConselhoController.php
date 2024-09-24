<?php

namespace Portal\Controller;

use Helpers\Pagination;

/**
 * Conselheiros
 *
 */
class MembrosConselhoController extends PortalController
{

    protected $defaultAction = 'lista';

    /**
     * Listagem de Membros do Conselho.
     *
     * @return string
     */
    public function lista()
    {
        $repository = $this->getEm()->getRepository('Entity\Conselheiro');
        $results = $repository->getConteudoInterna();
        $pagination = new Pagination($results, 20);
        $bread = $this->getEm()->getRepository('Entity\Menu')->getBreadCrumbs("Entity\Conselheiro", null, null, $this->getSubsite());
        $this->getTpl()->setTitle('Conselheiros');
        $this->getTpl()->renderView(array(
            'results' => $pagination->results(),
            'pagination' => $pagination->render(),
            'paginationObject' => $pagination->getPagerfanta(), // Classe da paginação
            'bread' => $bread,
            'site' => $this->getSubsite(),
        ));

        return $this->getTpl()->output();
    }


}
