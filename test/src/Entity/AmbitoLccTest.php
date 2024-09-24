<?php

namespace Entity;

/**
 * Description of AmbitoLccTest
 *
 * @author Jointi
 */
class AmbitoLccTest extends \PHPUnit_Framework_TestCase
{

    public function test_metodo_get_label()
    {
        $entity = new AmbitoLcc();
        $entity->setNome('teste');
        $this->assertEquals('teste', $entity->getLabel());
    }

    public function test_metodo_toArray()
    {
        $entity = new AmbitoLcc();
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
