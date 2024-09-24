<?php

namespace Portal\Controller;

use Helpers\Pagination;

/**
 * Description of LicitacoesConveniosContratos
 *
 */
class LicitacoesConveniosContratosController extends PortalController
{

    protected $defaultAction = 'lista';

    /**
     *
     * @return \Entity\Repository\LicitacaoConvenioContratoRepository
     */
    private function getRepository()
    {
        return $this->getEm()->getRepository('Entity\LicitacaoConvenioContrato');
    }

    /**
     * Listagem de Licitações Convênios e Contratos.
     *
     * @todo Quando atualizar o php >=5.4 e o doctrine para >=2-5-0-alpha-2, o método
     * getConteudoInterna() pode voltar a ser utilizado
     * @return string
     */
    public function lista()
    {
        // Parâmetros
        $paramCategoria = $this->getParam()->getString('categoria');
        $paramStatus = $this->getParam()->getString('status');
        $paramTipo = $this->getParam()->getString('tipo');

        // Opções do formulário
        //$categorias = $this->getEm()->getRepository('Entity\CategoriaLcc')->findAll();
        $categorias = $this->getEm()->getRepository('Entity\CategoriaLcc')->getCategorias();
        //$tipos = $this->getEm()->getRepository('Entity\TipoLcc')->findAll();
        $tipos = $this->getEm()->getRepository('Entity\TipoLcc')->getTipos();
        $status = $this->getEm()->getRepository('Entity\StatusLcc')->findBy(array(), array('ordem' => 'ASC'));

        $repository = $this->getRepository();
        $noFilter = false;

//        if (!empty($paramCategoria) || !empty($paramStatus) || !empty($paramTipo)) {
//            $results = $repository->getConteudoInterna($paramStatus, $paramCategoria, $paramTipo);
//            $pagination = new Pagination($results);
//        } else {
//            $results = $repository->getConteudoInterna();
//            $pagination = new Pagination($results, 10);
//            $noFilter = true;
//        }        
         
        $adapter = $repository->getPagination(array(
            
            'categoria' => $paramCategoria,
            'status' => $paramStatus,
            'tipo' => $paramTipo,
            
        ));  
        $pagination = new \Helpers\Pagination($adapter);
       
        $results = $repository->getResultsFromPagination($pagination->getPagerfanta());
                       
        $bread = $this->getEm()->getRepository('Entity\Menu')->getBreadCrumbs("Entity\LicitacaoConvenioContrato", null, null, $this->getSubsite());
        
        $this->getTpl()->setTitle('Licitações e Convênios');
        $this->getTpl()->renderView(array(
            'results' => $results,
            'pagination' => is_object($pagination) ? $pagination->render() : null,
            'paginationObject' => $pagination->getPagerfanta(), // Classe da paginação
            'paramStatus' => $paramStatus,
            'paramTipo' => $paramTipo,
            'paramCategoria' => $paramCategoria,
            'categorias' => $categorias,
            'tipos' => $tipos,
            'status' => $status,
            'noFilter' => $noFilter,
            'bread' => $bread,
            'site' => $this->getSubsite(),
        ));

        return $this->getTpl()->output();
    }

    /**
     * Detalhes de Editais.
     *
     * @param integer $id
     * @return string
     */
    public function detalhes($id = NULL)
    {
        $repository = $this->getEm()->getRepository('Entity\LicitacaoConvenioContrato');
        $result = $repository->getPublicado($id);

        if (!$result) {
            throw new \Exception\NotFoundException();
        }
        
        $bread = $this->getEm()->getRepository('Entity\Menu')->getBreadCrumbs("Entity\LicitacaoConvenioContrato", null, null, $this->getSubsite());

        $this->getTpl()->setTitle('Licitações, Convênios e Contratos: ' . $result->getObjeto());
        
        $categoria = $result->getCategoria();
        $categoriaFixa = in_array($categoria->getLabel(), $this->getEm()->getRepository('Entity\CategoriaLcc')->getCategoriasFixas()) ? true : null;
             
        $this->getTpl()->renderView(array(
            'result' => $result,
            'bread' => $bread,
            'site' => $this->getSubsite(),
            'categoriaFixa' => $categoriaFixa,
        ));

        return $this->getTpl()->output();
    }
}
