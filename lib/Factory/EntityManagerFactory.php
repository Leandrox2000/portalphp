<?php

namespace Factory;

use Doctrine\ORM\Tools\Setup;
use Doctrine\ORM\EntityManager;
use Logger;

/**
 * EntityManagerFactory
 *
 * @author Luciano
 */
class EntityManagerFactory {

    public static $em = NULL;

    /**
     * Retorna sempre uma nova instância do EntityManager
     * @return EntityManager
     */
    public static function factory()
    {
        $appCfg = include __DIR__ . "/../../config/app.php";
        Logger::configure(getcwd() . "/config/log.xml");
        $log = Logger::getLogger('Sql');
        $paths = array(__DIR__ . "/../../src/Entity");
        $isDevMode = ($appCfg['debug'] == true) ? true : false;
        $config = Setup::createAnnotationMetadataConfiguration($paths, $isDevMode, null, null, false);
        $config->setProxyDir(getcwd() . '/cache/doctrine_proxy');
        $config->addCustomStringFunction("semAcento", "DoctrineExtensions\DQLFunctions\SemAcento");
        $config->addCustomStringFunction("Unaccent", "DoctrineExtensions\DQLFunctions\Unaccent");
        
        if ($isDevMode) {
            $config->setAutoGenerateProxyClasses(true);
        } else {
            $config->setAutoGenerateProxyClasses(false);
        }
        //$config->setSQLLogger(new \Helpers\MySqlLog());

        $conn = include __DIR__ . "/../../config/db.php";

        if (isset($conn['cache']['enable']) && $conn['cache']['enable'] === true) {
            // Cache
            $cacheDir = $conn['cache']['directory'];
            //$config->setQueryCacheImpl(new \Doctrine\Common\Cache\FilesystemCache($cacheDir));
            //$config->setResultCacheImpl(new \Doctrine\Common\Cache\FilesystemCache($cacheDir));
            $config->setMetadataCacheImpl(new \Doctrine\Common\Cache\FilesystemCache($cacheDir));
        }

        try {
            $em = EntityManager::create($conn, $config);
            $query = $em->createNativeQuery("SELECT 1", new \Doctrine\ORM\Query\ResultSetMapping);
            $query->getResult();
        } catch (\Exception $ex) {
            $log->error($ex->getMessage());
            echo $ex->getMessage();
            die("Erro de conexão");
        }

        return $em;
    }

    /**
     * Retorna sempre a mesma instância do EntityManager
     *
     * @return EntityManager
     */
    public static function getEntityManger()
    {
        if (self::$em === NULL) {
            self::$em = self::factory();
        }

        return self::$em;
    }

}
