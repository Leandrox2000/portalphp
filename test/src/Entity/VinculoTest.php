<?php

namespace Entity;

/**
 * Description of VinculoTest
 *
 * @author Luciano
 */
class VinculoTest extends BaseTest
{

    public function test_metodo_get_label()
    {
        $vinculo = new Vinculo();
        $vinculo->setNome("teste");
        $this->assertEquals("teste", $vinculo->getLabel());
    }

    public function test_toArray()
    {
        $vinculo = new Vinculo();
        $vinculo->setId(1);
        $vinculo->setNome("teste");

        $array = array(
            'id' => 1,
            'nome' => "teste",
        );

        $this->assertEquals($array, $vinculo->toArray());
    }

}
