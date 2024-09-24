<?php
require_once __DIR__.'/vendor/autoload.php';

use Timthumb\Timthumb;

// Configurações
define('MAX_WIDTH', 3000);
define('MAX_HEIGHT', 3000);
define('DEFAULT_Q', 90);
define('FILE_CACHE_DIRECTORY', './cache/images');
define('ALLOW_EXTERNAL', FALSE);

Timthumb::start();