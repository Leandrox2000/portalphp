<?php

namespace Entity;


/**
 * Description of CategoriaDicionarioTest
 *
 * @author Luciano
 */
class CategoriaDicionarioTest extends BaseTest
{

    public function test_metodo_get_Label()
    {
        $catDic = new CategoriaDicionario();
        $catDic->setNome('teste');
        
        $this->assertEquals("teste", $catDic->getLabel());
    }
    
    public function test_metodo_toArray()
    {
        $catDic = new CategoriaDicionario();
        $catDic->setId(1);
        $catDic->setNome('nome teste');

        $arr = array(
            'id' => 1,
            'nome' => 'nome teste',
        );

        $this->assertEquals($arr, $catDic->toArray());
    }

}
