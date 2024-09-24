<?php

namespace Entity;

use Entity\Site;

/**
 * Description of EditalStatusTest
 *
 * @author Eduardo
 */
class EditalStatusTest extends \PHPUnit_Framework_TestCase
{

    public function test_metodo_get_label()
    {
        //Declara a noticia
        $editalStatus = new EditalStatus();
        $editalStatus->setNome('teste');

        $this->assertEquals('teste', $editalStatus->getLabel());
    }

    public function test_metodo_to_array()
    {
 
        $editalStatus = new EditalStatus();
        $editalStatus->setNome('teste');
        $editalStatus->setEditais(new \Doctrine\Common\Collections\ArrayCollection());
        
        $array = array(
            'id' => null,
            'nome' => 'teste',
            'editais' => new \Doctrine\Common\Collections\ArrayCollection()
        );

        $this->assertEquals($array, $editalStatus->toArray());
    }

}
