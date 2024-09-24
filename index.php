<?php
ini_set('display_errors', 1);
error_reporting(E_ALL & ~E_STRICT & ~E_NOTICE);
//error_reporting('E_WARNING');

//ini_set('display_errors', 0);
//error_reporting(0);

ini_set("default_socket_timeout", 6000);

ob_start();
define('DS', DIRECTORY_SEPARATOR);
define('BASE_PATH', dirname(__FILE__) . DS);
define('CACHE_LIFE_TIME', 30);

date_default_timezone_set('America/Sao_Paulo');
setlocale(LC_ALL, 'pt_BR', 'ptb'); // Define locale para Linux e Windows
$start = microtime(true);

require_once __DIR__.'/vendor/autoload.php';

use Factory\LoggerFactory;
use Factory\EntityManagerFactory as EM;
use LibraryController\FrontControllerCMS;
use LibraryController\FrontControllerPortal;
use Helpers\Session;
use Helpers\Http;

if($_SERVER['HTTP_HOST'] != $_SERVER['HTTP_X_FORWARDED_HOST']){
    if($_SERVER['HTTP_X_FORWARDED_HOST'] == "")
        $_SERVER['HTTP_X_FORWARDED_HOST'] = $_SERVER['HTTP_HOST'];
    $_SERVER['HTTP_HOST'] = $_SERVER['HTTP_X_FORWARDED_HOST'];
}

$appCfg = include BASE_PATH . 'config/app.php';
$domainCfg = include BASE_PATH . 'config/domain.php';
$domain = Http::getServerName();
$session = new Session($appCfg['session_duration']);

//Define URL do portal e cms
define('URL_PORTAL', $domainCfg['portal']);
define('URL_CMS', $domainCfg['cms']);

// Se é o domínio do CMS
if ($domain == $domainCfg['cms']) {
    $frontController = new FrontControllerCMS(array(), $session);
}

// Se é o domínio do portal
else if ($domain == $domainCfg['portal']) {
    $subsites = EM::getEntityManger()
            ->getRepository('Entity\Site')
            ->getQueryIndex();
    $frontController = new FrontControllerPortal(array(), $session, $subsites);
}
// Não encontrado
else {
    throw new \Exception('Domínio inválido.');
}

$frontController->run();

// Somente se o debug estiver ativado, para não criar um gargalo de performance
if (isset($appCfg['debug']) && $appCfg['debug'] === true) {
    $time = microtime(true) - $start;
    $log = LoggerFactory::getLoggerExecucao();
    $log->info($time ." - ". $_SERVER['REQUEST_URI'] ." - ". $_SERVER['REQUEST_METHOD'] );
}

