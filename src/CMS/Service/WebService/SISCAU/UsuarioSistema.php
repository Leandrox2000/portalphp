<?php

namespace CMS\Service\WebService\SISCAU;

use Doctrine\ORM\EntityManager;
use WebService\WebserviceInterface;
use WebService\AbstractWebservice;

/**
 * Description of UsuarioSistema
 * 
 * @autor join-ti
 */
class UsuarioSistema extends AbstractWebservice implements WebserviceInterface {

    /**
     *
     * @var String 
     */
    private $sistema;

    /**
     *
     * @var  String
     */
    private $location;

    /**
     *
     * @var EntityManager 
     */
    private $em;

    /**
     * 
     * @param String $configName
     * @param String $webservice
     */
    public function __construct($configName, $webservice, EntityManager $em) {
        //Seta as configurações iniciais
        $config = include_once __DIR__ . "/../../../../../config/" . $configName;
        $this->setSistema($config[$webservice]['sistema']);
        $this->setWsdl($config[$webservice]['wsdl']);
        $this->setLocation($config[$webservice]['location']);
        $this->setEm($em);
    }

    /**
     * 
     * @return String
     */
    public function getSistema() {
        return $this->sistema;
    }

    /**
     * 
     * @return String
     */
    public function getLocation() {
        return $this->location;
    }

    /**
     * 
     * @param String $sistema
     */
    public function setSistema($sistema) {
        $this->sistema = $sistema;
    }

    /**
     * 
     * @param String $location
     */
    public function setLocation($location) {
        $this->location = $location;
    }

    /**
     * 
     * @return EntityManager
     */
    public function getEm() {
        return $this->em;
    }

    /**
     * 
     * @param EntityManager $em
     */
    public function setEm(EntityManager $em) {
        $this->em = $em;
    }

    /**
     * 
     * @param String $login
     * @param String $senha
     */
    public function auth($login, $senha) {
        //Cria o array com os argumentos
        $arguments = array(
            'arg0' => $login,
            'arg1' => $senha,
            'arg2' => $this->getSistema()
        );

        //Cria um array com as opções
        $options = array('location' => $this->getLocation());

        //Faz a requisição ao webservice
        $result = $this->requestMethod('autenticaUsuario', $arguments, $options);

        //Armazena o xml de resultado
        $xml = $result->return;

        //Carrega o xml
        $simple = simplexml_load_string($xml);

        //Verifica o resultado da autenticação
        if ($simple->codErro) {
            //Monta o resultado apresentando o erro
            $result = array('success' => false, 'error' => $simple->desErro);
        } else {
            //Monta as permissões do usuário
            $metodo = 'br.gov.iphan.siscau.ws.vo.FuncionalidadeVO';
            $array = $simple->$metodo;
            $permissoes = array();
            $subsites = array();
            $banners = array();
            $sede = false;

            //Busca os subsites cadastrados no cms
            $subsitesCms = $this->getEm()->getRepository('Entity\Site')->getArraySites();

            //Percorre e armazena as permissões
            foreach ($array as $valor) {
                $desSigla = str_replace(" ", "", $valor->desSigla);

//                var_dump($valor->desSigla);
//                echo "<hr>";
                //Quebra a sigla
                $arraySigla = explode("_", $desSigla);
//                var_dump($arraySigla);
//                echo "<hr>";
                //Verifica se subsite existe
                if (isset($subsitesCms[$arraySigla[0]])) {
                    $subsites[] = $subsitesCms[$arraySigla[0]];
                }

                //Verifica se é sede
                if ($arraySigla[0] == 'SEDE') {
                    $sede = true;
                }
                
                //Verifica se trata-se de uma permissão
                if (count($arraySigla) == 3) {
                    if($arraySigla[2] != "BANNE"){
                        $permissoes[$arraySigla[1] . "_" . $arraySigla[2]] = $arraySigla[1] . "_" . $arraySigla[2];
                    }
                }
                
//                print_r($arraySigla);
//                echo "<hr>";
                //Permiss�es de Banners
                if (count($arraySigla) == 4) {
                    if($arraySigla[1] == "BANNE"){
                        $banners[$arraySigla[0]][$arraySigla[2]][$arraySigla[3]] = 1;
                    }
                }
                
                
                
//                
//                $banners = array(
//                   'SP' => array(
//                       'CATEGORIA' => array(
//                           'INSERIR' => 1
//                       )
//                   )
//                    
//                );
                
            }
           
            //Retira os registro duplicados desse array
            $subsites = array_unique($subsites);
                    
            //Monta o array de resultados
            $result = array('success' => true, 'permissoes' => $permissoes, 'subsites' => $subsites, 'sede' => $sede, 'banners' => $banners);
        }

        //Retorna o resultado
        return $result;
    }

}
