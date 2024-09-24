<?php

namespace CMS\Service\WebService\RestSISCAU;

/**
 * Classe base para ser complementada com funcionalidades em comum
 * das actions.
 */
class RestSISCAU extends \CMS\Service\WebService\Rest {
    
    /**
     * @var string
     */
    protected $sistema;

    /**
     * Atribui as configurações.
     * 
     * @param string $configName
     */
    public function __construct($configName = 'webservice.php') {
        $config = include __DIR__ . "/../../../../../config/" . $configName;
        $this->setSistema($config['RestSiscau']['params']['sistema']);
        $this->setActions($config['RestSiscau']['actions']);
        $this->setUrl($config['RestSiscau']['urlBase']);
    }
    
    /**
     * @param string $sistema
     */
    protected function setSistema($sistema) {
        $this->sistema = $sistema;
    }
    
    /**
     * @return string
     */
    protected function getSistema() {
        return $this->sistema;
    }
    
    /**
     * Realiza o request para a api rest do SISCAU.
     *      
     * @param string $action  Nome da action no arquivo de configuração de WS
     * @param string $method  POST, GET ou PUT
     * @param array|null $data  Dados para serem enviados na requisição
     * 
     * @return array
     */
    protected function request($action, $method, $data = null) {
        $this->validarAction($action);
        return parent::request($method, $this->getUrlCompleta($action), $data);
    }
    
}