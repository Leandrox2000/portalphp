<?php

namespace Factory;

use Logger;

/**
 * Description of LoggerServiceFactory
 *
 * @author Luciano
 */
class LoggerFactory
{
    /**
     * Retorna a Instancia do Loggger para Service
     * 
     * @return type
     */
    public static function getLoggerService()
    {
        Logger::configure(getcwd()."/config/log.xml");
        return Logger::getLogger("Service");
    }
    
    /**
     * Retorna a Instancia do Loggger para Service
     * 
     * @return type
     */
    public static function getLoggerExecucao()
    {
        Logger::configure(getcwd()."/config/log.xml");
        return Logger::getLogger("Execucao");
    }
    
}
