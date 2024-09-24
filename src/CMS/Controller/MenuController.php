<?php

namespace CMS\Controller;

use LibraryController\CrudControllerInterface;
use CMS\Service\ServiceRepository\Menu as MenuService;
use Entity\Menu as MenuEntity;
use Entity\Type;
use Helpers\Http;
use Helpers\Param;

/**
 * Description of MenuController
 */
class MenuController extends CrudController implements CrudControllerInterface
{

    const PAGE_TITLE = 'Gerenciamento de menu';
    const DEFAULT_ACTION = 'lista';

    protected $title = self::PAGE_TITLE;
    protected $defaultAction = self::DEFAULT_ACTION;

    /**
     * Rota para o controller.
     */
    protected $controllerRoute = 'menu';

    /**
     * Nome da entidade
     */
    protected $entityNamespace = 'Entity\Menu';

    /**
     * Name do checkbox utilizado para seleção multipla.
     */
    protected $selectionCheckboxName = 'itensMenu';

    /**
     *
     * @var MenuService
     */
    private $service;

    /**
     *
     * @var MenuEntity
     */
    private $entity;

    /**
     *
     * @param string $title
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }

    /**
     *
     * @return \MenuService
     */
    public function getService()
    {
        if (empty($this->service)) {
            $this->setService(new MenuService($this->getEm(), $this->getEntity(), $this->getSession()));
        }
        return $this->service;
    }

    /**
     *
     * @return MenuEntity
     */
    public function getEntity()
    {
        if (empty($this->entity)) {
            $this->setEntity(new MenuEntity());
        }
        return $this->entity;
    }

    /**
     *
     * @param MenuService $service
     */
    public function setService(MenuService $service)
    {
        $this->service = $service;
    }

    /**
     *
     * @param MenuEntity $entity
     */
    public function setEntity(MenuEntity $entity)
    {
        $this->entity = $entity;
    }

    /**
     *
     * @return string
     */
    public function getDefaultAction()
    {
        return $this->defaultAction;
    }

    /**
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     *
     * @return \Template\TemplateAmanda
     */
    public function lista()
    {
        $status = array();
        $status[] = new Type('0', 'Não publicado');
        $status[] = new Type('1', 'Publicado');

        $subsite = array();
        $subsites = $this->getEm()
                ->createQuery('SELECT e FROM Entity\Site e ORDER BY e.nome ASC ')
                ->getResult();

        $subsite[] = new \Entity\Type('', 'Selecione o Site');
        
        

        foreach ($subsites as $e) {
            if (in_array($e->getId(), $_SESSION['user']['subsites'])) {
                $subsite[] = new Type($e->getId(), $e->getNome());
            }
        }
        $tipoMenu = $this->getTiposMenu();
        
        $vinculoPai = $this->getVinculoPai();

        $this->tpl->renderView(
                array(
                    'titlePage' => $this->getTitle(),
                    'status' => $status,
                    'selTipoMenu' => $tipoMenu,
                    'vinculoPai' => $vinculoPai,
                    'selSubsite' => $subsite,
                )
        );

        return $this->getTpl()->output();
    }

    /**
     * Valida a ordenação de duas listas de ordenações
     *
     * @param arrray $newOrdenation Somente os itens que tiveram sua ordenação alterada (info. POST/GET).
     * @param array $oldOrdenation Itens que estavam armazenados no banco de dados.
     * @return array|true Itens inválidos (iguais quando não poderiam) ou verdadeiro se válido.
     */
    public function validateOrdenation($newOrdenation, $oldOrdenation)
    {
        $equal_ids = array();

        // para cada item alterado
        foreach ($newOrdenation as $nkey => $nOrdenation) {
            // compara com um item no banco de dados
            foreach ($oldOrdenation as $oOrdenation) {
                $okey = $oOrdenation->getId();
                $newOrdenationEntity = $this->getEm()->getRepository('Entity\Menu')->find($nkey);

                // ** CONDIÇÕES **
                // Mesma categoria/tipo
                $sameCategory = ($newOrdenationEntity->getTipoMenu() == $oOrdenation->getTipoMenu());
                // Mesmo subsite
                $sameSite = ($newOrdenationEntity->getSite()->getId() == $oOrdenation->getSite()->getId());
                // Mesma ordenação
                $sameOrdenation = ($nOrdenation == $oOrdenation->getOrdem());
                // Não veio em branco
                $notEmpty = (!empty($nOrdenation));

                // se a ordenação é igual e não é o mesmo item (mesmo id)
                if ($sameOrdenation && $nkey != $okey && $sameCategory && $notEmpty) {
                    // se o item não foi alterado
                    if (!in_array($okey, array_keys($newOrdenation))) {
                        // adiciona as duas chaves na lista de elementos iguais
                        if (!in_array($nkey, $equal_ids)) {
                            $equal_ids[] = $nkey;
                        }
                        if (!in_array($okey, $equal_ids)) {
                            $equal_ids[] = $okey;
                        }
                    }
                }
            }
        }

        // para cada item alterado
        foreach ($newOrdenation as $nkey => $nOrdenation) {
            // comparar com seus irmãos (itens que também foram alterados)
            foreach ($newOrdenation as $nbkey => $nBrotherOrdenation) {
                $newOrdenationEntity = $this->getEm()->getRepository('Entity\Menu')->find($nkey);
                $newBrotherOrdenationEntity = $this->getEm()->getRepository('Entity\Menu')->find($nbkey);

                // ** CONDIÇÕES **
                // Mesma categoria/tipo
                $sameCategory = ($newOrdenationEntity->getTipoMenu() == $newBrotherOrdenationEntity->getTipoMenu());
                // Mesmo site
                $sameSite = ($newOrdenationEntity->getSite()->getId() == $newBrotherOrdenationEntity->getSite()->getId());
                // Mesma ordenação
                $sameOrdenation = ($nOrdenation == $nBrotherOrdenation);
                // Não veio em branco
                $notEmpty = (!empty($nOrdenation) && !empty($nBrotherOrdenation));

                // se a ordenação é igual e não é o mesmo item (mesmo id)
                if ($sameOrdenation && $nkey != $nbkey && $notEmpty && $sameCategory && $sameSite) {
                    // adiciona as duas chaves na lista de elementos iguais
                    if (!in_array($nkey, $equal_ids)) {
                        $equal_ids[] = $nkey;
                    }
                    if (!in_array($nbkey, $equal_ids)) {
                        $equal_ids[] = $nbkey;
                    }
                }
            }
        }

        if (count($equal_ids) == 0) {
            return TRUE;
        } else {
            return $equal_ids;
        }
    }

    /**
     * @return string JSON
     */
    public function ajaxAtualizarOrdenacao()
    {
        if(!$this->verifyPermission('MENUS_ALTPOS')){
            die('Acesso negado');
        }

        $paramOrdenation = $this->getParam()->get('ordenacao');
        $newOrdenation = array();
        foreach ($paramOrdenation as $item) {
            $newOrdenation[$item['id']] = $item['ordenacao'];
        }
        $repository = $this->getEm()->getRepository('Entity\Menu');
//        $oldOrdenation = $this->getEm()->getRepository('Entity\Menu')->findAll();
//        $resultado = $this->validateOrdenation($newOrdenation, $oldOrdenation);

//        if ($resultado === TRUE) {
            // Atualiza as entidades
            foreach ($newOrdenation as $id => $ordenacao) {
                // Se vazio
                if (empty($ordenacao)) {
                    $ordenacao = NULL;
                }
                $entity = $repository->find($id);
                $entity->setOrdem($ordenacao);
                $this->getEm()->persist($entity);
            }

            // Salva
            $this->getEm()->flush();

            return json_encode(array(
                'resultado' => 'ok',
            ));
//        } else {
//            return json_encode(array(
//                'resultado' => 'erro',
//                'equals' => $resultado,
//            ));
//        }
    }

    /**
     *
     * @param string $tipoMenu
     * @return string
     */
    public function tipoMenuParaDescricao($tipoMenu)
    {
        switch ($tipoMenu) {
            case 'n1':
                return 'Menu 1º Nível';
            case 'n2':
                return 'Menu 2º Nível';
            case 'n3':
                return 'Menu 3º Nível';
            case 'aux':
                return 'Menu Auxiliar';
        }
    }

    /**
     *
     * @return string JSON
     */
    public function pagination()
    {
        $tag = $this->getTag();
        $param = $this->getParam();
        $dados = array();
        $repository = $this->getEm()->getRepository($this->entityNamespace);
        $filtros = array(
            'status' => $param->get('status'),
            'tipoMenu' => $param->get('tipoMenu'),
            'subsite' => $param->get('subsite'),
            'busca' => $param->get('sSearch'),
            'vinculoPai' => $param->get('vinculoPai'),
            'data_inicial' => $this->getDatetimeFomat()->formatUs($param->get('data_inicial')),
            'data_final' => $this->getDatetimeFomat()->formatUs($param->get('data_final')),
        );
        
        if ($param->get('subsite') == "") {
            $filtros['subsite'] = implode(",", $_SESSION['user']['subsites']);
        }
        
        $registros = $repository->getBusca($param->getInt("iDisplayLength"), $param->getInt("iDisplayStart"), $filtros);
        
        $x = 0;
        foreach ($registros as $registro) {
            $linha = array();

            $linha[] = $this->getFields()->checkbox("{$this->selectionCheckboxName}[]", $registro->getId());
            
            if ($this->verifyPermission('MENUS_ALTERAR')) {
                $linha[] = $tag->link($tag->h4($registro->getLabel()), array('href' => "{$this->controllerRoute}/form/" . $registro->getId()));
            } else {
                $linha[] = $tag->h4($registro->getLabel());
            }
            
            $linha[] = '<span class="tipoMenu">' . $this->tipoMenuParaDescricao($registro->getTipoMenu()) . '</span>';
//            $linha[] = $registro->getOrdem().'<input data-id="' . $registro->getId() . '" type="hidden" value="' . $registro->getOrdem() . '" name="ordenacao_registro" class="ordenacao_registro">';
            
            $urlExterna = $registro->getUrlExterna();
            if (empty($urlExterna)) {
                $linha[] = Http::generateUrl($registro);
            }
            else if (!empty($urlExterna)) {
                $linha[] = $registro->getUrlExterna();
            }

            $linha[] = $registro->getPublicado() ? $tag->span('Publicado', array('class' => 'publicado')) : $tag->span('Não publicado', array('class' => 'naoPublicado'));

            $dados[] = $linha;
            $x++;
        }

        $retorno['sEcho'] = $this->getParam()->getInt('sEcho');
        $retorno['iTotalDisplayRecords'] = $repository->getTotal($filtros);
        $retorno['iTotalRecords'] = $repository->countAll();
        $retorno['aaData'] = $dados;

        return json_encode($retorno);
    }

    /**
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $data
     * @return \Entity\Type
     */
    private function resultsToType($data)
    {
        $array = array();

        foreach ($data as $row) {
            $array[] = new Type($row->getId(), $row->getLabel());
        }

        return $array;
    }

    /**
     *
     * @param string $n
     * @return string|null
     */
    private function ancestral($n)
    {
        if ($n == 'n1') {
            return NULL;
        }
        if ($n == 'n2') {
            return 'n1';
        }
        if ($n == 'n3') {
            return 'n2';
        }
    }

    /**
     *
     * @return array
     */
    private function getTiposMenu()
    {
        return array(
            new Type('n1', 'Menu 1º nível (principal)'),
            new Type('n2', 'Menu 2º nível'),
            new Type('n3', 'Menu 3º nível'),
            new Type('aux', 'Menu auxiliar'),
            new Type('lr', 'Links Rápidos'),
            //new Type('ss', 'Menu Subsite'),
        );
    }
    
    /**
     *
     * @return array
     */
    private function getVinculoPai($id = null)
    {
        $array = array();
        $array[] = new Type(0, 'Vinculo Pai');
        return $array;
    }
    
    /**
     *
     * @param int $id
     * @return \Template\TemplateAmanda
     */
    public function form($id = 0)
    {
        $entity = NULL;

        // Verifica o Id para passar o título
        if ($id == 0) {
            $this->setTitle(self::PAGE_TITLE . ' - Inserir');
        } else {
            $entity = $this->getEm()
                    ->getRepository($this->getService()->getNameEntity())
                    ->find($id);
            
            if ($entity === NULL) {
                throw new \Exception("Registro não encontrado.");
            }

            $this->setTitle(self::PAGE_TITLE . ' - Alterar');
        }

        // Tipo menu
        $valCategorias = $this->getTiposMenu();

        $valAbrirEm = array(
            new Type('n', 'Nova página'),
            new Type('m', 'Mesma página'),
        );

        // Vínculo
        $vinculoPai = is_object($entity) ? $entity->getVinculoPai() : NULL;
        if ($id > 0 && !empty($vinculoPai)) {
            $ancestral = $this->ancestral($entity->getTipoMenu());
            $vinculosData = $this->getOpcoesVinculoPai($ancestral, $entity->getSite(), $entity->getId());
            $valVinculos = $this->resultsToType($vinculosData);
        } else {
            $valVinculos = array(
                new Type('', 'Selecione'),
            );
        }

        // Funcionalidades
        $bdFuncionalides = $this->getEm()
                ->getRepository('Entity\FuncionalidadeMenu')
                ->createQueryBuilder('f')
                ->orderBy('f.funcionalidade', 'ASC')
                ->getQuery()
                ->getResult();
        $valFuncionalidades = $this->resultsToType($bdFuncionalides);

        $funcionalidadeDefault = new Type();
        $funcionalidadeDefault->setId('');
        $funcionalidadeDefault->setNome('Selecione');
        
        array_unshift($valFuncionalidades, $funcionalidadeDefault);

        //array_unshift($valFuncionalidades, new Type('', 'Selecione')); 
        // $sigla = $entity->getSite()->getSigla();

        // html páginas
        $htmlPaginas = "";
        if ($entity) {
            $htmlPaginas = $this->getHtmlPagina($entity->getIdEntidade());
        }

        // Subsites
        // $bdSubsites = $this->getEm()
        //         ->getRepository('Entity\Site')
        //         ->getSites($this->getSession());

        // $valSubsites = $this->resultsToType($bdSubsites);
        // array_unshift($valSubsites, new Type('', 'Selecione'));

        $subsite = array();
        $subsites = $this->getEm()
                ->createQuery('SELECT e FROM Entity\Site e ORDER BY e.nome ASC ')
                ->getResult();

        $subsite[] = new \Entity\Type('', 'Selecione');
        
        

        foreach ($subsites as $e) {
            if (in_array($e->getId(), $_SESSION['user']['subsites'])) {
                $subsite[] = new Type($e->getId(), $e->getNome());
            }
        }

        // // html subsite
        // $htmlSubsites = "";
        // if($entity and is_array($entity)){
        //     if($entity and $entity->getFuncionalidadeMenu()->getFuncionalidade() == "Subsite"){
        //         $htmlPaginas = "";
        //         $htmlSubsites = $this->getHtmlSubsite($entity->getIdEntidade());
        //     }
        // } else {
        //     if($entity){
        //         if($entity->getFuncionalidadeMenu() and $entity->getFuncionalidadeMenu()->getFuncionalidade() == "Subsite"){
        //             $htmlPaginas = "";
        //             $htmlSubsites = $this->getHtmlSubsite($entity->getIdEntidade());
        //         }
        //     }

        // }
        
        $this->getTpl()->addJS('/menu/paginasEstaticas.js');
        $this->getTpl()->addJS('/menu/subsite.js');

        $this->getTpl()->renderView(
                array(
                    'valCategorias' => $valCategorias,
                    'valFuncionalidades' => $valFuncionalidades,
                    'valVinculos' => $valVinculos,
                    'valAbrirEm' => $valAbrirEm,
                    'valSubsites' => $subsite,
                    'htmlPaginas' => $htmlPaginas,
                    'htmlSubsites' => $htmlSubsites,
                    'data' => new \DateTime('now'),
                    'entity' => isset($entity) ? $entity : NULL,
                    'method' => 'POST',
                    'titlePage' => $this->getTitle(),
                )
        );

        return $this->getTpl()->output();
    }

    /**
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $results
     * @return array
     */
    private function toArray($results)
    {
        $values = array();

        foreach ($results as $row) {
            $values[] = $row->toArray();
        }

        return $values;
    }

    /**
     *
     * @param string $ancestral
     * @param int $entity
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    private function getOpcoesVinculoPai($ancestral, $subsite, $id = NULL)
    {
        $query = $this->getEm()
                      ->createQueryBuilder()
                      ->select('m')
                      ->from('Entity\Menu', 'm')
                      ->where('m.tipoMenu = :menuTipo')
                      ->setParameter('menuTipo', $ancestral)
                      ->andWhere('m.site = :menuSite')
                      ->setParameter('menuSite', $subsite);

        if ($id !== NULL) {
            $query->andWhere('m.id != :menuId')
                  ->setParameter('menuId', $id);
        }

        return $query->getQuery()->getResult();
    }

    /**
     *
     * @return string
     */
    public function ajaxVinculo()
    {
        header('Content-Type: application/json');

        $pTipoMenu = $this->getParam()->get('tipoMenu');
        $pSubsite = $this->getParam()->getInt('subsite');
        $pId = $this->getParam()->getInt('id');
        $results = $this->getOpcoesVinculoPai($pTipoMenu, $pSubsite, $pId);

        return json_encode(array(
            'values' => $this->toArray($results)
        ));
    }

    /**
     *
     * @return array
     */
    protected function getDados()
    {
        $dataInicial = $this->getDatetimeFomat()->formatUs($this->getParam()->get('dataInicial'));
        $horaInicial = $this->getParam()->get('horaInicial');
        $dataFinal = $this->getParam()->get('dataFinal');
        $horaFinal = $this->getParam()->get('horaFinal');

        $dados = array(
            'id' => $this->getParam()->getInt('id'),
            'dataInicial' => new \DateTime($dataInicial . " " . $horaInicial),
            'tipoMenu' => $this->getParam()->get('tipoMenu'),
            'vinculoPai' => $this->getParam()->getInt('vinculoPai'),
            'abrirEm' => $this->getParam()->get('abrirEm'),
            'site' => $this->getParam()->getInt('site'),
            'titulo' => $this->getParam()->get('titulo'),
            'funcionalidadeMenu' => $this->getParam()->getInt('funcionalidadeMenu'),
            'idEntidade' => $this->getParam()->getInt('idEntidade'),
            'urlExterna' => $this->getParam()->get('urlExterna'),
            'site' => $this->getParam()->get('site'),
        );
        
        // se a dataFinal não estiver setada ou receber o valor vazio
        // a variável deve ser setada como NULA
        if (!empty($dataFinal) && !empty($horaFinal)) {
            $dados['dataFinal'] = new \DateTime($this->getDatetimeFomat()->formatUs($dataFinal) . " " . $horaFinal);
        } else {
            $dados['dataFinal'] = null;
        }

        return $dados;
    }

    /**
     *
     * @return JSON
     */
    public function salvar()
    {
        $dados = $this->getDados();
     
        return json_encode($this->getService()->save($dados));
    }

    public function getHtmlSubsite($ids, \Doctrine\Common\Collections\Collection $sites = null)
    {
        //Verifica se os ids foram passados
        if (!empty($ids)) {

            if (is_null($sites)) {
                $sites = $this->getEm()->getRepository("Entity\Site")->getSiteIds($ids);
            }

            //Monta e retorna o html
            $tag = $this->getTag();
            $html = "";

            foreach ($sites as $s) {
                $linkExcluir = $tag->link("Desfazer seleção", array('href' => "javascript:excluirSubsite({$s->getId()})"));
                $atributos = array('id' => 'subsiteSel' . $s->getId(), 'class' => 'photo');
                $html .= $tag->div($tag->h4($s->getSigla()) . "<br />" . $linkExcluir, $atributos);
            }

            return $html;
        } else {
            return '';
        }
    }
    
    public function getHtmlPagina($ids, \Doctrine\Common\Collections\Collection $paginas = null)
    {
        //Verifica se os ids foram passados
        if (!empty($ids)) {

            //Verifica se um objeto com as galerias já foi passado
            if (is_null($paginas)) {
                //Busca as galerias
                $paginas = $this->getEm()->getRepository("Entity\PaginaEstatica")->getPaginaIds($ids);
            }

            //Monta e retorna o html
            $tag = $this->getTag();
            $html = "";

            foreach ($paginas as $pag) {
                $linkExcluir = $tag->link("Desfazer seleção", array('href' => "javascript:excluirPagina({$pag->getId()})"));
                $atributos = array('id' => 'paginaSel' . $pag->getId(), 'class' => 'photo');
                $html .= $tag->div($tag->h4($pag->getLabel()) . "<br />" . $linkExcluir, $atributos);
                //$html .= "<input type='hidden' value='{$pag->getId()}' name='idPaginaValue' />";
            }

            return $html;
        } else {
            return '';
        }
    }

    /**
     *
     * @return html
     */
    public function paginas()
    {
        return $this->getTpl()->renderView(array());
    }

    /**
     *
     * @return html
     */
    public function subsites()
    {
        return $this->getTpl()->renderView(array());
    }
    
    /**
     *
     * @return String
     */
    public function paginacaoPaginas()
    {
        //Instancia o repository
        $em = $this->getEm();
        $repoPaginas = $em->getRepository("Entity\PaginaEstatica");

        //Armazena a busca de parametro
        $param = $this->getParam();

        //Busca o total
        $total = $repoPaginas->countAll($this->getSession()->get('user'));

        //Faz a busca
        $paginas = $repoPaginas->getBuscaPaginaEstatica($param->getInt("iDisplayLength"), $param->getInt("iDisplayStart"), array("busca" => $param->get('sSearch'), "status" => ''), $this->getSession()->get('user'));
       

        //Busca o total de registros
        $totalFiltro = $repoPaginas->getTotalBuscaPaginaEstatica(array("busca" => $param->get('sSearch'), "status" => ''), $this->getSession()->get('user'));

        //Organiza os ids em array
        $ids = explode(',', $param->get('ids'));
        $arrayIds = array();

        foreach ($ids as $id) {
            $arrayIds[$id] = $id;
        }

        //Percorre e organiza o HTML da listagem
        $dados = array();
        $tag = $this->getTag();

        foreach ($paginas as $pagina) {
            $linha = array();
            $checked = isset($arrayIds[$pagina->getId()]) ? "checked" : "";

            $linha[] = "<input type='radio' name='pagina' value={$pagina->getId()} class='marcar' {$checked} />";

            $linha[] = $tag->h4($pagina->getLabel()) . $pagina->getDataCadastro()->format('d/m/Y') . " as " . $pagina->getDataCadastro()->format('H:i');
            
            $linha[] = $pagina->getPublicado() ? "Publicado" : "Não Publicado";

            $dados[] = $linha;
        }

        //Organiza o retorno e retorna via json
        $retorno['sEcho'] = Param::getInt("sEcho");
        $retorno['iTotalDisplayRecords'] = $totalFiltro;
        $retorno['iTotalRecords'] = $total;
        $retorno['aaData'] = $dados;
        
        return json_encode($retorno);
    }

    
    /**
     *
     * @return String
     */
    public function paginacaoSubsites()
    {
        //Instancia o repository
        $em = $this->getEm();
        $repoSubsites = $em->getRepository("Entity\Site");

        //Armazena a busca de parametro
        $param = $this->getParam();

        //Busca o total
        $total = $repoSubsites->countAll($this->getSession()->get('user'));

        //Faz a busca
        //$paginas = $repoPaginas->getBuscaPaginaEstatica($param->getInt("iDisplayLength"), $param->getInt("iDisplayStart"), array("busca" => $param->get('sSearch'), "status" => ''), $this->getSession()->get('user'));
        $subsites = $repoSubsites->getSites($this->getSession());
        
        //Busca o total de registros
        //$totalFiltro = $repoSubsites->getTotalBuscaPaginaEstatica(array("busca" => $param->get('sSearch'), "status" => ''), $this->getSession()->get('user'));
        $totalFiltro = count($subsites);

        //Organiza os ids em array
        $ids = explode(',', $param->get('ids'));
        $arrayIds = array();

        foreach ($ids as $id) {
            $arrayIds[$id] = $id;
        }

        //Percorre e organiza o HTML da listagem
        $dados = array();
        $tag = $this->getTag();

        foreach ($subsites as $subsite) {
            $linha = array();
            $checked = isset($arrayIds[$subsite->getId()]) ? "checked" : "";

            $linha[] = "<input type='radio' name='subsite' value={$subsite->getId()} class='marcar' {$checked} />";

            $linha[] = $tag->h4($subsite->getLabel()) . $subsite->getDataCadastro()->format('d/m/Y') . " as " . $subsite->getDataCadastro()->format('H:i');
            $linha[] = $subsite->getPublicado() ? "Publicado" : "Não Publicado";

            $dados[] = $linha;
        }

        //Organiza o retorno e retorna via json
        $retorno['sEcho'] = Param::getInt("sEcho");
        $retorno['iTotalDisplayRecords'] = $totalFiltro;
        $retorno['iTotalRecords'] = $total;
        $retorno['aaData'] = $dados;

        return json_encode($retorno);
    }
    
    /**
     *
     * @return String
     */
    public function ajaxVinculoPai()
    {
        $tipoMenu = $this->ancestral($this->getParam()->get('tipoMenu'));
        $subsite = $this->getParam()->get('subsite');
        
        $query = $this->getEm()
                ->createQueryBuilder()
                ->select('m.id', 'm.titulo')
                ->from('Entity\Menu', 'm')
                ->where('m.tipoMenu = :menuTipo')
                ->setParameter('menuTipo', $tipoMenu)
                ->andWhere('m.site = :menuSite')
                ->setParameter('menuSite', $subsite);

        return json_encode($query->getQuery()->getResult());
    }

}
