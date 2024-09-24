<?php

namespace Entity;

/**
 * Description of VideoTest
 *
 * @author Eduardo
 */
class PublicacaoIntroducaoTest extends \PHPUnit_Framework_TestCase {


    public function test_metodo_get_label() {
        $introducao = new PublicacaoIntroducao();
        $introducao->setConteudo('teste');
        $this->assertEquals('teste', $introducao->getLabel());
    }

    public function test_metodo_toArray() {
        $dataCadastro = new \DateTime();

        $introducao = new PublicacaoIntroducao();
        $introducao->setId(1);
        $introducao->setConteudo('teste');
        $introducao->setDataCadastro($dataCadastro);

        $array = array(
            "id" => 1,
            "conteudo" => 'teste',
            "dataCadastro" => $dataCadastro,
        );

        $this->assertEquals($array, $introducao->toArray());
    }

}
