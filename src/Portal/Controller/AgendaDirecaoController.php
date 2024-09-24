<?php

namespace Portal\Controller;

use Helpers\Pagination;

/**
 * Agenda da Direção
 * 
 */
class AgendaDirecaoController extends PortalController
{

    protected $defaultAction = 'lista';

    /**
     *
     * @return \Entity\Repository\AgendaDirecaoRepository
     */
    private function getRepository()
    {
        return $this->getEm()->getRepository('Entity\AgendaDirecao');
    }

    /**
     * Lista de eventos cadastrados na Agenda.
     *
     * @return string
     */
    public function lista()
    {
        $agendas = $this->getRepository()->agendasPublicadas($this->getSubsite());
        $bread = $this->getEm()->getRepository('Entity\Menu')->getBreadCrumbs("Entity\AgendaDirecao", null, null, $this->getSubsite());
        
        $this->getTpl()->setTitle('Agenda da Direção');
        $this->getTpl()->renderView(array(
            'agendas' => $agendas,
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
    public function detalhes($id)
    {
        $agenda = $this->getRepository()->getPublicado($id);
        $bread = $this->getEm()->getRepository('Entity\Menu')->getBreadCrumbs("Entity\AgendaDirecao", null, null, $this->getSubsite());
        $this->getTpl()->setTitle('Agenda da Direção: ' . $agenda->getTitulo());
                        
        // Consulta as marcações dos dias
        $repository = $this->getEm()->getRepository('Entity\Compromisso');
        $marcado = json_encode($repository->listaMarcada($id, $this->getSubsite()));
        
        // Consulta os compromissos e cria a paginação
        $now = new \DateTime('now');
        $paramData = $this->getParam()->getString('data', $now->format('Y-m-d'));
        $results = $repository->getCompromissos($id, $this->getSubsite(), $paramData);
        $pagination = new Pagination($results);
        
        $this->getTpl()->renderView(array(
            'agenda' => $agenda,
            'marcado' => $marcado,
            'paramData' => new \DateTime($paramData),
            'results' => $pagination->results(),
            'pagination' => $pagination->render(),
            'paginationObject' => $pagination->getPagerfanta(), // Classe da paginação
            'bread' => $bread,
            'site' => $this->getSubsite(),
        ));

        return $this->getTpl()->output();
    }



}

