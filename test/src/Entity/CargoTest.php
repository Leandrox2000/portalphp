<?php

namespace Entity;


/**
 * Description of CargoTest
 *
 * @author Luciano
 */
class CargoTest extends BaseTest
{
    public function test_metodo_set_cargo()
    {
        $cargo = new Cargo();
        
        $cargo->setCargo("Teste");
        
        $this->assertEquals("Teste", $cargo->getCargo());
    }
    
    public function test_metodo_get_id()
    {
        $cargo = new Cargo();
        
        $this->assertEmpty($cargo->getId());
    }
    
    
    public function test_metodo_get_Label()
    {
        $cargo = new Cargo();
        $cargo->setCargo("Cargo");
        
        $this->assertEquals("Cargo", $cargo->getLabel());
    }
    
    /**
     * 
     * @dataProvider data_provider_attributes
     */
    public function test_atributos_existem($attr)
    {
        $this->assertClassHasAttribute($attr, "Entity\\Cargo");
    }
    
    public function data_provider_attributes()
    {
            return array(
                array("id"),
                array("cargo"),
            );
    }

    public function test_metodo_toArray()
    {
        $cargo = new Cargo();
        $cargo->setCargo('teste');
        $cargo->setId(1);

        $arr = array(
            'id' => 1,
            'cargo' => 'teste',
        );
        $toArray = $cargo->toArray();

        $this->assertEquals($toArray, $arr);
    }

}
