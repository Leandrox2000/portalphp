<?php

namespace Entity;

/**
 * Description of AtaTest
 *
 * @author Eduardo
 */
class AtaTest extends \PHPUnit_Framework_TestCase
{

    public function test_metodo_get_label()
    {
        //Declara a ata
        $ata = new Ata();
        $ata->setNome('teste');

        $this->assertEquals('teste', $ata->getLabel());
    }

    public function test_metodo_to_array()
    {

        $ata = new Ata();
        $ata->setNome("teste");
        $ata->setArquivo("teste");
        $ata->setDataCadastro(new \DateTime('now'));
        $ata->setDataReuniao(new \DateTime('now'));
        $ata->setDataInicial(new \DateTime('now'));
        $ata->setDataFinal(new \DateTime('now'));
        $ata->setDescricao("descricao de teste");
        $ata->setPublicado(1);
        $ata->setArquivo('arquivo.txt');
        
        
        $array = array(
            "id" => null,
            "dataCadastro" => new \DateTime('now'),
            "dataReuniao" => new \DateTime('now'),
            "dataInicial" => new \DateTime('now'),
            "dataFinal" => new \DateTime('now'),
            "nome" => 'teste',
            "descricao" => "descricao de teste",
            "arquivo" => 'arquivo.txt',
            "publicado" => 1
        );

        $this->assertEquals($array, $ata->toArray());
    }

}
