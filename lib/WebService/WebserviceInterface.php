<?php
namespace WebService;


interface WebserviceInterface
{

    public function getWsdl();
    
    public function getLocation();

    public function setWsdl($wsdl);

    public function setLocation($location);
}
