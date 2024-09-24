<?php

namespace Portal\Controller;

use Helpers\Pagination;

/**
 * Bibliotecas do IPHAN
 *
 */
class BibliotecasIphanController extends PortalController
{

    protected $defaultAction = 'lista';

    /**
     * Listagem de Bibliotecas do IPHAN
     *
     * @return string
     */
    public function lista()
    {
        $param = $this->getParam();
        $repository = $this->getEm()->getRepository('Entity\Biblioteca');
        $estados = $repository->getEstados();
        $paramEstado = $param->get('estado');
        //$results = $repository->getConteudoInterna($paramEstado);
        $results = $repository->getConteudoInternaOrder($paramEstado);
        $pagination = new Pagination($results, 10);

        $estadosValues = array();
        foreach ($estados as $e) {
            $estadosValues[] = new \Entity\Type($e['uf'], $e['uf']);
        }
        $bread = $this->getEm()->getRepository('Entity\Menu')->getBreadCrumbs("Entity\Biblioteca", null, null, $this->getSubsite());
        
        $this->getTpl()->setTitle('Bibliotecas do IPHAN');
        $this->getTpl()->renderView(array(
            'estado' => $paramEstado,
            'estados' => $estadosValues,
            'results' => $pagination->results(),
            'pagination' => $pagination->render(),
            'paginationObject' => $pagination->getPagerfanta(), // Classe da paginação
            'bread' => $bread,
            'site' => $this->getSubsite(),
        ));

        return $this->getTpl()->output();
    }

}
