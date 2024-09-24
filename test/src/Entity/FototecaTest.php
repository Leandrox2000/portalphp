<?php
namespace Entity;

use Entity\Imagem;

/**
 * Description of FototecaTest
 *
 * @author Eduardo
 */
class FototecaTest extends \PHPUnit_Framework_TestCase
{

    public function test_metodo_get_label()
    {
        //Declara a fototeca
        $fototeca = new Fototeca();
        $fototeca->setNome('teste');

        $this->assertEquals('teste', $fototeca->getLabel());
    }

    public function test_metodo_to_array()
    {

        $fototeca = new Fototeca();
        $fototeca->setId(1);
        $fototeca->setNome('teste');
        $fototeca->setDescricao('teste');
        $fototeca->setDataCadastro(new \DateTime('now'));
        $fototeca->setDataInicial(new \DateTime('now'));
        $fototeca->setDataFinal(new \DateTime('now'));
        $fototeca->setPublicado(1);
        $fototeca->setGalerias(new \Doctrine\Common\Collections\ArrayCollection());
        $fototeca->setCategoria(new \Entity\CategoriaFototeca());

        $array = array(
            "id" => 1,
            "dataCadastro" => new \DateTime('now'),
            "dataInicial" => new \DateTime('now'),
            "dataFinal" => new \DateTime('now'),
            "nome" => 'teste',
            "descricao" => 'teste',
            "publicado" => 1,
            "galerias" => new \Doctrine\Common\Collections\ArrayCollection(),
            "categoria" => new \Entity\CategoriaFototeca(),
        );

        $this->assertEquals($array, $fototeca->toArray());
    }

}
