<?php
namespace Entity;


/**
 * Description of FuncionalidadeSiscauTest
 *
 * @author Join-ti
 */
class FuncionalidadeSiscauTest extends BaseTest
{
  
    public function test_metodo_get_Label()
    {
        $entity = new FuncionalidadeSiscau();
        $entity->setSigla("SEDE");
        $this->assertEquals("SEDE", $entity->getLabel());
    }
    
   

    public function test_metodo_toArray()
    {
        $entity = new FuncionalidadeSiscau();
        $entity->setId(1);
        $entity->setController('controller');
        $entity->setAcao('acao');
        $entity->setSigla('CADAS_INSERIR');

        $arr = array(
            'id' => 1,
            'controller' => 'controller',
            'acao' => 'acao',
            'sigla' => 'CADAS_INSERIR'
        );
        
        $toArray = $entity->toArray();
        $this->assertEquals($toArray, $arr);
    }

}
