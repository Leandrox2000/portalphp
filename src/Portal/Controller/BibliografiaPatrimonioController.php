<?php

namespace Portal\Controller;

use Helpers\Pagination;

/**
 * Bibliografia Geral do Patrimônio Cultural
 *
 */
class BibliografiaPatrimonioController extends PortalController
{

    protected $defaultAction = 'lista';

    /**
     *
     * @return \Entity\Repository\BibliografiaRepository
     */
    private function getRepository()
    {
        return $this->getEm()->getRepository('Entity\Bibliografia');
    }

    /**
     * Listagem de Blibliografia do Patrimônio Cultural
     *
     * @return string
     */
    public function lista()
    {
        $paramLetra = $this->getParam()->getString('letra', 'a');
        $repository = $this->getRepository();
        $results = $repository->getConteudoInterna($paramLetra);
        $pagination = new Pagination($results, 10);

        $bread = $this->getEm()->getRepository('Entity\Menu')->getBreadCrumbs("Entity\Bibliografia", null, null, $this->getSubsite());
        
        $this->getTpl()->setTitle('Bibliografia Geral do Patrimônio Cultural');
        $this->getTpl()->renderView(array(
            'paramLetra' => $paramLetra,
            'results' => $pagination->results(),
            'pagination' => $pagination->render(),
            'paginationObject' => $pagination->getPagerfanta(), // Classe da paginação
            'bread' => $bread,
            'site' => $this->getSubsite(),
        ));

        return $this->getTpl()->output();
    }

}
