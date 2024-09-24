<?php

namespace Entity;

/**
 * Description of VideoTest
 *
 * @author Eduardo
 */
class EnderecoRodapeTest extends \PHPUnit_Framework_TestCase {

    /**
     *
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage A publicação deve ser 0 ou 1
     */
    public function test_publicado_aceita_diferente_zero_ou_um() {
        $endereco = new EnderecoRodape();
        $endereco->setPublicado(2);
    }

    public function test_metodo_get_label() {
        $endereco = new EnderecoRodape();
        $endereco->setEndereco("Teste");
        $this->assertEquals("Teste", $endereco->getLabel());
    }

    public function test_metodo_toArray() {

        $endereco = new EnderecoRodape();
        $endereco->setDataCadastro(new \DateTime('now'));
        $endereco->setDataInicial(new \DateTime('now'));
        $endereco->setDataFinal(new \DateTime('now'));
        $endereco->setEndereco("Teste");
        $endereco->setPublicado(1);

        $array = array(
            "id" => NULL,
            "endereco" => 'Teste',
            "dataCadastro" => new \DateTime('now'),
            "dataInicial" => new \DateTime('now'),
            "dataFinal" => new \DateTime('now'),
            "publicado" => 1,
        );

        $this->assertEquals($array, $endereco->toArray());
    }

}
