<?php

namespace Entity;

/**
 * Description of PerguntaCategoriaTest
 *
 * @author Luciano
 */
class CategoriaLegislacaoTest extends BaseTest
{

    public function test_metodo_get_label()
    {
        $categoria = new CategoriaLegislacao();
        $categoria->setNome("teste");
        $this->assertEquals("teste", $categoria->getLabel());
    }

    public function test_toArray()
    {
        $categoria = new CategoriaLegislacao();
        $categoria->setId(1);
        $categoria->setNome("teste");

        $array = array(
            'id' => 1,
            'nome' => "teste",
        );

        $this->assertEquals($array, $categoria->toArray());
    }

}
