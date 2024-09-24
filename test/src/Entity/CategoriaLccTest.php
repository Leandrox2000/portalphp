<?php

namespace Entity;

/**
 * Description of CategoriaLccTest
 *
 * @author Jointi
 */
class CategoriaLccTest extends \PHPUnit_Framework_TestCase
{

    public function test_metodo_get_label()
    {
        $entity = new CategoriaLcc();
        $entity->setNome('teste');
        $this->assertEquals('teste', $entity->getLabel());
    }

    public function test_metodo_toArray()
    {
        $entity = new CategoriaLcc();
        $entity->setId(1);
        $entity->setNome('teste');
        $entity->setPermiteExcluir(1);
        
        $arr = array(
            'id' => 1,
            'nome' => 'teste',
            'permiteExcluir' => 1
        );

        $toArray = $entity->toArray();

        $this->assertEquals($toArray, $arr);
    }

}
