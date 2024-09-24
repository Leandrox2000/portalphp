<?php

namespace Exception;

/**
 * Classe de Exceçãodo Controller
 * 
 */
class NotFoundException extends \Exception {

    /**
     * Exeption do Controller
     * 
     * @param string $message
     * @throws \Exception
     */
    public function __construct($message = "Erro 404 - Página não encontrada") 
    {
        header('HTTP/1.0 404 Not Found');
        header('Content-type: text/html; charset=UTF-8');
        parent::__construct($message);
    }
    
}