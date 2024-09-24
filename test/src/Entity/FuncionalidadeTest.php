<?php
namespace Entity;


/**
 * Description of FuncionalidadeTest
 *
 * @author Luciano
 */
class FuncionalidadeTest extends BaseTest
{
  
    public function test_metodo_get_Label()
    {
        $entity = new Funcionalidade();
        $entity->setNome("teste");
        
        $this->assertEquals("teste", $entity->getLabel());
    }
    
   

    public function test_metodo_toArray()
    {
        $entity = new Funcionalidade();
        $entity->setNome('teste');
        $entity->setId(1);

        $arr = array(
            'id' => 1,
            'nome' => 'teste',
        );
        $toArray = $entity->toArray();

        $this->assertEquals($toArray, $arr);
    }

}
