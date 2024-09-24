<?php

namespace Factory;
use DebugBar\StandardDebugBar;

class DebugBarFactory {

    public static $instance = NULL;

    /**
     * Singleton
     * @return StandardDebugBar
     */
    public static function getInstance()
    {
        if (self::$instance === NULL) {
            $debugStack = new \Doctrine\DBAL\Logging\DebugStack();
            EntityManagerFactory::getEntityManger()
                    ->getConnection()
                    ->getConfiguration()
                    ->setSQLLogger($debugStack);

            $instance = new StandardDebugBar();
            $instance->getJavascriptRenderer()->setEnableJqueryNoConflict(FALSE);
            $instance->addCollector(new \DebugBar\Bridge\DoctrineCollector($debugStack));

            self::$instance = $instance;
        }

        return self::$instance;
    }
    
}