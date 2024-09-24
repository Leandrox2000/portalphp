<?php

namespace Entity;

/**
 * Description of TypeTest
 *
 * @author Luciano
 */
class TypeTest extends BaseTest
{
    
    public function test_contrutor()
    {
        $type = new Type(5, "Nome");
        
        $this->assertEquals(5, $type->getId());
        $this->assertEquals("Nome", $type->getNome());
    }
    
    
    public function test_type()
    {
        $type = new Type("", "");
        $type->setId(5);
        $type->setNome("Nome");
        
        $this->assertEquals(5, $type->getId());
        $this->assertEquals("Nome", $type->getNome());
    }
    
    public function test_getLabel()
    {
        $type = new Type(5, "Nome");
        
        $this->assertNotEmpty($type->getLabel());
    }
    
    public function teste_metodo_toArray()
    {
        $type = new Type("", "");
        $type->setId(1);
        $type->setNome('jonas');
        
        $array = array(
            'id' => 1,
            'nome' => 'jonas',
        );
        
        $this->assertEquals($array, $type->toArray());
    }
    
}
