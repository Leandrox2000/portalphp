<?php

namespace Portal\Controller;

use Helpers\Pagination;

/**
 * Fototecas
 *
 */
class FototecaController extends PortalController
{

    protected $defaultAction = 'lista';

    /**
     *
     * @return \Entity\Repository\FototecaRepository
     */
    private function getRepository()
    {
        return $this->getEm()->getRepository('Entity\Fototeca');
    }

    /**
     * Listagem de Fototecas.
     *
     * @param integer $id_banner
     * @param integer $id_categoria_banner
     * @param integer $hash
     * @return String
     */
    public function lista($id_banner = null, $id_categoria_banner = null, $hash = null)
    {
        //Busca os banners
        $this->getBannersLaterais($hash, $id_categoria_banner, $id_banner);

        $pagMaximo = 9;
        $pagNumero = $this->getParam()->getString('pagina', 1);
        
        $this->tpl->setTitle('Fototecas');
        $categorias = $this->getEm()->getRepository('Entity\CategoriaFototeca')->findAll();
        $categoria = $this->getParam()->getString('categoria');
        $query = $this->getRepository()->getQueryPortal(null, $categoria, null, $pagNumero, $pagMaximo);
        $pagination = new Pagination($query, $pagMaximo);

        $bread = $this->getEm()->getRepository('Entity\Menu')->getBreadCrumbs("Entity\Fototeca", null, null, $this->getSubsite());
        //Manda os dados para view
        $this->getTpl()->renderView(array(
            'categoria' => $categoria,
            'categorias' => $categorias,
            'results' => $pagination->results(),
            'pagination' => $pagination->render(),
            'paginationObject' => $pagination->getPagerfanta(), // Classe da paginação
            'bread' => $bread,
            'site' => $this->getSubsite(),
        ));

        return $this->tpl->output();
    }

    /**
     * Detalhes de uma Fototeca.
     *
     * @param integer $id
     * @return string
     * @throws \Exception\NotFoundException
     */
    public function detalhes($id)
    {
        $entity = $this->getRepository()->getPublicado($id);
        
        if($this->getRepository()->getRelacionados($id))
            $relacionados = $this->getRepository()->getRelacionados($id)->getFototecasFilhas();
        
        $bread = $this->getEm()->getRepository('Entity\Menu')->getBreadCrumbs("Entity\Fototeca", null, null, $this->getSubsite());
        
        $arrayIdsGalerias = $this->getEm()->getRepository("Entity\Galeria")->getGaleriasIdsFototeca($id);
        
        if($arrayIdsGalerias){
	        foreach($arrayIdsGalerias as $id){
	            $ordem[] = $id['idGaleria'];
			}        	
        }
		 
        $this->getTpl()->renderView(array(
            'entity' => $entity,
            'relacionados' => $relacionados,
            'bread' => $bread,
            'site' => $this->getSubsite(),
            'ordem' => $ordem,
        ));
        
        return $this->tpl->output();
    }

}