<?php

namespace LibraryController;

/**
 * Implemntar isso em todos os Curds do CMS
 *
 * @author Luciano
 */
interface CrudControllerInterface
{
    public function lista();
    
    public function pagination();
    
    public function form($id);
    
    public function salvar();
    
    public function delete();
    
}
