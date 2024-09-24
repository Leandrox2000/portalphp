<?php

namespace LibraryController;

/**
 * Description of FrontControllerInterface
 *
 * @author Luciano
 */
interface FrontControllerInterface
{
    
    public function setBasePath();
    
    public function setController($controller);
    
    public function setAction($action);
    
    public function setParams(array $params);
    
    public function run();
    
}