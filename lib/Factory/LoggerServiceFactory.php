<?php

namespace Factory;

use Logger;

/**
 * Description of LoggerServiceFactory
 *
 * @author Luciano
 */
class LoggerServiceFactory
{
    /**
     * Retorna a Instancia do Loggger para Service
     * 
     * @return type
     */
    public static function getLogger()
    {
        Logger::configure(getcwd()."/config/log.xml");
        return Logger::getLogger("Service");
    }
    
}
