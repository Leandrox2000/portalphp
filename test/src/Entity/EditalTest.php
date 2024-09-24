<?php

namespace Entity;

/**
 * Description of EditalTest
 *
 * @author Eduardo
 */
class EditalTest extends \PHPUnit_Framework_TestCase
{

    public function test_metodo_get_label()
    {
        //Declara a noticia
        $edital = new Edital();
        $edital->setNome('teste');

        $this->assertEquals('teste', $edital->getLabel());
    }

    public function test_metodo_to_array()
    {

        $edital = new Edital();
        $edital->setArquivo('arquivo.txt');
        $edital->setConteudo('teste');
        $edital->setNome('teste');
        $edital->setDataInicial(new \DateTime('now'));
        $edital->setDataFinal(new \DateTime('now'));
        $edital->setDataCadstro(new \DateTime('now'));
        $edital->setPublicado(1);
        $edital->setPropriedadeSede(1);
        $edital->setCategoria(new \Entity\EditalCategoria());
        $edital->setStatus(new \Entity\EditalStatus());
        $edital->setSites(new \Doctrine\Common\Collections\ArrayCollection());

        $array = array(
            "id" => null,
            "nome" => 'teste',
            "conteudo" => 'teste',
            "arquivo" => 'arquivo.txt',
            "dataInicial" => new \DateTime('now'),
            "dataFinal" => new \DateTime('now'),
            "dataCadastro" => new \DateTime('now'),
            "publicado" => 1,
            "propriedadeSede" => 1,
            "categoria" => new \Entity\EditalCategoria(),
            "status" => new \Entity\EditalStatus(),
            "sites" => new \Doctrine\Common\Collections\ArrayCollection()
        );

        $this->assertEquals($array, $edital->toArray());
    }

}
