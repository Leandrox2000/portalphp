<?php

namespace Template;

use Helpers\Session;

/**
 * Classe de Impletação do TemplateInterface.
 * Se restringe ao Temlate do Amanda
 *
 * @author Luciano
 */
class TemplateAmanda implements TemplateInterface
{

    const PAGE_TITLE = "Sistema - ";
    const DEFAULT_VIEW = "default.html.twig";

    protected $title = self::PAGE_TITLE;
    protected $defaultView = self::DEFAULT_VIEW;
    private $config;
    private $header;
    private $top;
    private $menu;
    private $redesSociaisItens;
    private $bannerComunicacaoItens;
    private $content;
    private $contentView;

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
    private $currentController ;

        /**
     *
     * @var String
     */
    private $action ;

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
            'cache' => './cache/twig',
            'auto_reload' => true,
        );
        $this->twig = new \Twig_Environment($this->loader, $this->cacheConfig);
        $this->twig->addExtension(new \Twig_Extension_Escaper('html'));
        $this->currentController = $currentController;
        $this->action = $action;
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
        $this->config = include getcwd() . "/config/templateAmanda.php";
        ;
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
        $this->menu = $this->twig->render("menu.html.twig", array('permissoes' => $sessao['permissoesUser'], 'controller' => $this->currentController, 'acao' => $this->action));
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
    
    
      public function getBannerComunicacaoItens()
    {
        return $this->bannerComunicacaoItens;
    }
    
    public function setBannerComunicacaoItens($bannerComunicacaoItens)
    {
        $this->bannerComunicacaoItens = $bannerComunicacaoItens;
    }
    
    /**
     * Seta  o Todo o conteúdo da Página, Topo Men e Ocnteúdo
     *
     * @param HTML $content Todo o conteúdo que a página vai exibir
     */
    public function setContent($content)
    {
        $this->content = $this->twig->render("content.html.twig", array(
            "top" => $this->top,
            "menu" => $this->menu,
            "content" => $this->contentView,
        ));
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
    public function renderView(array $context = array())
    {
        //Verifica se existe a sessão para passar as permissões para view
        if ($this->session->get('user')) {
            $sessao = $this->session->get('user');
            $context['permissoes'] = $sessao['permissoesUser'];
        }

        return $this->contentView = $this->twig->render($this->defaultView, $context);
    }

    public function addGlobal($name, $value)
    {
        $this->twig->addGlobal($name, $value);
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
        $this->setTop();
        $this->setMenu();
        $this->setRedesSociais();
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
     * Não é implementado neste Template
     *
     * @ignore
     */
    public function setFooter()
    {
        ;
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
        
    }

}
