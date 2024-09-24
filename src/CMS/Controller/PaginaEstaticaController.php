<?php

namespace CMS\Controller;

use Entity\PaginaEstatica as PaginaEstaticaEntity;
use Entity\Type;
use CMS\Service\ServiceRepository\PaginaEstatica as PaginaEstaticaService;
use CMS\Service\ServiceRepository\Menu as MenuService;
use Helpers\Param;
use Helpers\Menu;
use Helpers\MenuLinksRapidos;
use LibraryController\CrudControllerInterface;
use Entity\Menu as MenuEntity;

/**
 * PaginaEstaticaController
 *
 * @author join-ti
 */
class PaginaEstaticaController extends CrudController implements CrudControllerInterface
{

    const PAGE_TITLE = "Novas páginas";
    const DEFAULT_ACTION = "lista";

    /**
     * @var String
     */
    protected $title = self::PAGE_TITLE;

    /**
     * @var String
     */
    protected $defaultAction = self::DEFAULT_ACTION;

    /**
     *
     * @var GaleriaEntity
     */
    protected $entity;
     
    /**
     *
     * @var MenuService
     */
    private $serviceMenu;
    

    /**
     *
     * @var GaleriaService
     */
    protected $service;

    /**
     *
     * @var array
     */
    protected $user;

    /**
     *
     * @param \Template\TemplateInterface $tpl
     * @param \Helpers\Session $session
     */
    public function __construct(\Template\TemplateInterface $tpl, \Helpers\Session $session)
    {
        parent::__construct($tpl, $session);
        $this->setUser($this->getUserSession());
    }

    /**
     *
     * @return array
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     *
     * @param array $user
     */
    public function setUser(array $user)
    {
        $this->user = $user;
    }

    /**
     *
     * @return String
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     *
     * @param String $title
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }

    /**
     *
     * @return String
     */
    public function getDefaultAction()
    {
        return $this->defaultAction;
    }

    /**
     *
     * @return PaginaEstaticaEntity
     */
    public function getEntity()
    {
        if (!isset($this->entity))
            $this->entity = new PaginaEstaticaEntity();
        return $this->entity;
    }

    /**
     *
     * @return \Entity\Menu
     */
    public function getEntityMenu()
    {
        if (empty($this->entityMenu)) {
            $this->setEntityMenu(new MenuEntity());
        }
        return $this->entityMenu;
    }
     /**
     *
     * @param \Entity\Menu $entityMenu
     */
    public function setEntityMenu(MenuEntity $entityMenu)
    {
        $this->entityMenu = $entityMenu;
    }
    
    /**
     *
     * @param PaginaEstaticaEntity $entity
     */
    public function setEntity(PaginaEstaticaEntity $entity)
    {
        $this->entity = $entity;
    }

    /**
     *
     * @return PaginaEstaticaService
     */
    public function getService()
    {
        if (!isset($this->service)) {
            $this->service = new PaginaEstaticaService($this->getEm(), $this->getEntity(), $this->getSession());
        }
        return $this->service;
    }

    /**
     *
     * @param PaginaEstaticaService $service
     */
    public function setService(PaginaEstaticaService $service)
    {
        $this->service = $service;
    }

    
    
    
      /**
     *
     * @return \CMS\Service\ServiceRepository\Menu
     */
    public function getServiceMenu()
    {
        if (empty($this->serviceMenu)) {
            $this->setServiceMenu(new MenuService($this->getEm(), $this->getEntityMenu(), $this->getSession()));
        }
        return $this->serviceMenu;
    }
    /**
     *
     * @param \CMS\Service\ServiceRepository\Menu $serviceMenu
     */
    public function setServiceMenu(MenuService $serviceMenu)
    {
        $this->serviceMenu = $serviceMenu;
    }
    
    
    
    /**
     *
     * Utilizado para exibir a página de listagem
     * @return string
     */
    public function lista()
    {

        //Cria os objetos status
        $naoPublicado = new Type("0", "Não publicado");
        $publicado = new Type("1", "Publicado");
        $compartilhado = new Type("2", "Compartilhado");
        #REGRA DE PERMISS�O
//        $sites = $this->user['sede'] ? $this->getEm()->getRepository("Entity\Site")->findBy(array(), array('nome' => 'ASC')) : $this->getEm()->getRepository("Entity\Site")->findIn($this->user['subsites']);
        $sites = $this->getEm()->getRepository("Entity\Site")->findAll();
        //Busca os anos
        $this->tpl->renderView(array(
            'status' => array($naoPublicado, $publicado, $compartilhado),
            'sites' => $sites,
            'titlePage' => $this->getTitle(),
            'subTitlePage' => "",
            'sites' => $sites
                )
        );

        return $this->tpl->output();
    }

    /**
     *
     * @param type $id
     * @return \Template\TemplateAmanda
     */
    public function form($id = 0)
    {
        //Busca dados da página
        #REGRA DE PERMISS�O
//        $pagina = $this->user['sede'] ? $this->getEm()->getRepository("Entity\PaginaEstatica")->find($id) : $this->getEm()->getRepository("Entity\PaginaEstatica")->findByIdSite($id, $this->user['subsites']);
//        $sites = $this->user['sede'] ? $this->getEm()->getRepository("Entity\Site")->findBy(array(), array('nome' => 'ASC')) : $this->getEm()->getRepository("Entity\Site")->findIn($this->user['subsites']);

        $pagina = $this->getEm()->getRepository("Entity\PaginaEstatica")->findByIdSite($id);
        $sites = $this->getEm()->getRepository("Entity\Site")->findIn($this->user['subsites']);
        
        //Verifica o id para passar o título
        if ($id == 0) {
            $this->setTitle(self::PAGE_TITLE . " - Inserir");
        } else {
            $this->setTitle(self::PAGE_TITLE . " - Alterar");
        }

        //Busca os ids das galeria relacionadas
        $idsGalerias = "";
        $htmlGalerias = "";

        if ($pagina) {
            $arrayIdsGalerias = array();
            foreach ($pagina->getGalerias() as $gal) {
                $arrayIdsGalerias[] = $gal->getGaleria()->getId();
            }

            $idsGalerias = implode(',', $arrayIdsGalerias);
            $htmlGalerias = $this->getHtmlGalerias($idsGalerias, $id);
        }

        //Adiciona o css e o js da seleção de imagens
        $this->tpl->addJS("/galeria/galerias.js");
        
        $this->tpl->addJS("/plugins/EasyTree/jquery.easytree.js");
        $this->tpl->addJS("/plugins/EasyTree/jquery.easytree.min.js");
        $this->tpl->addCSS("/plugins/skin-win8/ui.easytree.css");
        
        
        $htmlMenuRelacionado = $this->getHtmlMenuRelacionado($pagina);

        $repository = $this->getEm()->getRepository('Entity\PaginaEstatica');

        $permissao = $repository->validaVinculo($id, 'pagina_estatica');

        $this->tpl->renderView(array(
            "data" => new \DateTime('now'),
            "pagina" => $pagina,
            "idmenuRelacionado" => $pagina ? ($pagina->getMenu() ? $pagina->getMenu()->getId() : null) : null,
            "sites" => $sites,
            "idsGalerias" => $idsGalerias,
            "htmlGalerias" => $htmlGalerias,
            "htmlMenuRelacionado" => $htmlMenuRelacionado,
            "titlePage" => $this->getTitle(),
            "method" => "POST",
            "compartilhado" => $permissao
            )
        );

        return $this->tpl->output();
    }

    /**
     *
     * @return string
     */
    public function salvar()
    {

        $dados = array(
            'id' => $this->getParam()->getInt('id'),
            'login' => $this->user['dadosUser']['login'],
            'dataInicial' => new \DateTime($this->getDatetimeFomat()->formatUs($this->getParam()->get('data_inicial')) . " " . $this->getParam()->get('hora_inicial')),
            'titulo' => $this->getParam()->get('titulo'),
            'palavrasChave' => $this->getParam()->getString("palavrasChave"),
            'conteudo' => $this->getParam()->getString("conteudo"),
            'idsGalerias' => $this->getParam()->get('galeriaBanco'),
            'sites' => $this->getParam()->get('sites')
        );

        $menu = $this->getParam()->getString('idMenuRelacionado');
        if(!empty($menu)){
                    $dados['menu'] = $this->getEm()->getReference($this->getServiceMenu()->getNameEntity(), $this->getParam()->getString('idMenuRelacionado')) ;
        }
        

        
        $dataFinal = $this->getParam()->get('data_final');
        $horaFinal = $this->getParam()->get('hora_final');

        // Caso a data final seja preenchida, preenche a variável no formato YYYY-MM-DD HH:MM:SS
        // ao contrário, preenche a variável com NULL para poder apagar no banco caso haja alguma data setada na coluna da tabela.
        if (!empty($dataFinal) && !empty($horaFinal)) {
            $dados['dataFinal'] = new \DateTime($this->getDatetimeFomat()->formatUs($dataFinal) . " " . $horaFinal);
        } else {
            $dados['dataFinal'] = null;
        }

        return json_encode($this->getService()->save($dados));
    }

    /**
     *
     * Resposável por retornar os registros paginados
     * @return String
     */
    public function pagination()
    {

        //Instancia o repository
        $em = $this->getEm();
        $repoPaginaEstatica = $em->getRepository("Entity\PaginaEstatica");

        //Armazena o parâmetro
        $param = $this->getParam();

        //Busca o total
        $total = $repoPaginaEstatica->countAll($this->getSession()->get('user'));

        //Cria os filtros
        $filtros = array(
            "busca" => mb_strtolower($param->get('sSearch'), 'UTF-8'),
            "data_inicial" => $this->getDatetimeFomat()->formatUs($param->get('data_inicial')),
            "data_final" => $this->getDatetimeFomat()->formatUs($param->get('data_final')),
            "status" => $param->get('status'),
            "site" => $param->get('site')
        );
        
        //Faz a busca e armazena o total de registros
        if (!$param->get('site') || in_array($param->get('site'), $_SESSION['user']['subsites'])) {
            $paginasEstaticas = $repoPaginaEstatica->buscaRegistroByLogin('Entity\PaginaEstatica', $param->getInt("iDisplayLength"), $param->getInt("iDisplayStart"), $filtros, $this->getSession()->get('user'));
            $totalFiltro = $repoPaginaEstatica->buscaRegistroByLogin('Entity\PaginaEstatica', $param->getInt("iDisplayLength"), $param->getInt("iDisplayStart"), $filtros, $this->getSession()->get('user'), true);
            
        } else {
            $paginasEstaticas = $repoPaginaEstatica->getDataBySubsite('Entity\PaginaEstatica', $param->getInt("iDisplayLength"), $param->getInt("iDisplayStart"), $filtros, $param->get('site'));
            $totalFiltro = $repoPaginaEstatica->getDataBySubsite('Entity\PaginaEstatica', $param->getInt("iDisplayLength"), $param->getInt("iDisplayStart"), $filtros, $param->get('site'), true);
            
        }

        //Percorre e organiza o HTML da listagem
        $dados = array();
        $tag = $this->getTag();
        //$totalFiltro = $repoPaginaEstatica->getTotalBusca('Entity\PaginaEstatica', $filtros, $this->getSession()->get('user'));
        $totalFiltro = $totalFiltro[0]['total'];
        
        foreach ($paginasEstaticas as $pagina) {
            $linha = array();
            $pais = array();

            $linha[] = $this->getFields()->checkbox("paginas[]", $pagina->getId());
            
            $sitesPai = $this->getEm()->find("Entity\PaginaEstatica", $pagina->getId())->getPaiSites();
            if($sitesPai){
                foreach ($sitesPai as $pai) {
                    $pais[] = $pai->getSigla();
                }
            }
            $siglas = implode(", ", $pais);

            if ($this->verifyPermission('PAGIN_INSERIR')) {
                $linha[] = $tag->link($tag->h4($pagina->getLabel()), array("href" => "paginaEstatica/form/" . $pagina->getId())) . 'Criado por ' . $pagina->getLogin() . ' - '.$siglas.' - ' . ' em ' . $pagina->getDataCadastro()->format('d/m/Y') . " as " . $pagina->getDataCadastro()->format('H:i');
            } else {
                $linha[] = $tag->h4($pagina->getLabel()) . $pagina->getDataCadastro()->format('d/m/Y') . " as " . $pagina->getDataCadastro()->format('H:i');
            }
            
            if ($repoPaginaEstatica->getCompartilhadosById($pagina->getId(), "Entity\PaginaEstatica") == 1) {
                $linha[] = "<span class='compartilhado'>Compartilhado de $siglas</span>";
            } else {
                $linha[] = $pagina->getPublicado() ? "<span class='publicado'>Publicado</span>" : "<span class='naoPublicado'>Não publicado</span>";
            }
//            $linha[] = $pagina->getPublicado() ? "<span class='publicado'>Publicado</span>" : "<span class='naoPublicado'>Não publicado</span>";
            $linha[] = "<a href='javascript:visualizar({$pagina->getId()})' class='btn btn3 btn_search' ></a>";
            $dados[] = $linha;
        }


        //Organiza o retorno e retorna via json
        $retorno['sEcho'] = Param::getInt("sEcho");
        $retorno['iTotalDisplayRecords'] = $totalFiltro;
        $retorno['iTotalRecords'] = $total;
        $retorno['aaData'] = $dados;

        return json_encode($retorno);
    }

    
    
    public function getHtmlMenuRelacionado($pagina)
    {
        
        $html .= "<div id='demo1_menu'>";
            // Menus de Nível 1, Nível 2 e Nível 3
           $html .= "<ul><li>SEDE";
               $html .= "<ul><li>Menu Principal";
               $menuTree = $this->getEm()->getRepository('Entity\Menu')->getQueryPortal(NULL, array('n1','n2','n3'));
               $html .= Menu::menuRecursiveThree($menuTree, 0, 'menuRelacional');
               $html .= "</li></ul>";

               $html .= "<ul><li>Menu Auxiliar";
               $menuTree = $this->getEm()->getRepository('Entity\Menu')->getQueryPortal(NULL, array('aux'));
               $html .= Menu::menuRecursiveThree($menuTree, 0, 'menuRelacional');
               $html .= "</li></ul>";


               $html .= "<ul><li>Menu Links Rápidos";
               $menuTree = $this->getEm()->getRepository('Entity\Menu')->getQueryPortal(NULL, array('lr'));
               $html .=  Menu::menuRecursiveThree($menuTree, 0, 'menuRelacional');
               $html .= "</li></ul>";
           $html .= "</li></ul>";
        
        
            $html .= "<ul><li>Subsites";
            $subsites = $this->getEm()->getRepository('Entity\Site')->findAll();
            foreach ($subsites as $subsite){
                // Menus de Subsite
                if($subsite->getSigla() != "SEDE"){
                    $subsiteTree = $this->getEm()->getRepository('Entity\Menu')->getQueryPortal($subsite, array('n1', 'n2', 'n3'));
                    $html .= "<ul><li>".$subsite->getNome();
                    $html .= Menu::menuRecursiveThree($subsiteTree, 0, 'menuRelacional');
                    $html .= "</li></ul>";
                }
            }
            $html .= "</li></ul>";
        
        $html .= "</div>";
        
        return $html;
    }    
    /**
     *
     * @param string $ids
     * @return string
     */
    public function getHtmlGalerias($ids, $idPaginaEstatica = null)
    {
        if (!empty($ids)) {

            //Busca e organiza as relações da galeria com a página estática
            $arrayRelacoes = array();
            if (!is_null($idPaginaEstatica)) {
                $relacoes = $this->getEm()->getRepository('Entity\PaginaEstaticaGaleria')->getPaginaEstaticaGaleriaIdsPaginas($idPaginaEstatica);
                foreach ($relacoes as $relacao) {
                    $arrayRelacoes[$relacao->getGaleria()->getId()] = $relacao->getPosicaoPagina();
                }
            }


            //Busca as imagens
            $galerias = $this->getEm()->getRepository('Entity\Galeria')->getGaleriaIds($ids);

            //Percorre e organiza o HTML da listagem
            $tag = $this->getTag();
            $html = "";

            foreach ($galerias as $galeria) {
                $checked = isset($arrayRelacoes[$galeria->getId()]) ? $arrayRelacoes[$galeria->getId()] : 1;

                $html .= "<div id='galeria{$galeria->getId()}' class='photo'>";
                $html .= $tag->h4($galeria->getLabel());
                $html .= "<br />";
                $html .= "<strong>Posicionar galeria: </strong><br />";

                if ($checked == 1) {
                    $html .= "<label><input type='radio' name='posicao{$galeria->getId()}' value='1' checked /> No início da página  </label> ";
                    $html .= "<label><input type='radio' name='posicao{$galeria->getId()}' value='2' /> Ao final da página  </label><br />";
                } else {
                    $html .= "<label><input type='radio' name='posicao{$galeria->getId()}' value='1' /> No início da página  </label> ";
                    $html .= "<label><input type='radio' name='posicao{$galeria->getId()}' value='2' checked /> Ao final da página  </label><br />";
                }
                $html .= "<a href='javascript:excluirGaleria({$galeria->getId()})' class='btn btn3 btn_trash'></a>";
                $html .= "</div>";
            }

            return $html;
        } else {
            return '';
        }
    }

    public function validaSubsiteVinculadoPaginaEstatica()
    {
        $repository = $this->getEm()->getRepository('Entity\PaginaEstatica');

        $retorno = $repository->validaVinculo($_REQUEST['id'], 'pagina_estatica');

        return json_encode(array('permissao' => $retorno));
    }

    //Compartilha registro com subsites selecionados
    public function compartilhar()
    {
        $error = array();

        try {
            if (!empty($_REQUEST['sites'])) {

                $sites = $_REQUEST['sites'];

                $connection = $this->getEm()->getConnection();

                foreach ($_SESSION['user']['subsites'] as $site) {
                    $connection->query("DELETE FROM tb_pagina_estatica_site WHERE id_pagina_estatica = {$_REQUEST['id']} AND id_site = {$site}");
                }

                foreach ($sites as $site) {
                    $statment = $connection->prepare("INSERT INTO tb_pagina_estatica_site (id_pagina_estatica, id_site) VALUES({$_REQUEST['id']}, $site)");

                    $statment->execute();
                }
                $response = 1;
                $success = "Registro compartilhado com sucesso";
            } else {
                $response = 1;
                $success = "Registro descompartilhado com sucesso";
                $this->getService()->deletaCompartilhadosById($_REQUEST['id'], 'pagina_estatica');
            }

        } catch(\Exception $exc) {
            $error[] = "Não foi possível executar esta ação";
        }

        return json_encode(array('error' => $error, 'response' => $response, 'success' => $success));
    }

}
