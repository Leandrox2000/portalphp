<?php

namespace Portal\Controller;

use Helpers\Pagination;

/**
 * Description of DicionariPatrimonioCulturalController
 *
 */
class DicionarioPatrimonioCulturalController extends PortalController
{

    protected $defaultAction = 'lista';

    /**
     * Listagem de Dicionário do Patriomônio Cultural.
     *
     * @return string
     */
    public function lista()
    {
        $param = $this->getParam();
        $repository = $this->getEm()->getRepository('Entity\DicionarioPatrimonioCultural');
        //$categorias = $this->getEm()->getRepository('Entity\CategoriaDicionario')->findAll();
        $categorias = $this->getEm()->getRepository('Entity\CategoriaDicionario')->getCategorias();
        $paramCategoria = $param->get('categoria');
        $paramBusca = $param->get('busca');
        if(!$paramBusca and !$paramCategoria)
            $paramLetra = $this->getParam()->getString('letra', 'a');
        $results = $repository->getConteudoInterna($paramCategoria, str_replace("+", " ", $paramBusca), $paramLetra);
        $pagination = new Pagination($results, 10);

        $bread = $this->getEm()->getRepository('Entity\Menu')->getBreadCrumbs("Entity\DicionarioPatrimonioCultural", null, null, $this->getSubsite());
        
        $this->getTpl()->setTitle('Dicionário do Patrimônio Cultural');
        $this->getTpl()->renderView(array(
            'paramLetra' => $paramLetra,
            'categoria' => $paramCategoria,
            'busca' => $paramBusca,
            'categorias' => $categorias,
            'results' => $pagination->results(),
            'pagination' => $pagination->render(),
            'paginationObject' => $pagination->getPagerfanta(), // Classe da paginação
            'bread' => $bread,
            'site' => $this->getSubsite(),
        ));

        return $this->getTpl()->output();
    }

    /**
     * Detalhes de Dicionário do Patriônio Cultural.
     *
     * @param integer $id
     * @return string
     */
    public function detalhes($id = NULL)
    {
        $repository = $this->getEm()->getRepository('Entity\DicionarioPatrimonioCultural');
        $result = $repository->getPublicado($id);

        if (!$result) {
            throw new \Exception\NotFoundException();
        }
        $bread = $this->getEm()->getRepository('Entity\Menu')->getBreadCrumbs("Entity\DicionarioPatrimonioCultural", null, null, $this->getSubsite());

        $this->getTpl()->setTitle('Dicionário do Patrimônio Cultural: ' . $result->getTitulo());
        $this->getTpl()->renderView(array(
            'result' => $result,
            'bread' => $bread,
            'site' => $this->getSubsite(),
        ));

        return $this->getTpl()->output();
    }

}
