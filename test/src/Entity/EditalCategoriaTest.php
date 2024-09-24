<?php

namespace Entity;

use Entity\Site;

/**
 * Description of EditalCategoriaTest
 *
 * @author Eduardo
 */
class EditalCategoriaTest extends \PHPUnit_Framework_TestCase
{

    public function test_metodo_get_label()
    {
        //Declara a noticia
        $editalCategoria = new EditalCategoria();
        $editalCategoria->setNome('teste');

        $this->assertEquals('teste', $editalCategoria->getLabel());
    }

    public function test_metodo_to_array()
    {
 
        $editalCategoria = new EditalCategoria();
        $editalCategoria->setNome('teste');
        $editalCategoria->setEditais(new \Doctrine\Common\Collections\ArrayCollection());
        
        $array = array(
            'id' => null,
            'nome' => 'teste',
            'editais' => new \Doctrine\Common\Collections\ArrayCollection()
        );

        $this->assertEquals($array, $editalCategoria->toArray());
    }

}
