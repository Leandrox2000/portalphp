<?php

require './vendor/autoload.php';

ob_start();

$log = "";
$em = Factory\EntityManagerFactory::getEntityManger();
// Lista das entidades
$entities = array(
    'Entity\Noticia',
    'Entity\PaginaEstatica',
    'Entity\Ata',
    'Entity\Agenda',
    'Entity\Biblioteca',
    'Entity\Bibliografia',
    'Entity\DicionarioPatrimonioCultural',
    'Entity\Edital',
    'Entity\Fototeca',
    'Entity\Galeria',
    'Entity\Legislacao',
    'Entity\Publicacao',
);
// Expressões regulares utilizadas
$regex = array(
    '/(http\:\/\/)(novoportal|cmsnovoportal)(\.iphan\.gov\.br)(\/){1,2}/',
);

function showMessage($string)
{
    global $log;

    echo $string;
    $log .= $string;
}

/**
 *
 * @global array $regex
 * @param string $property Nome da propriedade
 * @param object $record Objeto da entidade.
 */
function setProperty($property, $record)
{
    global $regex;

    $getterMethod = function() use ($property, $record) {
        return call_user_method('get' . $property, $record);
    };
    $setterMethod = function($newValue) use ($property, $record) {
        return call_user_method('set' . $property, $record, $newValue);
    };
    $pregMatchArray = function($string) use ($regex) {
        foreach ($regex as $re) {
            if (preg_match($re, $string)) {
                return true;
            }
        }

        return false;
    };

    // Se existe a propriedade/método
    if (method_exists($record, 'get' . $property)) {
        // Se encontrou alguma string que deve ser substituída
        $show = $pregMatchArray($getterMethod()) ? true : false;

        if ($show) {
            showMessage("Antes:\n");
            showMessage("ID: " . $record->getId() . "\nDescrição: ". $getterMethod() . "\n");
        }

        $setterMethod(preg_replace($regex, '/', $getterMethod()));

        if ($show) {
            showMessage("Depois:\n");
            showMessage("ID: " . $record->getId() . "\nDescrição: ". $getterMethod() . "\n");
            showMessage("\n#####################################################\n\n\n");
        }
    }
}

// Para cada entidade
foreach ($entities as $entity) {
    $repository = $em->getRepository($entity);
    $records = $repository->findAll();

    // Para cada registro
    foreach ($records as $record) {
        setProperty('Descricao', $record);
        setProperty('Conteudo', $record);
        setProperty('Curriculo', $record);

        $em->persist($record);
        $em->flush();

        // Impede 100% de uso de CPU
        usleep(10);
    }

    $em->clear();
}

file_put_contents('./logs/urls-relativas.log', $log);
