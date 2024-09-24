<?php

namespace Portal\Controller;

use Helpers\Pagination;

/**
 * Galerias
 * 
 */
class GaleriaController extends PortalController {

    protected $defaultAction = 'lista';

    /**
     *
     * @return \Entity\Repository\FototecaRepository
     */
    private function getRepository()
    {
        return $this->getEm()->getRepository('Entity\Galeria');
    }

    /**
     * Listagem de Galerias.
     *
     * @return string
     */
    public function lista()
    {
        $breadFototeca  = null;
        $bread          = null;
        $this->tpl->setTitle('Galerias');

        $pagMaximo = 9;
        $pagNumero = $this->getParam()->getString('pagina', 1);
        
        //$query = $this->getRepository()->getConteudoInterna($this->getSubsite(), $pagNumero, $pagMaximo);
        $query = $this->getEm()->getRepository('Entity\GaleriaSite')->getConteudoInternaOrder($this->getSubsite(), $pagNumero, $pagMaximo);
        
        $pagination = new Pagination($query, $pagMaximo);

        if (!($this->getSubsite() instanceof \Entity\Site)) {
            $breadFototeca = $this->getEm()->getRepository('Entity\Menu')->getBreadCrumbs("Entity\Fototeca", null, null, $this->getSubsite());
        } else {
            $bread = $this->getEm()->getRepository('Entity\Menu')->getBreadCrumbs("Entity\Galeria", null, null, $this->getSubsite());
        }
        
        foreach ($pagination->results() as $galeria){
            $result[] = $galeria->getGaleria();
        }
        
        $this->getTpl()->renderView(array(
            'results' => $result,
            'pagination' => $pagination->render(),
            'paginationObject' => $pagination->getPagerfanta(),
            'bread' => $bread,
            'breadFototeca' => $breadFototeca,
            'site' => $this->getSubsite(),
        ));
        return $this->tpl->output();
    }

    /**
     * Detalhes de uma Galeria.
     *
     * @param integer $id
     * @return string
     * @throws \Exception\NotFoundException
     */
    public function detalhes($id)
    {
        $this->tpl->setTitle('Galeria');
        // Esconde a sidebar
        $this->getTpl()->addGlobal('hide_sidebar', false);

        try {
            $entity = $this->getRepository()->getPublicado($id);
            $ordemId = $this->getEm()->getRepository('Entity\Imagem')->getImagemIdsGaleria($id);
            if($ordemId){
	            foreach($ordemId as $id){
	            	$ordem[] = $id['imagemId'];
	            }
            }
        } catch (\Doctrine\ORM\NoResultException $e) {
            throw new \Exception\NotFoundException();
        }

        if ( $entity === NULL ) {
            throw new \Exception\NotFoundException();
        }

        if (!($this->getSubsite() instanceof \Entity\Site)) {
            $breadFototeca = $this->getEm()->getRepository('Entity\Menu')->getBreadCrumbs("Entity\Fototeca", null, null, $this->getSubsite());
        } else {
            $bread = $this->getEm()->getRepository('Entity\Menu')->getBreadCrumbs("Entity\Galeria", null, null, $this->getSubsite());
        }

        $this->getTpl()->renderView(array(
            'entity' => $entity,
            'paramFototeca' => $this->getParam()->getInt('eFototeca'),
            'bread' => $bread,
            'breadFototeca' => $breadFototeca,
            'site' => $this->getSubsite(),
            'ordem' => $ordem
        ));
        return $this->tpl->output();
    }

}