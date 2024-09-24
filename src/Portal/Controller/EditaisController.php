<?php

namespace Portal\Controller;

use Helpers\Pagination;

/**
 * Editais
 *
 */
class EditaisController extends PortalController {

    protected $defaultAction = 'lista';

    /**
     *
     * @return \Entity\Repository\EditalRepository
     */
    private function getRepository()
    {
        return $this->getEm()->getRepository('Entity\Edital');
    }

    public function paginar(){
        
        $assign = array();
        $filter = array();
        $param = $this->getParam();
        
        $assign['perpage'] = 10;
        $assign['limit'] = $param->getString('limit', 10);
        
        $arrSubSite = $this->getSubsite();
        if(!empty($arrSubSite)) {
            $filter['subsite'] = $arrSubSite;
        }
        
        if ($param->getString('categoria')) {

            $filter['categoria'] = $param->getString('categoria');
        }

        if ($param->getString('status')) {

            $filter['status'] = $param->getString('status');
        }
        
      
        $assign['count'] = intval($this->getRepository()->getEditaisCount($filter));
        
        $assign['editais'] = $this->getRepository()->getEditaisRaw($filter, 0, $assign['limit']);        
        
        
        return $this->getTpl()->renderView($assign, 'editais/paginar.html.twig');
    }
    /**
     * Listagem de Editais.
     *
     * @return string
     */
    public function lista() {
        
        //\Doctrine\Common\Util\Debug::dump($this->getSubsite()); exit;
        
        $categorias = $this->getEm()->getRepository('Entity\EditalCategoria')->getCategorias();
        
        $noFilter = false;
        
        $allStatus = $status = $this->getEm()->getRepository('Entity\EditalStatus')->findAll();
        
        if($this->getParam()->getString('status')){
            
            $status = $this->getEm()->getRepository('Entity\EditalStatus')->findBy(array('id' => $this->getParam()->getString('status')));
        }
        else {
            
            $status = $allStatus;
        }
        
        $bread = $this->getEm()->getRepository('Entity\Menu')->getBreadCrumbs("Entity\Edital", null, null, $this->getSubsite());
        
        $this->getTpl()->setTitle('Editais');
        $this->getTpl()->renderView(array(
            'categorias' => $categorias,
            'selCategoria' => $this->getParam()->getString('categoria'),
            'allStatus' => $allStatus,
            'status' => $status,
            'selStatus' => $this->getParam()->getString('status'),
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
        $repository = $this->getEm()->getRepository('Entity\Edital');
        $result = $repository->getPublicado($id);

        if (!$result) {
            throw new \Exception\NotFoundException();
        }

        $bread = $this->getEm()->getRepository('Entity\Menu')->getBreadCrumbs("Entity\Edital", null, null, $this->getSubsite());
        
        $this->getTpl()->setTitle('Edital: ' . $result->getNome());
        $this->getTpl()->renderView(array(
            'result' => $result,
            'bread' => $bread,
            'site' => $this->getSubsite(),
        ));

        return $this->getTpl()->output();
    }

}
