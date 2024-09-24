<?php

namespace Entity;

/**
 * Description of VideoTest
 *
 * @author Eduardo
 */
class DiretoriaTest extends \PHPUnit_Framework_TestCase {


    public function test_metodo_get_label() {
        $diretoria = new Diretoria();
        $diretoria->setFuncionario(new Funcionario());
        $diretoria->getFuncionario()->setNome('teste');
        $this->assertEquals('teste', $diretoria->getLabel());
    }

    public function test_metodo_toArray() {
        $diretoria = new Diretoria();
        $diretoria->setId(1);
        $diretoria->setFuncionario(new Funcionario());
        $diretoria->setOrdem(1);
        $diretoria->setPublicado(1);
        $array = array(
            "id" => 1,
            "funcionario" => new Funcionario(),
            "ordem" => 1,
            "publicado" => 1,
        );

        $this->assertEquals($array, $diretoria->toArray());
    }

}
