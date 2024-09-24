<?php

namespace Entity;

use Entity\Site;

/**
 * Description of ConselheiroTest
 *
 * @author Eduardo
 */
class ConselheiroTest extends \PHPUnit_Framework_TestCase
{

    public function test_metodo_get_label()
    {
        //Declara o conselheiro
        $conselheiro = new Conselheiro();
        $conselheiro->setNome('teste');

        $this->assertEquals('teste', $conselheiro->getLabel());
    }

    public function test_metodo_to_array()
    {

        $conselheiro = new Conselheiro();
        $conselheiro->setNome('teste');
        $conselheiro->setInstituicao('teste');
        $conselheiro->setPropriedadeSede(1);
        $conselheiro->setTipo('teste');
        $conselheiro->setDataInicial(new \DateTime('now'));
        $conselheiro->setDataFinal(new \DateTime('now'));
        $conselheiro->setDataCadastro(new \DateTime('now'));
        $conselheiro->setPublicado(1);

        $array = array(
            "id" => null,
            "nome" => 'teste',
            "instituicao" => 'teste',
            "propriedadeSede" => 1,
            "tipo" => 'teste',
            "dataInicial" => new \DateTime('now'),
            "dataFinal" => new \DateTime('now'),
            "dataCadastro" => new \DateTime('now'),
            "publicado" => 1,
        );

        $this->assertEquals($array, $conselheiro->toArray());
    }

}
