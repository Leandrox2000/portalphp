<?php

namespace Portal\Controller;

use Helpers\Pagination;

/**
 * Agenda de Eventos
 * 
 */
class AgendaEventosController extends PortalController
{

    protected $defaultAction = 'lista';

    /**
     *
     * @return \Entity\Repository\AgendaRepository
     */
    private function getRepository()
    {
        return $this->getEm()->getRepository('Entity\Agenda');
    }

    /**
     * Lista de eventos cadastrados na Agenda.
     *
     * @return string
     */
    public function lista()
    {

        $now = new \DateTime('now');
        $paramData = $this->getParam()->getString('data', $now->format('d-m-Y'));
        $repository = $this->getRepository();
        $marcado = json_encode($repository->listaMarcada($this->getSubsite()));
        $results = $repository->getConteudoInterna($this->getSubsite(), $paramData);
        $pagination = new Pagination($results);
        $bread = $this->getEm()->getRepository('Entity\Menu')->getBreadCrumbs("Entity\Agenda", null, null, $this->getSubsite());
        $this->getTpl()->setTitle('Agenda');
        $this->getTpl()->renderView(array(
            'marcado' => $marcado,
            'paramData' => $paramData,
            'results' => $pagination->results(),
            'pagination' => $pagination->render(),
            'paginationObject' => $pagination->getPagerfanta(), // Classe da paginação
            'bread' => $bread,
            'site' => $this->getSubsite(),
        ));

        return $this->getTpl()->output();
    }

    /**
     * Detalhes de um evento cadastrado na Agenda.
     *
     * @param integer $id
     * @return string
     */
    public function detalhes($id = NULL)
    {
        $repository = $this->getEm()->getRepository('Entity\Agenda');
        $result = $repository->getPublicado($id);
        $bread = $this->getEm()->getRepository('Entity\Menu')->getBreadCrumbs("Entity\Agenda", null, null, $this->getSubsite());
        $this->getTpl()->setTitle('Evento: ' . $result->getTitulo());
        $this->getTpl()->renderView(array(
            'result' => $result,
            'bread' => $bread,
            'site' => $this->getSubsite(),
        ));

        return $this->getTpl()->output();
    }



}

