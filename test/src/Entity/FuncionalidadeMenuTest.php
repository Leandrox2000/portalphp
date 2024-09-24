<?php
namespace Entity;


/**
 * Description of FuncionalidadeMenuTest
 *
 * @author Join-ti
 */
class FuncionalidadeMenuTest extends BaseTest
{
  
    public function test_metodo_get_Label()
    {
        $entity = new FuncionalidadeMenu();
        $entity->setFuncionalidade("teste");
        
        $this->assertEquals("teste", $entity->getLabel());
    }
    
   

    public function test_metodo_toArray()
    {
        $entity = new FuncionalidadeMenu();
        $entity->setId(1);
        $entity->setEntidade('entidade');
        $entity->setFuncionalidade('funcionalidade');
        $entity->setUrl('www.google.com.br');

        $arr = array(
            'id' => 1,
            'entidade' => 'entidade',
            'funcionalidade' => 'funcionalidade',
            'url' => 'www.google.com.br',
        );
        
        $toArray = $entity->toArray();
        $this->assertEquals($toArray, $arr);
    }

}
