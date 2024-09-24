<?php

namespace Portal\Service\WebService\SICG;

use WebService\WebserviceInterface;
use WebService\AbstractWebservice;


/**
 * Description of PesquisaBem
 *
 * @author Luciano
 */
class PesquisaBem extends AbstractWebservice implements WebserviceInterface
{

    /**
     * Construtor
     */
    public function __construct()
    {
        $config = include_once __DIR__ . "/../../../../../config/webservice.php";
        $this->setWsdl($config['pesquisarBem']['wsdl']);
        $this->setLocation($config['pesquisarBem']['location']);
    }

    /**
     * 
     * @param String $location
     */
    public function setLocation($location)
    {
        $this->location = $location;
    }

    /**
     * 
     * @return String
     */
    public function getLocation()
    {
        return $this->location;
    }

    
    public function getBens()
    {
        //Cria o array com os argumentos
        $arguments = array(NULL, TRUE, TRUE, TRUE, TRUE, TRUE);

        //Cria um array com as opções
        $options = array('location' => $this->getLocation());

        //Faz a requisição ao webservice
        return $this->requestMethod('pesquisarBem', $arguments, $options);

    }


}
