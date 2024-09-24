<?php

namespace CMS\Service\WebService;

use Logger;
use CMS\StaticMethods\StaticMethods;

class Rest {
    
    const METHOD_POST = 'POST';
    const METHOD_GET = 'GET';
    const METHOD_PUT = 'PUT';
    const METHOD_DELETE = 'DELETE';
    
    /**
     * @var array
     */
    protected $actions;
    
    /**
     * @var string
     */
    protected $url;
    
    /**
     * @var Logger
     */
    protected $logger;

    /**
     * @return array
     */
    protected function getActions() {
        return $this->actions;
    }
    
    /**
     * @return string
     */
    protected function getUrl() {
        return $this->url;
    }
    
    /**
     * @return Logger
     */
    public function getLogger()
    {
        if (!isset($this->logger))
            $this->logger = $this->getStaticMethods()->getLoggerFactory();
        return $this->logger;
    }

    /**
     * @return type new \CMS\StaticMethods\StaticMethods()
     */
    public function getStaticMethods()
    {
        if (!isset($this->staticMethods))
            $this->staticMethods = new StaticMethods();
        return $this->staticMethods;
    }
    
    /**
     * @param array $actions
     */
    protected function setActions($actions) {
        $this->actions = $actions;
    }
    
    /**
     * @param string $url
     */
    protected function setUrl($url) {
        $this->url = $url;
    }
    
    /**
     * Retorna a url completa.
     * 
     * @param string $action
     * @return string
     */
    protected function getUrlCompleta($action) {
        $this->validarAction($action);
        return $this->url . '/' . $this->actions[$action];
    }
    
    /**
     * Valida se a action está configurada.
     * 
     * @param string $action
     * @return boolean
     */
    protected function validarAction($action) {
        if (!isset($this->actions[$action])) {
            throw new \Exception('Action não encontrada no arquivo de configuração do WS.');
        }
        return true;
    }
    
    /**
     * Ordena por uma determinada propriedade do objeto.
     * 
     * @param array $dados
     * @param string $propriedade
     */
    protected function ordenarAsc(&$dados, $propriedade) {
        usort($dados, function ($a, $b) use ($propriedade) {
            return strtolower($a->$propriedade) > strtolower($b->$propriedade);
        });
    }
    
    /**
     * Realiza o request para uma determinada api rest.
     * 
     * @param string $method  POST, GET ou PUT
     * @param string $url  URL da requisição
     * @param array|null $data  Dados para serem enviados na requisição
     * @return array
     */    
    protected function request($method, $url, $data = null) {
        $curl = curl_init();

        switch ($method) {
            case "POST":
                curl_setopt($curl, CURLOPT_POST, 1);

                if ($data)
                    curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
                break;
            case "PUT":
                curl_setopt($curl, CURLOPT_PUT, 1);
                break;
            default:
                if ($data)
                    $url = sprintf("%s?%s", $url, http_build_query($data));
        }

        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);

        $result = array();
        $result['result'] = curl_exec($curl);        
        $result['error'] = curl_error($curl);
        $result['info'] = curl_getinfo($curl);

        curl_close($curl);

        return $result;
    }
    
}