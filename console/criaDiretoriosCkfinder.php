<?php
/**
 * Verifica se os diretórios de categorias e pastas existem.
 * Caso não existam os diretórios, os mesmos serão criados.
 */

require '../vendor/autoload.php';

use Helpers\String;

$em = Factory\EntityManagerFactory::getEntityManger();
$repoCat = $em->getRepository('Entity\ImagemCategoria');
$repoPas = $em->getRepository('Entity\ImagemPasta');
$categorias = $repoCat->findAll();
$pastas = $repoPas->findAll();
$base = '../uploads/ckfinder/images/';

// Verifica se os diretórios de categorias existem
foreach ($categorias as $categoria) {
    $fullPath = $base . String::removeSpecial($categoria->getLabel());

    // Caso não exista, cria o diretório
    if (!file_exists($fullPath)) {
        echo "Criando o diretorio '" . $fullPath . "'...\n";
        mkdir($fullPath);
    }
}

// Verifica se os diretórios de pastas existem
foreach ($pastas as $pasta) {
    $fullPath = $base . String::removeSpecial($pasta->getCategoria()->getLabel()) . '/' . $pasta->getCaminho();

    // Caso não exista, cria o diretório
    if (!file_exists($fullPath)) {
        echo "Criando o diretorio '" . $fullPath . "'...\n";
        mkdir($fullPath);
    }
}