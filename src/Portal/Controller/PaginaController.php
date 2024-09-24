<?php

namespace Portal\Controller;

/**
 * Novas Páginas
 *
 */
class PaginaController extends PortalController
{

    const PAGE_TITLE = "Página";
    const DEFAULT_ACTION = "index";

    protected $title = self::PAGE_TITLE;
    protected $defaultAction = self::DEFAULT_ACTION;

    /**
     * Detalhes de uma página.
     *
     * @return string
     */
    public function detalhes($id = null, $hash = null)
    {
        //Chama a página estática
        if ($hash) {
            if ($this->verifyHash($hash)) {
                $pagina = $this->getEm()->getRepository('Entity\PaginaEstatica')->getQueryPortal($id, false);

            } else {
                throw new \Exception\NotFoundException;
            }
        } else {
            $pagina = $this->getEm()->getRepository('Entity\PaginaEstatica')->getQueryPortal($id);
        }

        //Verirfica a existência da página
        if (!$pagina) {
            throw new \Exception\NotFoundException;
        }
        
        // Caso no formulário (CMS > Novas páginas > Form ) a opção "Menu Relacionado" foi marcada,
        // então pega do menu vinculado à esta página
        $menu = $pagina->getMenu();
        if(!empty($menu)) {
            
            $bread = $this->getEm()->getRepository('Entity\Menu')->find($menu);
        }
        else {
            $bread = $this->getEm()->getRepository('Entity\Menu')->getBreadCrumbs("Entity\PaginaEstatica", $id, null, $this->getSubsite());
        }
        
        //var_dump($bread);
        //die();

        //Renderiza o template
        $this->tpl->setTitle($this->getTitle());
        $this->getTpl()->renderView(array(
            'pagina' => $pagina,
            'bread' => $bread,
            'site' => $this->getSubsite(),
            'preVisualizacao' => $hash ? true : false
        ));

        return $this->tpl->output();
    }

}
