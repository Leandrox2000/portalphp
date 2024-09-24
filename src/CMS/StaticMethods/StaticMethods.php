<?php

namespace CMS\StaticMethods;

/**
 * Classe StaticMethods
 * 
 * Utilizada para mediar a chamada de métodos estáticos 
 */
class StaticMethods
{

    /**
     * Metodo getMethodStatic
     * 
     * Utilizado para retornar um método estático
     * @param String $class
     * @param String $method
     * @param array $params
     * @return mixed
     */
    private function getMethodStatic($class, $method, $params = array())
    {
        return call_user_func_array(array($class, $method), $params);
    }

    /**
     * Metodo getLoggerFactory
     * 
     * Retorna um loggerFactory
     * @return \Logger
     */
    public function getLoggerFactory()
    {
        return $this->getMethodStatic("\Factory\LoggerFactory", "getLoggerService");
    }

    /**
     * Metodo getPHPExcel_IOFactory
     * 
     * Retorna o método statico createWriter da classe PHPExcel_IOFactory
     * @param \PHPExcel $objPHPExcel
     * @return mixed
     */
    public function getPHPExcel_IOFactory(\PHPExcel $objPHPExcel)
    {
        return $this->getMethodStatic("\PHPExcel_IOFactory", "createWriter", array($objPHPExcel, 'Excel5'));
    }

}
