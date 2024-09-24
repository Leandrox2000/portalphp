<?php

namespace Helpers;

/**
 * Helper para imagem
 */
class Image {

    public static function completePath(\Entity\Imagem $imagem = NULL)
    {
        if ($imagem === NULL) {
            return NULL;
        }

        $categoriaNome = $imagem->getPasta()->getCategoria()->getNome();
        $categoria = \Helpers\String::removeSpecial($categoriaNome);
        $pasta = $imagem->getPasta()->getCaminho();
        $arquivoImagem = $imagem->getImagem();
        $base = 'uploads/ckfinder/images/';

        return $base . $categoria . '/' . $pasta . '/' . $arquivoImagem;
    }

    public static function path(\Entity\Imagem $imagem = NULL)
    {
        if ($imagem === NULL) {
            return NULL;
        }

        $categoriaNome = $imagem->getPasta()->getCategoria()->getNome();
        $categoria = \Helpers\String::removeSpecial($categoriaNome);
        $pasta = $imagem->getPasta()->getCaminho();
        $base = 'uploads/ckfinder/images/';

        return $base . $categoria . '/' . $pasta;
    }    
}