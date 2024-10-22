<?php

namespace Entity;

/**
 * Description of TipoLccTest
 *
 * @author Jointi
 */
class TipoLccTest extends \PHPUnit_Framework_TestCase
{

    public function test_metodo_get_label()
    {
        $entity = new TipoLcc();
        $entity->setNome('teste');
        $this->assertEquals('teste', $entity->getLabel());
    }

    public function test_metodo_toArray()
    {
        $entity = new TipoLcc();
        $entity->setId(1);

        $entity->setNome('teste');

        $arr = array(
            'id' => 1,
            'nome' => 'teste',
        );

        $toArray = $entity->toArray();

        $this->assertEquals($toArray, $arr);
    }

}
