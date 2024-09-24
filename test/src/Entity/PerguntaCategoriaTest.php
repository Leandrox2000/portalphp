<?php

namespace Entity;

/**
 * Description of PerguntaCategoriaTest
 *
 * @author Luciano
 */
class PerguntaCategoriaTest extends BaseTest
{
    public function test_metodo_set_categoria()
    {
        $categoria = new PerguntaCategoria();
        
        $categoria->setCategoria("Categoria");
        
        $this->assertEquals("Categoria", $categoria->getCategoria());
    }
    
    
    
    /**
     * 
     * @dataProvider data_provider_attributes
     */
    public function test_atributos_existem($attr)
    {
        $this->assertClassHasAttribute($attr, "Entity\\PerguntaCategoria");
    }
    
    public function data_provider_attributes()
    {
            return array(
                array("id"),
                array("categoria"),
            );
    }

    public function test_metodo_get_label()
    {
        $categoria = new PerguntaCategoria();
        $categoria->setCategoria("Categoria");
        
        
        $this->assertNotEmpty($categoria->getLabel());
    }
    
    public function test_metodo_to_string()
    {
        $categoria = new PerguntaCategoria();
        $categoria->setCategoria("Categoria");
        
        $this->assertEquals("Categoria", $categoria);
    }

    
    public function test_toArray()
    {
        $categoria = new PerguntaCategoria();

        $categoria->setCategoria("Nova Categoria");
        
        $array = array(
                        'id'=>"",
                        'categoria'=>"Nova Categoria",
        );
        
        $this->assertEquals($array, $categoria->toArray());
    }


    
    
}
