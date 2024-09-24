<?php

namespace Entity;

/**
 * Description of ImagemCategoriaTest
 *
 * @author Eduardo
 */
class ImagemCategoriaTest extends \PHPUnit_Framework_TestCase
{

    public function test_metodo_get_label()
    {
        //Declara a categoria
        $imagemCategoria = new ImagemCategoria();
        $imagemCategoria->setNome('Categoria de teste');
        
        $this->assertEquals('Categoria de teste', $imagemCategoria->getLabel());
    }

    public function test_metodo_to_array()
    {
        //Declara a categoria
        $imagemCategoria = new ImagemCategoria();
        $imagemCategoria->setId(1);
        $imagemCategoria->setNome('Categoria de teste');

        $array = array(
            'id' => 1,
            'nome' => 'Categoria de teste'
        );

        $this->assertEquals($array, $imagemCategoria->toArray());
    }

}
