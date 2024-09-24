<?php

namespace CMS\Controller;

use LibraryController\CrudControllerInterface;
use CMS\Service\ServiceRepository\BannerGeral as BannerGeralService;
use Entity\BannerGeral as BannerEntity;
use Entity\Type;

/**
 * Description of GerenciadorBanner
 */
class GerenciadorBannerController extends CrudController implements CrudControllerInterface
{

    const PAGE_TITLE = "Gerenciador de Banners";
    const DEFAULT_ACTION = "lista";

    protected $title = self::PAGE_TITLE;
    protected $defaultAction = self::DEFAULT_ACTION;

    /**
     *
     * @var BannerGeralService
     */
    private $service;

    /**
     *
     * @var BannerEntity
     */
    private $entity;

    /**
     *
     * @var array
     */
    private $user;

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
     * @return \CMS\Service\ServiceRepository\BannerGeral
     */
    public function getService()
    {
        if (empty($this->service)) {
            $this->setService(new BannerGeralService($this->getEm(), $this->getEntity(), $this->getSession()));
        }
        return $this->service;
    }

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
     * @return \Entity\BannerGeral
     */
    public function getEntity()
    {
        if (empty($this->entity)) {
            $this->setEntity(new BannerEntity());
        }
        return $this->entity;
    }

    /**
     *
     * @param \CMS\Service\ServiceRepository\BannerGeral $service
     */
    public function setService(BannerGeralService $service)
    {
        $this->service = $service;
        $this->service->setSession($this->getSession());
    }

    /**
     *
     * @param \Entity\BannerGeral $entity
     */
    public function setEntity(BannerEntity $entity)
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
     * @return JSON
     */
    public function delete()
    {
        $array = $this->getParam()->getArray("sel");
        $retorno = $this->getService()->delete($array);

        return json_encode($retorno);
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
     * @param int $id
     * @return \Template\TemplateAmanda
     */
    public function form($id = 0)
    {
        // Adiciona o css e o js da seleção de imagens
        $this->tpl->addJS('/imagem/imagens.js');
        $this->tpl->addCSS('/imagem/imagens.css');
        $this->tpl->addJS('/gerenciadorBanner/paginasEstaticas.js');

        // Verifica o id para passar o título
        if ($id == 0) {
            $this->setTitle(self::PAGE_TITLE . " - Inserir");
        } else {
            $this->setTitle(self::PAGE_TITLE . " - Alterar");
        }

        // Inicializa a entidade com base nas permissões de acesso do usuário
        if ($this->user['sede']) {
            $entity = $this->getEm()
                ->getRepository($this->getService()->getNameEntity())
                ->find($id);
        } else {
            $entity = $this->getEm()
                ->getRepository($this->getService()->getNameEntity())
                ->findByIdSite($id, $this->user['subsites']);
        }

        // Funcionalidades do menu
        $bdFuncionalidades = $this->getEm()
            ->getRepository('Entity\FuncionalidadeMenu')
            ->createQueryBuilder('f')
            ->orderBy('f.funcionalidade', 'ASC')
            ->getQuery()
            ->getResult();
        array_unshift($bdFuncionalidades, new Type('', 'Selecione'));

        // HTML páginas
        $htmlPaginas = "";
        if ($entity) {
            $htmlPaginas = $this->getHtmlPagina($entity->getIdEntidade());
        }

        $idImagem = ($entity) ? $entity->getImagem()->getId() : NULL;
        $categorias = $this->getEm()
            ->getRepository('Entity\BannerGeralCategoria')
            ->findAllBySite($this->user);
        $abrirEm = array(
            new Type('n', 'Abrir em uma nova janela'),
            new Type('m', 'Abrir na mesma janela'),
        );
#Jeito antigo
//        if ($this->user['sede']) {
//            $sites = $this->getEm()->getRepository("Entity\Site")->findBy(array(), array('nome' => 'ASC'));
//        } else {
//            $sites = $this->getEm()
//                    ->getRepository("Entity\Site")
//                    ->findIn($this->user['subsites']);
//        }

        if($entity){
            $categoriaBanner = $entity->getCategoria();
            $id_categoria_banner = $categoriaBanner->getId();
        }
        $aux = $this->buscaSubsitesPermissionadosBannerInterno($id_categoria_banner,$id,NULL);
        $sites = $aux["subsites"];

//        echo "<pre>";
//        \Doctrine\Common\Util\Debug::dump($aux["subsites"]);
//        echo "</pre>";
//        die();

        $this->getTpl()->renderView(
            array(
                "data" => new \DateTime("now"),
                "hora" => new \DateTime("now"),
                "entity" => $entity,
                "method" => "POST",
                'valFuncionalidades' => $bdFuncionalidades,
                "htmlPaginas" => $htmlPaginas,
                "titlePage" => $this->getTitle(),
                "formOptions" => array(
                    "categorias" => $categorias,
                    "sites" => $sites,
                    "abrirEm" => $abrirEm,
                ),
                "formValues" => array(
                    "imagem" => $this->getHtmlImagens($idImagem)
                ),
            )
        );

        return $this->getTpl()->output();
    }

    /**
     * Retorna verdadeiro caso as duas listas tenham algum site em comum.
     * @param array $nSites IDs dos sites.
     * @param array $oSites IDs dos sites.
     * @return boolean
     */
    private function siteInCommon($nSites, $oSites)
    {
        foreach ($nSites as $site) {
            if (in_array($site, $oSites)) {
                return true;
            }
        }

        return false;
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
                $newOrdenationEntity = $this->getEm()->getRepository('Entity\BannerGeral')->find($nkey);

                // ** CONDIÇÕES **
                // Mesma categoria/tipo
                $sameCategory = ($newOrdenationEntity->getCategoria()->getLabel() == $oOrdenation->getCategoria()->getLabel());
                // Mesmo subsite
                $nSites = array(); foreach ($newOrdenationEntity->getSites() as $site) { $nSites[] = $site->getId(); };
                $oSites = array(); foreach ($oOrdenation->getSites() as $site) { $oSites[] = $site->getId(); };
//                $sameSite = ($newOrdenationEntity->getSite()->getId() == $oOrdenation->getSite()->getId());
                $sameSite = $this->siteInCommon($nSites, $oSites);
                // Mesma ordenação
                $sameOrdenation = ($nOrdenation == $oOrdenation->getOrdem());
                // Não veio em branco
                $notEmpty = (!empty($nOrdenation));

                // se a ordenação é igual e não é o mesmo item (mesmo id)
                if ($sameOrdenation && $nkey != $okey && $notEmpty && $sameCategory && $sameSite) {
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
//                $notEmpty = (!empty($nOrdenation) && !empty($nBrotherOrdenation));
                $newOrdenationEntity = $this->getEm()->getRepository('Entity\BannerGeral')->find($nkey);
                $brotherEntity = $this->getEm()->getRepository('Entity\BannerGeral')->find($nbkey);

                // ** CONDIÇÕES **
                // Mesma categoria/tipo
                $sameCategory = ($newOrdenationEntity->getCategoria()->getLabel() == $brotherEntity->getCategoria()->getLabel());
                // Mesmo subsite
//                $sameSite = ($newOrdenationEntity->getSite()->getId() == $oOrdenation->getSite()->getId());
                $nSites = array(); foreach ($newOrdenationEntity->getSites() as $site) { $nSites[] = $site->getId(); };
                $oSites = array(); foreach ($brotherEntity->getSites() as $site) { $oSites[] = $site->getId(); };
                $sameSite = $this->siteInCommon($nSites, $oSites);
                // Mesma ordenação
//                $sameOrdenation = ($nOrdenation == $oOrdenation->getOrdem());
                // Não veio em branco
                $notEmpty = (!empty($nOrdenation));

                // se a ordenação é igual e não é o mesmo item (mesmo id)
                if ($nOrdenation == $nBrotherOrdenation && $nkey != $nbkey && $notEmpty) {
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
        if (!$this->verifyPermission('BANNE_ALTERAR')) {
            die('Acesso negado');
        }

        $requestOrdenation = $this->getParam()->get('ordenacao');
        $newOrdenation = array();
        foreach ($requestOrdenation as $item) {
            $newOrdenation[$item['id']] = $item['ordenacao'];
        }
        $oldOrdenation = $this->getEm()->getRepository('Entity\BannerGeral')->findAll();
        $repository = $this->getEm()->getRepository('Entity\BannerGeral');
        $resultado = $this->validateOrdenation($newOrdenation, $oldOrdenation);

        if ($resultado === TRUE) {
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
        } else {
            return json_encode(array(
                'resultado' => 'erro',
                'equals' => $resultado,
            ));
        }
    }

    /**
     *
     * @return \Template\TemplateAmanda
     */
    public function lista()
    {
        $sites = array();
        $status = array();
        $status[] = new Type("0", "Não publicado");
        $status[] = new Type("1", "Publicado");

        $aux = $this->buscaSubsitesPermissionadosBannerInterno($id_categoria_banner,$id,"CONSULT");
        $sites = $aux["subsites"];




#Jeito Antigo
//        if ($this->user['sede']) {
//            $sites = $this->getEm()->getRepository("Entity\Site")->findBy(array(), array('nome' => 'ASC'));
//            array_unshift($sites, new \Entity\Type('', 'Selecione o Site'));
//
//        } else {
//            $sites = $this->getEm()->getRepository("Entity\Site")->findIn($this->user['subsites']);
//        }


        #buscar categorias - CONSULT
        $user = $this->getUser();
        if($user['banners']){
            foreach ($user['banners'] as $sub => $s) {
                foreach ($s as $categoria => $c) {
                    if($c["CONSULT"] == "1"){
                        $array_categorias[$categoria] = $categoria;
                    }
                }
            }
        }

        $todas_categorias = $this->getEm()->getRepository('Entity\BannerGeralCategoria')->findAll();
        if($array_categorias){
            foreach ($todas_categorias as $categoria) {
                if($array_categorias[$categoria->getNomeCategoriaSiscau()]) $c[]  = $categoria->getId();
            }
        }


#Jeito Antigo
//        $categorias = $this->getEm()
//                ->getRepository('Entity\BannerGeralCategoria')
//                ->findAllBySite($this->user);
        if($c){
            $categorias = $this->getEm()
                ->getRepository('Entity\BannerGeralCategoria')
                ->findAllById($c);
        }else{
            $categorias = $this->getEm()
                ->getRepository('Entity\BannerGeralCategoria')
                ->findAllBySite($this->user);
        }


        $this->tpl->renderView(
            array(
                'titlePage' => $this->getTitle(),
                'status' => $status,
                'sites' => $sites,
                'categorias' => $categorias,
            )
        );

        return $this->getTpl()->output();
    }

    /**
     *
     * @return JSON
     */
    public function pagination()
    {
        $tag = $this->getTag();
        $param = $this->getParam();
        $dados = array();
        $repository = $this->getEm()
            ->getRepository($this->getService()->getNameEntity());
        $registros = $repository->getResults(
            $param->getInt("iDisplayLength"), $param->getInt("iDisplayStart"), array(
            'status' => $param->get("status"),
            'site' => $param->getInt("site"),
            'categoria' => $param->getInt("categoria"),
            "busca" => $param->get("sSearch"),
            "data_inicial" => $this->getDatetimeFomat()->formatUs($param->get("data_inicial")),
            "data_final" => $this->getDatetimeFomat()->formatUs($param->get("data_final")),
        ), $this->getSession()
        );

//        echo "<pre>";
//            var_dump($registros);
//        echo "</pre>";
//        die();

        if(!$this->podePesquisar($param->getInt("categoria"),$param->getInt("site"))) $registros = array();

        foreach ($registros as $registro) {
            $linha = array();
            $linha[] = $this->getFields()->checkbox("sel[]", $registro->getId());

            if ($this->verifyPermission('BANNE_ALTERAR')) {
                $linha[] = $tag->link($tag->h4($registro->getLabel()), array("href" => "gerenciadorBanner/form/" . $registro->getId())) . $registro->getDataCadastro()->format('d/m/Y') . " as " . $registro->getDataCadastro()->format('H:i');
            } else {
                $linha[] = $tag->h4($registro->getLabel()) . $registro->getDataCadastro()->format('d/m/Y') . " as " . $registro->getDataCadastro()->format('H:i');
            }
            $linha[] = '<input data-id="' . $registro->getId() . '" type="text" value="' . $registro->getOrdem() . '" name="ordenacao_registro" class="ordenacao_registro" style="width: 85%; text-align: center;">';

            $linha[] = $registro->getPublicado() ? $tag->span("Publicado", array('class' => 'publicado st')) : $tag->span("Não publicado", array('class' => 'naoPublicado st'));
            $linha[] = "<a href='javascript:visualizar({$registro->getId()}, {$registro->getCategoria()->getId()})' class='btn btn3 btn_search' ></a>";
            $dados[] = $linha;
        }

        $retorno['sEcho'] = $this->getParam()->getInt("sEcho");
        $retorno['iTotalDisplayRecords'] = $repository->getMaxResult();
        $retorno['iTotalRecords'] = $repository->countAll();
        $retorno['aaData'] = $dados;

        return json_encode($retorno);
    }

    /**
     *
     * @return JSON
     */
    public function salvar()
    {

        #sites vinculados
        $sites_da_sessao = $this->getEm()
            ->getRepository("Entity\Site")
            ->findIn($this->user['subsites']);

        $dados = array(
            'id' => $this->getParam()->getInt('id'),
            'nome' => $this->getParam()->get("nome"),
            'sites' => $this->getParam()->get("sites"),
            'sitesSessao' => $sites_da_sessao,
            'categoria' => $this->getParam()->get("categoria"),
            'imagem' => $this->getParam()->get("imagemBanco"),
            'descricao' => $this->getParam()->getString("descricao"),
            'temLink' => $this->getParam()->getInt("temLink"),
            'url' => $this->getParam()->get("url"),
            'abrirEm' => $this->getParam()->get("abrirEm"),
            'funcionalidadeMenu' => $this->getParam()->get('funcionalidadeMenu'),
            'idEntidade' => $this->getParam()->getInt('idEntidade'),
            'dataInicial' => new \DateTime($this->getDatetimeFomat()->formatUs($this->getParam()->get('dataInicial')) . " " . $this->getParam()->get('horaInicial')),
        );
        $dataFinal = $this->getParam()->get('dataFinal');
        $horaFinal = $this->getParam()->get('horaFinal');

        // Caso a data final seja preenchida, preenche a variável no formato YYYY-MM-DD HH:MM:SS
        // ao contrário, preenche a variável com NULL para poder apagar no banco caso haja alguma data setada na coluna da tabela.
        if (!empty($dataFinal) && !empty($horaFinal)) {
            $dados['dataFinal'] = new \DateTime($this->getDatetimeFomat()->formatUs($dataFinal) . " " . $horaFinal);
        } else {
            $dados['dataFinal'] = null;
        }


        $alterarBanner = $this->getEm()
            ->getRepository('Entity\BannerGeral')
            ->find($this->getParam()->getInt('id'));

        $categoriaSelecionada = $this->getEm()
            ->getRepository('Entity\BannerGeralCategoria')
            ->find($this->getParam()->get("categoria"));
//            echo "<pre>";
//            \Doctrine\Common\Util\Debug::dump( $dados["sites"]);
//            echo "</pre>";
//            die();
        if($categoriaSelecionada and $categoriaSelecionada->getNomeCategoriaSiscau() == "COMUNICACAO"){
//          $dados["sites"] = $this->getEm()->getRepository('Entity\Site')->find('1');
            $dados["sites"] = array("1");

        }

        #validar regra de apenas existir 2 banners publicados para a categoria COMUNICACAO
        if($alterarBanner and $alterarBanner->getPublicado() == "1"){
            $categoria = $alterarBanner->getCategoria();
            $retorno = $this->verificaBannersPublicadosSalvar($this->getParam()->get("categoria"));

            if($categoria->getId() == $this->getParam()->get("categoria")) $retorno = true;
            if($retorno == false){
                $msg[] = "Apenas pode existir dois banners publicados para esta categoria!";
                return json_encode(  array(
                    "success" => $success,
                    "error" => $msg,
                    "response" => 0,
                ));
            }
        }

        return json_encode($this->getService()->save($dados));
    }

    /**
     *
     * @param integer $id
     * @param integer $categoria
     *
     */
    public function visualizar($id, $categoria)
    {
        //Gera o hash
        $hash = $this->getHash();
        $www = preg_match('/^www/', $_SERVER['SERVER_NAME']) ? 'www.' : '';

        //Verifica a categoria para a url que irá redirecionar
        if ($categoria == 6 || $categoria == 7) {
            //Redireciona para a página
            $url = "http://" . $www . URL_PORTAL . "/fototeca/lista/{$id}/{$categoria}/{$hash}";

        } else {
            //Redireciona para a página
            $url = "http://" . $www . URL_PORTAL . "/index/index/{$id}/{$hash}";

        }

        return json_encode(array('url' => $url));
    }

    public function buscaCategoria()
    {
        $id_tipo_banner = $this->getParam()->getInt('tipoBanner');
        $categoria = $this->getEm()
            ->getRepository('Entity\BannerGeralCategoria')
            ->find($id_tipo_banner);

        $podePublicar = true;
        if($categoria and ($categoria->getNome() == utf8_encode('Comunicacao'))){

            $podePublicar = false;

        }

        return json_encode(array('podePublicar' => $podePublicar));
    }


    public function buscaCategoria2()
    {
        $id_tipo_banner = $this->getParam()->getInt('tipoBanner');
        $categoria = $this->getEm()
            ->getRepository('Entity\BannerGeralCategoria')
            ->find($id_tipo_banner);

        $podePublicar = true;
        if($categoria and ($categoria->getNome() == utf8_encode('Comunicacao'))){
            $podePublicar = false;
            $bannersComunicacao = $this->getEm()
                ->getRepository('Entity\BannerGeral')
                ->getBannersComunicacao(null, utf8_encode('Comunicacao'), 2);

            if(!$bannersComunicacao) $podePublicar = true;
        }

        return json_encode(array('podePublicar' => $podePublicar));
    }

    public function ehComunicacao()
    {
        if ($_REQUEST['tipoBanner'] == 6 && $_REQUEST['status'] == 'true') {
            $permission = false;

            $connection = $this->getEm()->getConnection();

            $statment = $connection->query("SELECT count(*) quantity FROM tb_banner_geral bg
                                        INNER JOIN tb_banner_geral_site bgs ON bg.id_banner_geral = bgs.id_banner_geral
                                        WHERE bg.id_banner_geral_categoria = 6 AND bgs.id_site = {$_REQUEST['subsite']} AND bg.st_publicado = 1");
            $rows = $statment->fetchAll();

            if ($rows[0]['quantity'] > 2) {
                $permission = true;
            }

            return json_encode(array('permissao' => $permission, 'comunicacao' => false));
        } else {
            $id_tipo_banner = $this->getParam()->getInt('tipoBanner');
            $categoria = $this->getEm()
                ->getRepository('Entity\BannerGeralCategoria')
                ->find($id_tipo_banner);

            $ehComunicacao = false;
            if($categoria and ($categoria->getNome() == utf8_encode('Comunicacao'))){
                $ehComunicacao = true;
            }

            return json_encode(array('comunicacao' => $ehComunicacao));
        }
    }

    public function verificaBannersPublicadosSalvar($id_categoria)
    {
        $id_tipo_banner = $id_categoria ? $id_categoria : $this->getParam()->getInt('tipoBanner');
        $categoria = $this->getEm()
            ->getRepository('Entity\BannerGeralCategoria')
            ->find($id_tipo_banner);

        $podePublicar = true;
        if($categoria and ($categoria->getNome() == utf8_encode('Comunicacao'))){

            $bannersComunicacao = $this->getEm()
                ->getRepository('Entity\BannerGeral')
                ->getBannersComunicacao(null, utf8_encode('Comunicacao'), 2);

            if(count($bannersComunicacao) >= 2) $podePublicar = false;

        }
        if($id_categoria) return $podePublicar;
        return json_encode(array('podePublicar' => $podePublicar));
    }

    public function verificaBannersPublicados()
    {
        $id_tipo_banner = $id_categoria ? $id_categoria : $this->getParam()->getInt('tipoBanner');
        $categoria = $this->getEm()
            ->getRepository('Entity\BannerGeralCategoria')
            ->find($id_tipo_banner);

        $podePublicar = true;
        if($categoria and ($categoria->getNome() == utf8_encode('Comunicacaoo'))){

            $bannersComunicacao = $this->getEm()
                ->getRepository('Entity\BannerGeral')
                ->getBannersComunicacao(null, utf8_encode('Comunicacaoo'), 2);

//            echo "<pre>";
//                var_dump($bannersComunicacao);
//            echo "</pre>";
//            die();
            if(count($bannersComunicacao) >= 2) $podePublicar = false;

        }
        if($id_categoria) return $podePublicar;
        return json_encode(array('podePublicar' => $podePublicar));
    }


    public function buscaSubsitesPermissionadosBanner()
    {
        $user = $this->getUser();
        if($user["dadosUser"]["login"] != "teste"){
            $id_tipo_banner = $this->getParam()->getInt('tipoBanner');
            $id_banner = $this->getParam()->getInt('idBanner');



            #ALTERAR
            if($id_banner){
                $categoriaSelecionada = $this->getEm()
                    ->getRepository('Entity\BannerGeralCategoria')
                    ->find($id_tipo_banner);
                if($categoriaSelecionada->getNomeCategoriaSiscau() != "COMUNICACAO"){
                    if($user['banners']){
                        foreach ($user['banners'] as $sub => $s) {
                            foreach ($s as $categoria => $c) {
                                if($categoriaSelecionada->getNomeCategoriaSiscau() == $categoria){
                                    if($c["ALTERAR"] == "1") $subsiteSiglas[] = $sub;
                                }
                            }
                        }
                    }
                }
            }else{
                #INSERIR
//                if($user['banners']){
//                    foreach ($user['banners'] as $sub => $s) {
//                        foreach ($s as $categoria => $c) {
//                            if($c['INSERIR'] == "1") $subsiteSiglas[] = $sub;
//                        }
//                    }
//                }

                $categoriaSelecionada = $this->getEm()
                    ->getRepository('Entity\BannerGeralCategoria')
                    ->find($id_tipo_banner);
                if($categoriaSelecionada and $categoriaSelecionada->getNomeCategoriaSiscau() != "COMUNICACAO"){
                    if($user['banners']){
                        foreach ($user['banners'] as $sub => $s) {
                            foreach ($s as $categoria => $c) {
                                if($categoriaSelecionada->getNomeCategoriaSiscau() == $categoria){
                                    if($c["INSERIR"] == "1") $subsiteSiglas[] = $sub;
                                }
                            }
                        }
                    }
                }

            }

//            echo "<pre>";
//            var_dump($subsiteSiglas);
//            echo "</pre>";
//            die();
            #Subsites vinculados ao tipo de banner
            if($subsiteSiglas){
                for($i = 0; $i < count($subsiteSiglas); $i++){
                    $result = $this->getEm()
                        ->getRepository('Entity\Site')
                        ->getSiteBySigla($subsiteSiglas[$i]);
                    $subsitePermissionados[] = $result;
                    $subsiteNome[] = $result->getNome();
                    $subsiteId[] = $result->getId();

                    $array_retorno[$result->getNome()] = $result->getId();
                }
            }

            if($categoriaSelecionada and $categoriaSelecionada->getNomeCategoriaSiscau() == "COMUNICACAO"){
                $array_retorno = "";
                $aux = $result = $this->getEm()
                    ->getRepository('Entity\Site')->findAll();
                foreach ($aux as $value) {
                    $array_retorno[$value->getNome()] = $value->getId();
                }
            }

            #subsites vinculados
            if($id_banner){
                $banner = $this->getEm()
                    ->getRepository('Entity\BannerGeral')->find($id_banner);
                $teste = $banner->getSites();
                if($teste){
                    foreach ($teste as $entidade) {
                        $vinculados[$entidade->getId()] = true;
                    }
                }
            }

            return json_encode(array('login' => true,'subsites' => $array_retorno,'vinculos' => $vinculados));
        }else{

            $array_retorno = "";
            $aux = $result = $this->getEm()
                ->getRepository('Entity\Site')->findAll();
            foreach ($aux as $value) {
                $array_retorno[$value->getNome()] = $value->getId();
            }


            return json_encode(array('login' => false,'subsites' => $array_retorno,'vinculos' => $vinculados));
        }

    }



    public function buscaSubsitesPermissionadosBannerInterno($id_tipo_banner,$id_banner,$tipo_permissao)
    {


        $user = $this->getUser();
        if($user["dadosUser"]["login"] != "teste"){


            if($id_tipo_banner){
                $categoriaSelecionada = $this->getEm()
                    ->getRepository('Entity\BannerGeralCategoria')
                    ->find($id_tipo_banner);
            }

            #ALTERAR
            if($id_banner){
                $tipo_permissao = "ALTERAR";
                if($categoriaSelecionada->getNomeCategoriaSiscau() != "COMUNICACAO"){
                    if($user['banners']){
                        foreach ($user['banners'] as $sub => $s) {
                            foreach ($s as $categoria => $c) {
                                if($categoriaSelecionada->getNomeCategoriaSiscau() == $categoria){
                                    if($c[$tipo_permissao] == "1") $subsiteSiglas[] = $sub;
                                }
                            }
                        }
                    }
                }
            }else{
                $tipo_permissao = !$tipo_permissao ? "INSERIR" : $tipo_permissao;
                if($user['banners']){
                    foreach ($user['banners'] as $sub => $s) {
                        foreach ($s as $categoria => $c) {
                            if($c[$tipo_permissao] == "1") $subsiteSiglas[] = $sub;
                        }
                    }
                }
            }


            #Subsites vinculados ao tipo de banner
            if($subsiteSiglas){
                for($i = 0; $i < count($subsiteSiglas); $i++){
                    $result = $this->getEm()
                        ->getRepository('Entity\Site')
                        ->getSiteBySigla($subsiteSiglas[$i]);
                    if (!empty($result)) {
                        $array_retorno[] = $result->getId();
                    }
                }
            }


            if($categoriaSelecionada && $categoriaSelecionada->getNomeCategoriaSiscau() == "COMUNICACAO"){
                $lista_subsites = $this->getEm()->getRepository("Entity\Site")->findBy(array(), array('nome' => 'ASC'));
            }else{
                if($array_retorno){
                    $lista_subsites = $this->getEm()
                        ->getRepository("Entity\Site")
                        ->findIn($array_retorno);
                }else{
                    //$lista_subsites = $this->getEm()->getRepository("Entity\Site")->findBy(array(), array('nome' => 'ASC'));
                    return array('teste' => "teste",'subsites' => null,'vinculos' => null);
                }
            }

            #subsites vinculados
            if($id_banner){
                $banner = $this->getEm()
                    ->getRepository('Entity\BannerGeral')->find($id_banner);
                $teste = $banner->getSites();
                if($teste){
                    foreach ($teste as $entidade) {
                        $vinculados[$entidade->getId()] = true;
                    }
                }
            }


            return array('teste' => "teste",'subsites' => $lista_subsites,'vinculos' => $vinculados);
        }else{

            $array_retorno = "";
            $aux = $result = $this->getEm()
                ->getRepository('Entity\Site')->findAll();
            foreach ($aux as $value) {
                $array_retorno[] = $value->getId();
            }
            $lista_subsites = $this->getEm()
                ->getRepository("Entity\Site")
                ->findIn($array_retorno);
            return array('teste' => "teste",'subsites' => $lista_subsites,'vinculos' => $vinculados);
        }
    }

    public function teste()
    {
        $id = 14;

        $banner = $this->getEm()
            ->getRepository('Entity\BannerGeral')->find($id);


        $teste = $banner->getSites();

        foreach ($teste as $entidade) {
            $vinculados[] = $entidade->getId();
        }

        echo "<pre>";
        \Doctrine\Common\Util\Debug::dump($vinculados);
        echo "</pre>";
        die();

        return json_encode($teste);
    }


    public function podeDeletar(){
        $array = $this->getParam()->getArray("sel");
        $id_categoria = $this->getParam()->getInt("id_categoria");
        $id_site = $this->getParam()->getInt("id_site");

        $categoriaSelecionada = $this->getEm()
            ->getRepository('Entity\BannerGeralCategoria')->find($id_categoria);
        $site = $this->getEm()
            ->getRepository('Entity\Site')->find($id_site);

        $tipo_permissao = "EXCLUIR";
        foreach ($this->user['banners'] as $sub => $s) {
            foreach ($s as $categoria => $c) {
                if($categoriaSelecionada and $categoriaSelecionada->getNomeCategoriaSiscau() == $categoria){
                    if($c[$tipo_permissao] == "1") $subsiteSiglas[] = $sub;
                }
            }
        }

        if($subsiteSiglas){
            for($i = 0; $i < count($subsiteSiglas); $i++){
                $result = $this->getEm()
                    ->getRepository('Entity\Site')
                    ->getSiteBySigla($subsiteSiglas[$i]);
                $array_retorno[] = $result->getId();
            }
        }


//        for($i = 0;$i < count($array);$i++){
//            $banner = $this->getEm()
//                   ->getRepository('Entity\BannerGeral')->find($array[$i]);
//            $aux = $banner->getSites();
//            if($aux){
//                foreach ($aux as $value) {
//                    $sites[] = $value->getId();
//                }
//            }
//        }



        if($array_retorno){
            //$sites = array_unique($sites);
//            echo "<pre>";
//            \Doctrine\Common\Util\Debug::dump($sites);
//            echo "</pre>";
//            die();

            if(!in_array($site->getId(), $array_retorno)){
                return json_encode(array("podeExcluir"=>false));
            }

            return json_encode(array("podeExcluir"=>true));
        }else{
            return json_encode(array("podeExcluir"=>false));
        }

    }


    public function podePesquisar($categoria,$site){

        $categoriaSelecionada = $this->getEm()
            ->getRepository('Entity\BannerGeralCategoria')
            ->find($categoria);
        if($this->user["dadosUser"]["login"] == "teste") return true;
        $tipo_permissao = "CONSULT";
        foreach ($this->user['banners'] as $sub => $s) {
            foreach ($s as $categoria => $c) {
                if($categoriaSelecionada->getNomeCategoriaSiscau() == $categoria){
                    if($c[$tipo_permissao] == "1") $subsiteSiglas[] = $sub;
                }
            }
        }

        if($subsiteSiglas){
            for($i = 0; $i < count($subsiteSiglas); $i++){
                $result = $this->getEm()
                    ->getRepository('Entity\Site')
                    ->getSiteBySigla($subsiteSiglas[$i]);
                if (!empty($result)) {
                    $array_retorno[] = $result->getId();
                }
            }
        }

        if($site && $array_retorno){
            if(!in_array($site, $array_retorno)){
                return false;
            }
            return true;
        }else{
            return false;
        }
    }
}

