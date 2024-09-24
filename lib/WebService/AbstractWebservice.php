<?php
namespace WebService;

/**
 * Description of AbstractWebservice
 *
 * @autor join-ti
 */
abstract class AbstractWebservice
{

    /**
     *
     * @var String
     */
    protected $wsdl;

    /**
     *
     * @return String
     */
    public function getWsdl()
    {
        return $this->wsdl;
    }

    /**
     *
     * @param String $wsdl
     */
    public function setWsdl($wsdl)
    {
        $this->wsdl = $wsdl;
    }

    /**
     *
     * @param array $function
     * @param array $arguments
     * @param array $options
     * @return string
     */
    public function requestMethod($function, $arguments, $options)
    {
        try {
            //Cria o soap client
            $client = new \SoapClient($this->getWsdl());
        } catch (\Exception $e) {
            echo $e->getMessage();
        }

        try {
            //Retorna a resposta do webservice
            return $client->__soapCall($function, array($arguments), $options);
        } catch (Exception $e) {
            echo $e->getMessage();
        }
    }

}
