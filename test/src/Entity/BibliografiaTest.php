<?php

namespace Entity;

/**
 * Description of BibliografiaTest
 *
 * @author Eduardo
 */
class BibliografiaTest extends \PHPUnit_Framework_TestCase
{

    public function test_metodo_get_label()
    {
        //Declara a agenda
        $bibliografia = new Bibliografia();
        $bibliografia->setTitulo('teste');

        $this->assertEquals('teste', $bibliografia->getLabel());
    }

    public function test_metodo_to_array()
    {

        $bibliografia = new Bibliografia();
        $bibliografia->setTitulo('teste');
        $bibliografia->setConteudo('teste');
        $bibliografia->setDataCadastro(new \DateTime('now'));
        $bibliografia->setDataInicial(new \DateTime('now'));
        $bibliografia->setDataFinal(new \DateTime('now'));
        $bibliografia->setImagem(new \Entity\Imagem());
        $bibliografia->setPublicado(0);

       
        $array = array(
            "id" => null,
            "titulo" => 'teste',
            "conteudo" => 'teste',
            "dataInicial" => new \DateTime('now'),
            "dataFinal" => new \DateTime('now'),
            "dataCadastro" => new \DateTime('now'),
            "publicado" => 0,
            "imagem" => new \Entity\Imagem()
        );

        $this->assertEquals($array, $bibliografia->toArray());
    }

}
