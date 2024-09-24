<?php

namespace LibraryController;

use Template\TemplateAmanda;
use Helpers\Session;
use Security\User;


/**
 * Todas as página passam por aqui
 * 
 * @author Luciano
 */
class FrontControllerCMS implements FrontControllerInterface
{
    const DEFAULT_CONTROLLER = "login";
     
    /**
     *
     * @var AbstractController
     */
    protected $objController;
    protected $controller   = self::DEFAULT_CONTROLLER;
    protected $action;
    protected $params       = array();
    protected $basePath;
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
    public function __construct(array $options = array(), Session $session)
    {
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
    
    /**
     * 
     * @return \Helpers\Session
     */
    public function getSession()
    {
//        if (empty($this->session)) {
//            $this->setSession(new Session());
//        }
        return $this->session;
    }

    /**
     * 
     * @param \Helpers\Session $session
     */
    public function setSession(Session $session)
    {
        $this->session = $session;
    }

    
    /**
     * Seta  o BasePath da Aplicação
     * 
     * 
     */
    public function setBasePath()
    {
        $configPath     = require_once getcwd(). "/config/paths.php";
        $this->basePath = $configPath['base_path'];;
    }

    /**
     * Fragmenta a URI 
     * Gera  o Controller, Action e os Parâmetros
     * 
     * Se o COntroller não existe aparece uma mensagem
     */
    protected function parseUri() 
    {
        if ($this->basePath == '/') {
            $path = $_SERVER["REQUEST_URI"];
        } else {
            $path = str_replace($this->basePath, "", $_SERVER["REQUEST_URI"]);
        }

        $path = trim(parse_url($path, PHP_URL_PATH), "/");

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
        } catch (\Exception $ex) {
            die($ex->getMessage());
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
        
        $controller = "CMS\Controller\\".ucfirst($controller) . "Controller";

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
     * @return \Template\TemplateAmanda
     */
    public function getTemplate()
    {
        return new TemplateAmanda($this->getSession(), $this->controller, $this->action);
    }

    /**
     * Inicia o programa
     */
    public function run() 
    {
        $this->controlPermission();
        
        $this->objController->setTpl($this->getTemplate());
        $this->objController->setLayoutPage($this->controller."/".$this->action);
        
        echo call_user_func_array(array($this->objController, $this->action), $this->params);
    }
    
    /**
     * Permissionamento.
     */
    protected function controlPermission()
    {
        //Instancia a classe USER da security
        $user = new User($this->getSession());

        //Faz a verificação das permissões
        $user->verifyPermissions($this->controller, $this->action, $this->basePath, $this->params);
    }
}