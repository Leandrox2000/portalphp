<?php

namespace Portal\Controller;

use Helpers\Pagination;

/**
 * Boletim Eletrônico
 *
 */
class BoletimEletronicoController extends PortalController
{

    protected $defaultAction = 'lista';

    /**
     * Listagem de boletins cadastrados.
     *
     * @return string
     */
    public function lista()
    {
        $param = $this->getParam();
        $repository = $this->getEm()->getRepository('Entity\BoletimEletronico');
        $results = $repository->getConteudoInterna(array(
            'numero' => $param->getString('numero'),
            'dataInicial' => $param->getString('dataInicial') 
        ));
        $pagination = new Pagination($results);
        
        $bread = $this->getEm()->getRepository('Entity\Menu')->getBreadCrumbs("Entity\BoletimEletronico", null, null, $this->getSubsite());
        
        $this->getTpl()->setTitle('Boletim Eletrônico');        
        $this->getTpl()->renderView(array(
            'paginaAtual' => $param->getString('pagina'),
            'dataInicial' => $param->getString('dataInicial'),
            'numero' => $param->getString('numero'),
            'results' => $pagination->results(),
            'pagination' => $pagination->render(),
            'paginationObject' => $pagination->getPagerfanta(), // Classe da paginação
            'bread' => $bread,
            'site' => $this->getSubsite(),
        ));

        return $this->getTpl()->output();
    }

    /**
     * Faz o cadastro de um usuário no Boletim Eletrônico.
     *
     * @return string
     */
    public function cadastro()
    {
        $param = $this->getParam();
        $entity = new \Entity\EmailBoletim();
        $entity->setNome($param->getString('nome'));
        $entity->setEmail($param->getString('email'));

        try {
            $this->getEm()->persist($entity);
            $this->getEm()->flush();
        } catch (\Exception $e) {
            return json_encode(array(
                'status' => 0,
                'mensagem' => 'Não foi possível cadastrar seu e-mail.',
            ));
        }

        return json_encode(array(
            'status' => 1,
            'mensagem' => 'Seu e-mail foi cadastrado no Boletim Eletrônico.',
        ));
    }

}

