<?php

namespace Template;

use Helpers\Session;
use Helpers\Youtube;
use Factory\DebugBarFactory;

/**
 * Classe de Impletação do TemplateInterface.
 * Se restringe ao Temlate do Amanda
 *
 * @author Luciano
 */
class TemplatePortal implements TemplateInterface
{

    const PAGE_TITLE = "Home";
    const DEFAULT_VIEW = "default.html.twig";

    protected $title = self::PAGE_TITLE;
    protected $defaultView = self::DEFAULT_VIEW;
    private $config;
    private $header;
    private $top;
    private $menu;
    private $redes;
    private $bannersComunicacao;
    private $bannersComunicacaoHome;
    private $content;
    private $contentView;
    private $sidebar;
    private $subsite;
    private $debugbar;
    private $debugbarRenderer;
    private $debug = false;
    private $bannersLaterias = array();
    private $bannersLateraisDestaques = array();
    private $bannersDivulgacao = array();
    private $bannersRodape = array();
    private $enderecoRodape = array();

    /**
     *
     * @var \Twig_Loader_Filesystem
     */
    private $loader;

    /**
     *
     * @var Session
     */
    private $session;

    /**
     *
     * @var \Twig_Environment
     */
    private $twig;

    /**
     *
     * @var String
     */
    private $currentController;

    /**
     *
     * @var String
     */
    private $action;

    /**
     *
     * @var array
     */
    private $menuItems;
    
      /**
     *
     * @var array
     */
    private $redesSociaisItens;
     
    /**
     *
     * @var array
     */
    private $bannerComunicacaoItens;

    /**
     * Carrega o Twig Loader e as configurações
     *
     */
    public function __construct(Session $session = null, $currentController = "", $action = "")
    {
        $this->session = $session;
        $this->loadConfig();
        $this->loader = new \Twig_Loader_Filesystem($this->config['view_path']);
        $this->cacheConfig = array(
            'cache' => './cache/twig_portal',
            'auto_reload' => true,
            //'debug' => true,
        );
        $this->twig = new \Twig_Environment($this->loader, $this->cacheConfig);
        //$this->twig->addExtension(new \Twig_Extension_Debug());
        $this->initTwigExtensions();
        $this->initDebugBar();
        $this->currentController = $currentController;
        $this->action = $action;
        $this->twig->addGlobal('controller', $currentController);
        $this->twig->addGlobal('action', $action);
    }

    private function initDebugBar()
    {
        $appCfg = include BASE_PATH . 'config/app.php';

        if ($appCfg['debug']) {
            // Debug bar
            $this->debug = true;
            $this->debugbar = DebugBarFactory::getInstance();
            $this->debugbarRenderer = $this->debugbar->getJavascriptRenderer();
            $this->twig->addGlobal('debug', true);
            $this->twig->addGlobal('debugbar_renderer', $this->debugbarRenderer);
        } else {
            $this->twig->addGlobal('debug', false);
        }
    }

    private function initTwigExtensions()
    {
        $truncatetext = new \Twig_SimpleFilter('truncatetext', function ($string, $limit) {
            return \Helpers\String::truncateText($string, $limit, array('html' => true, 'exact' => false));
        });
        $youtube = new \Twig_SimpleFunction('youtube', function ($url) {
            $youtubeHelper = new Youtube($url);
            return $youtubeHelper->getEmbed($url);
        });
        $youtubeKey = new \Twig_SimpleFunction('youtubeKey', function ($url) {
            $youtubeHelper = new Youtube($url);
            return $youtubeHelper->getKey($url);
        });
        $strftime = new \Twig_SimpleFunction('strftime', function ($format, $timestamp) {
            return strftime($format, $timestamp);
        });
        $imgCompletePath = new \Twig_SimpleFunction('imgCompletePath', function ($image) {
            return \Helpers\Image::completePath($image);
        });
        $slugify = new \Twig_SimpleFunction('slugify', function ($title) {
            return \Helpers\Url::slugify($title);
        });
        $site = new \Twig_SimpleFunction('site', function (\Entity\Site $subsite) {
            return mb_strtolower($subsite->getSigla());
        });
        $that = $this; // PHP 5.3 não permite $this dentro de anonymous functions
        $route = new \Twig_SimpleFunction('route', function ($url = null) use ($that) {
            $config = $that->getConfig();
            $route = $config['base_link'];
            if ($that->getSubsite() != null) {
                $route .= mb_strtolower($that->getSubsite()->getSigla()) . '/';
            }
            $route .= $url;

            return $route;
        });
        $generateUrl = new \Twig_SimpleFunction("generateUrl", function ($entity, $baseLink = null) { 
            return \Helpers\Http::generateUrl($entity, $baseLink);
        });

        $this->twig->addFilter($truncatetext);
        $this->twig->addFunction($site);
        $this->twig->addFunction($route);
        $this->twig->addFunction($youtube);
        $this->twig->addFunction($youtubeKey);
        $this->twig->addFunction($strftime);
        $this->twig->addFunction($imgCompletePath);
        $this->twig->addFunction($slugify);
        $this->twig->addFunction($generateUrl);
        $this->twig->addExtension(new \Twig_Extension_Escaper('html'));
    }

    public function addGlobal($name, $value)
    {
        $this->twig->addGlobal($name, $value);
    }

    /**
     *
     * @return string
     */
    public function getMenuItems()
    {
        return $this->menuItems;
    }

    /**
     *
     * @param string $menuItems
     */
    public function setMenuItems($menuItems)
    {
        $this->menuItems = $menuItems;
    }

    /**
     *
     * @return string
     */
    public function getRedesSociaisItens()
    {
        return $this->redesSociaisItens;
    }
    
    public function setRedesSociaisItens($redesSociaisItens)
    {
        $this->redesSociaisItens = $redesSociaisItens;
    }
    
    
    /**
     *
     * @return string
     */
    public function getBannerComunicacaoItens()
    {
        return $this->bannerComunicacaoItens;
    }
    
    public function setBannerComunicacaoItens($bannerComunicacaoItens)
    {
        $this->bannerComunicacaoItens = $bannerComunicacaoItens;
    }
    
    /**
     *
     * @return \Twig_Environment
     */
    public function getTwig()
    {
        return $this->twig;
    }

    /**
     *
     * @param \Twig_Environment $twig
     */
    public function setTwig(\Twig_Environment $twig)
    {
        $this->twig = $twig;
    }

    /**
     * Carrega o Arquivo de Configurações do Tempalte
     *
     */
    public function loadConfig()
    {
        $config = include getcwd() . "/config/templatePortal.php";
        $this->config = $config;
    }

    public function getConfig()
    {
        return $this->config;
    }

    /**
     * Seta  o Header da Página
     * <header> </header>
     *
     */
    public function setHeader()
    {
        $this->header = $this->twig->render("header.html.twig", array(
            "title" => $this->title,
            "base_link" => $this->config['base_link'],
            "assets_link" => $this->config['base_link'],
            "js_link" => $this->config['js_link'],
            "css_link" => $this->config['css_link'],
            "scripts" => $this->config['js'],
            "styles" => $this->config['css'],
        ));
    }

    /**
     * Seta  o Topo da Página onde fica o menu de Configurações de usuário
     *
     */
    public function setTop()
    {
        //Busca os dados do usuário que estão na sessão
        $dadosUser = $this->session->get('user');

        $this->top = $this->twig->render("topHeader.html.twig", array(
            "base_link" => $this->config['base_link'],
            'loginUsuario' => $dadosUser['dadosUser']['login'],
        ));
    }

    /**
     * Seta  o menu da Página
     *
     */
    public function setMenu()
    {
        $sessao = $this->session->get('user');
        $this->twig->addGlobal('menu', $this->getMenuItems());
        $this->menu = $this->twig->render("menu.html.twig", array(
            'permissoes' => $sessao['permissoesUser'],
            'controller' => $this->currentController,
            'acao' => $this->action,
        ));
    }

    /**
     * Seta  o Todo o conteúdo da Página, Topo Men e Ocnteúdo
     *
     * @param HTML $content Todo o conteúdo que a página vai exibir
     */
    public function setContent($content)
    {
        $this->content = $this->twig->render("content.html.twig", array(
            "currentController" => $this->currentController,
            "currentAction" => $this->action,
            "subsite" => $this->subsite,
            "top" => $this->top,
            "menu" => $this->menu,
            'sidebar' => $this->sidebar,
            "content" => $this->contentView,
            "footer" => $this->footer,
            "base_link" => $this->config['base_link'],
            "scripts" => $this->config['js'],
            "js_link" => $this->config['js_link'],
        ));
    }

    public function setSidebar()
    {
        $this->sidebar = $this->twig->render('sidebar.html.twig', array("bannersLateral" => $this->bannersLaterias, "bannersLateralDestaque" => $this->bannersLateraisDestaques, "subsite" => $this->getSubsite()));
    }

    
    public function setLogin()
    {
        $this->content = $this->twig->render("login.html.twig", array(
            "title" => $this->title,
            "base_link" => $this->config['base_link'],
            "js_link" => $this->config['js_link'],
            "css_link" => $this->config['css_link'],
            "scripts" => $this->config['js'],
            "styles" => $this->config['css'],
        ));
    }

    /**
     *
     * @param type $context
     */
    public function renderView(array $context = array(), $view = NULL)
    {
        $this->setSidebar();
        $this->setRedesSociais();
        $this->setBannersComunicacao();
        $this->setBannersComunicacaoHome();
        $this->getBannersLateraisDestaques();
        $this->getBannersLaterias();
        //Verifica se existe a sessão para passar as permissões para view
        if ($this->session->get('user')) {
            $sessao = $this->session->get('user');
            $context['permissoes'] = $sessao['permissoesUser'];
        }
        $context['base_link'] = $this->config['base_link'];
        $context['sidebar'] = $this->sidebar;
        $context['subsite'] = $this->subsite;
        $context['menu'] = $this->getMenuItems();
        $context['redes'] = $this->redes;
        $context['bannersComunicacao'] = $this->bannersComunicacao;
        $context['bannersComunicacaoHome'] = $this->bannersComunicacaoHome;
        
//        echo "<pre>";
//        \Doctrine\Common\Util\Debug::dump($context['menu']);
//        echo "</pre>";
//        die('fim');
        
        if ($view == NULL) {
            return $this->contentView = $this->twig->render($this->defaultView, $context);
        } else {
            return $this->contentView = $this->twig->render($view, $context);
        }
    }

    /**
     * Retorna toda a página
     *
     * @return Html Retorna  o Html inteiro da Página
     */
    public function getHtml()
    {
        return $this->header . $this->content;
    }

    /**
     * Seta  o Title geral da Página
     * <title></title>
     *
     * @param string $title
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }

    /**
     * Seta a Página que será carrega
     * Se não for executado
     *
     * @param string $view
     */
    public function setView($view)
    {
        $this->defaultView = $view;
    }

    /**
     * Adiciona um Scrpit ao Header
     *
     * @param string $script Caminho do arquivo do em relação há /template/assests/js
     */
    public function addJS($script)
    {
        $this->config['js'][] = $script;
    }

    /**
     * Adicion um CSS ao Header
     *
     * @param string $css Caminho do arquivo do em relação há /template/assests/css
     */
    public function addCSS($css)
    {
        $this->config['css'][] = $css;
    }

    /**
     * Retorna a Instancia do FileSystema do Template
     *
     * @return \Twig_Loader_Filesystem
     */
    public function getLoader()
    {
        return $this->loader;
    }

    /**
     * Monta  o Template da Página e retorna a saída do HTML
     *
     * @return HTML
     */
    public function output()
    {
        $this->setHeader();
        $this->setMenu();
        $this->setRedesSociais();
        $this->setBannersComunicacao();
        $this->setBannersComunicacaoHome();
        $this->setTop();
        $this->setSidebar();
        $this->setFooter();
        $this->setContent("");

        return $this;
    }

    /**
     * Verifica se o Arquivo de View Existe
     *
     * @param string $view
     * @return boolean
     */
    public function viewExists($view)
    {
        return file_exists($this->config['view_path'] . "/" . $view) || file_exists($this->config['view_path'] . "/" . $view . ".twig");
    }

    /**
     * Verifica se Arquivo de Script Existe
     *
     * @param string $js
     * @return boolean
     */
    public function jsExists($js)
    {
        return file_exists($this->config['js_path'] . "/" . $js);
    }

    /**
     * Veriica se o Arquivo de CSS existe
     *
     * @param string $css
     * @return boolean
     */
    public function cssExists($css)
    {
        return file_exists($this->config['css_path'] . "/" . $css);
    }

    /**
     * Define o rodapé.
     */
    public function setFooter()
    {
        $this->footer = $this->twig->render("footer.html.twig", array(
            "banners" => $this->bannersDivulgacao,
            "bannersRodape" => $this->bannersRodape,
            "title" => $this->title,
            "base_link" => $this->config['base_link'],
            "assets_link" => $this->config['base_link'],
            "js_link" => $this->config['js_link'],
            "enderecoRodape" => $this->enderecoRodape,
            "css_link" => $this->config['css_link'],
            "scripts" => $this->config['js'],
            "styles" => $this->config['css'],
            
        ));
    }

    public function setSubsite($subsite)
    {
        $this->subsite = $subsite;
    }

    public function getSubsite()
    {
        return $this->subsite;
    }

    /**
     * Retorna todo o Html do Template
     *
     * @return string
     */
    public function __toString()
    {
        return $this->getHtml();
    }

    /**
     *
     * @return array
     */
    public function getBannersLaterias()
    {
        return $this->bannersLaterias;
    }

    /**
     *
     * @return array
     */
    public function getBannersLateraisDestaques()
    {
        return $this->bannersLateraisDestaques;
    }

    /**
     *
     * @param array $bannersLaterias
     */
    public function setBannersLaterias($bannersLaterias)
    {
        $this->bannersLaterias = $bannersLaterias;
    }

    /**
     *
     * @param array $bannersLateraisDestaques
     */
    public function setBannersLateraisDestaques($bannersLateraisDestaques)
    {
        $this->bannersLateraisDestaques = $bannersLateraisDestaques;
    }

    /**
     *
     * @return array
     */
    public function getBannersDivulgacao() {
        return $this->bannersDivulgacao;
    }

    /**
     *
     * @param array $bannersDivulgacao
     */
    public function setBannersDivulgacao($bannersDivulgacao) {
        $this->bannersDivulgacao = $bannersDivulgacao;
    }

    /**
     * 
     * @return array
     */
    public function getBannersRodape()
    {
        return $this->bannersRodape;
    }

    /**
     * 
     * @param array $bannersRodape
     */
    public function setBannersRodape($bannersRodape)
    {
        $this->bannersRodape = $bannersRodape;
    }

    /**
     * 
     * @param array $enderecoRodape
     */
    public function setEnderecoRodape($enderecoRodape)
    {
        $this->enderecoRodape = $enderecoRodape;
    }
    
    /**
     * 
     * @return array
     */
    public function getEnderecoRodape()
    {
        return $this->enderecoRodape;
    }

    public function container() {
        
    }

    public function current($page) {
        
    }

    public function first() {
        
    }

    public function last($page) {
        
    }

    public function nextDisabled() {
        
    }

    public function nextEnabled($page) {
        
    }

    public function page($page) {
        
    }

    public function pageWithText($page, $text) {
        
    }

    public function previousDisabled() {
        
    }

    public function previousEnabled($page) {
        
    }

    public function separator() {
        
    }

    public function setRedesSociais() {
        $sessao = $this->session->get('user');
        $this->redes = $this->twig->render("redes.html.twig", $this->getRedesSociaisItens());
    }
    public function setBannersComunicacao()
    {
        $sessao = $this->session->get('user');
        $this->bannersComunicacao = $this->twig->render("box-banners.html.twig", $this->getBannerComunicacaoItens());
    }
    
    public function setBannersComunicacaoHome()
    {
        $this->bannersComunicacaoHome = $this->twig->render("box-banners-home.html.twig", $this->getBannerComunicacaoItens());
    }

}