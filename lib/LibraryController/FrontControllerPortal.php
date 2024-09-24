<?php

namespace LibraryController;

use Template\TemplatePortal;
use Helpers\Session;
use Helpers\SiteMatcher;

/**
 * Todas as páginas do portal passam por aqui.
 *
 * @author Luciano
 */
class FrontControllerPortal implements FrontControllerInterface
{
    const DEFAULT_CONTROLLER = "index";

    /**
     * @var AbstractController
     */
    protected $objController;
    protected $controller   = self::DEFAULT_CONTROLLER;
    protected $action;
    protected $params       = array();
    protected $basePath;
    protected $subsites;
    /**
     *
     * @var Session
     */
    protected $session;

     /**
      *
      *
      * @param array $options
      */
    public function __construct(array $options = array(), Session $session, $subsites = NULL)
    {
        $this->setSubsites($subsites);
        $this->setSession($session);
        $this->setBasePath();

        if (empty($options)) {
           $this->parseUri();
        } else {
            if (isset($options["controller"])) {
                $this->setController($options["controller"]);
            }
            if (isset($options["action"])) {
                $this->setAction($options["action"]);
            } else {
                $this->setAction($this->objController->getDefaultAction());
            }
            if (isset($options["params"])) {
                $this->setParams($options["params"]);
            }
        }
    }

    public function getSubsites()
    {
        return $this->subsites;
    }

    public function setSubsites(array $subsites = NULL)
    {
        $this->subsites = $subsites;
    }

    /**
     * @return \Helpers\Session
     */
    public function getSession()
    {
        if (empty($this->session)) {
            $this->setSession(new Session());
        }
        return $this->session;
    }

    /**
     * @param \Helpers\Session $session
     */
    public function setSession(Session $session)
    {
        $this->session = $session;
    }

    /**
     * Seta  o BasePath da Aplicação
     */
    public function setBasePath()
    {
        $configPath     = require_once getcwd(). "/config/paths.php";
        $this->basePath = $configPath['base_path'];
    }

    /**
     * Extraí o subsite da URL e define a propriedade subsite.
     * @param string $path
     * @return string $path atualizado.
     */
    private function parseSiteFromUri($path)
    {
        $siteMatcher = new SiteMatcher($this->getSubsites());
        $matchedSite = $siteMatcher->matchAnySite($path);

        // Se encontrou o site remove o prefixo
        if ($matchedSite instanceof \Entity\Site) {
            $path = $siteMatcher->getPathWithoutSite($path);
        }

        if (!$matchedSite instanceof \Entity\Site) {
            // Se não encontrou o site
            $this->subsites = NULL;
        } else {
            // Se encontrou o site
            $this->subsites = $matchedSite;
        }

        // Retorna o caminho atualizado
        return $path;
    }

    /**
     * Fragmenta a URI
     * Gera  o Controller, Action e os Parâmetros
     *
     * Se o Controller não existe aparece uma mensagem
     */
    protected function parseUri()
    {
        if ($this->basePath == '/') {
            $path = $_SERVER["REQUEST_URI"];
        } else {
            $path = str_replace($this->basePath, "", $_SERVER["REQUEST_URI"]);
        }

        $path = $this->parseSiteFromUri(trim(parse_url($path, PHP_URL_PATH), "/"));

        if (!empty($this->basePath)) {
            if (strpos($path, $this->basePath) === 0) {
                $path = substr($path, strlen($this->basePath));
            }
        }

        @list($controller, $action, $params) = explode("/", $path, 3);

        if (!empty($controller)) {
            $this->controller = $controller;
        }

        try {
            $this->setController($this->controller);
        } catch (\Exception\NotFoundException $ex) {
            die('Erro 404 - Página não encontrada');
        }

        if (!empty($action)) {
            $this->setAction($action);
        } else {
            $this->setAction($this->objController->getDefaultAction());
        }
        if (!empty($params)) {
            $this->setParams(explode("/", $params));
        }
    }

    /**
     * Define a Classe de COntroller
     *
     * @param sring $controller
     */
    public function setController($controller)
    {
        $this->controller = ($controller);

        $controller = "Portal\Controller\\".ucfirst($controller) . "Controller";

        if (!class_exists($controller)) {
            throw new \Exception\NotFoundException(
                "The action controller '$controller' has not been defined.");
        }

        $this->objController = new $controller($this->getTemplate(), $this->getSession());
    }

    /**
     * Define a ação(método) que vai ser executado
     *
     * @param string $action
     */
    public function setAction($action)
    {
        $this->action = $action;
    }

    /**
     * Define os parâmetros que serão passados no Action
     *
     * @param array $params
     */
    public function setParams(array $params)
    {
        foreach ($params as $param) {
            $this->params[] = urldecode($param);
        }
    }

    /**
     *
     * @return \Template\TemplatePortal
     */
    public function getTemplate()
    {
        return new TemplatePortal($this->getSession(), $this->controller, $this->action);
    }

    /**
     * Inicia o programa
     */
    public function run()
    {
        $this->objController->setTpl($this->getTemplate());
        $this->objController->setSubsite($this->getSubsites());
        $this->objController->getTpl()->setMenuItems($this->objController->getMenu());
        //$this->objController->getTpl()->setSubsite($this->getSubsites());
        $this->objController->getTpl()->setSubsite($this->objController->getSubsite());
        $this->objController->getTpl()->setRedesSociaisItens($this->objController->getRedesSociais());
        $this->objController->getTpl()->setBannersDivulgacao($this->objController->getBannersDivulgacao());
        $this->objController->getTpl()->setBannersRodape($this->objController->getBannersRodape());
        $this->objController->getTpl()->setEnderecoRodape($this->objController->getEnderecoRodape());
        $this->objController->getBannersLaterais(null, null, null);
        $this->objController->getTpl()->setBannerComunicacaoItens($this->objController->getBannersComunicacao());
        $this->objController->setLayoutPage($this->controller."/".$this->action);
        
        try {
            echo call_user_func_array(
                array($this->objController, $this->action),
                $this->params
            );
        } catch (\Exception\NotFoundException $e) {
            echo "Erro 404 - Página não encontrada.";
        } catch (\Exception $e) {
            $cfg = include BASE_PATH . 'config/app.php';

            // Se o debug estiver desativado mostra uma mensagem amigável
            if ($cfg['debug'] === false) {
                echo "Ocorreu um erro interno no servidor.";
            // Caso contrário lança a exceção capturada novamente
            } else {
                throw $e;
            }
        }
    }
}