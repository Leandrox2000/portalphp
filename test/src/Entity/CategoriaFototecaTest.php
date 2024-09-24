<?php

namespace Entity;


/**
 * Description of CategoriaFototecaTest
 *
 * @author Luciano
 */
class CategoriaFototecaTest extends BaseTest
{

    public function test_metodo_get_Label()
    {
        $catFot = new CategoriaFototeca();
        $catFot->setNome('teste');
        
        $this->assertEquals('teste', $catFot->getLabel());
    }
    
    public function test_metodo_toArray()
    {
        $catFot = new CategoriaFototeca();
        $catFot->setId(1);
        $catFot->setNome('nome teste');

        $arr = array(
            'id' => 1,
            'nome' => 'nome teste',
        );

        $this->assertEquals($arr, $catFot->toArray());
    }

}
