<?php

namespace Entity;

/**
 * Description of LegislacaoTest
 *
 * @author Eduardo
 */
class BannerGeralCategoriaTest extends \PHPUnit_Framework_TestCase
{

    public function test_metodo_get_label()
    {
        $categoria = new BannerGeralCategoria();
        $categoria->setNome('teste');
        
        $this->assertEquals('teste', $categoria->getLabel());
    }

    public function test_metodo_to_array()
    {

        //Declara a fototeca
        $categoria = new BannerGeralCategoria();
        $categoria->setId(1);
        $categoria->setNome("Teste");
        $categoria->setSede(1);
        
        $array = array(
            "id" => 1,
            "nome" => "Teste",
            "sede" => 1,
        );


        $this->assertEquals($array, $categoria->toArray());
    }

}
