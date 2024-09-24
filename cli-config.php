<?php
require_once __DIR__.'/vendor/autoload.php';

use Factory\EntityManagerFactory as EM;

$em = EM::getEntityManger();


return \Doctrine\ORM\Tools\Console\ConsoleRunner::createHelperSet($em);