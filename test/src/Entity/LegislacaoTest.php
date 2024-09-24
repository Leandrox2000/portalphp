<?php

namespace Entity;

/**
 * Description of LegislacaoTest
 *
 * @author Eduardo
 */
class LegislacaoTest extends \PHPUnit_Framework_TestCase
{

    public function test_metodo_get_label()
    {
        $legislacao = new Legislacao();
        $legislacao->setTitulo('teste');
        
        $this->assertEquals('teste', $legislacao->getLabel());
    }

    public function test_metodo_to_array()
    {

        //Declara a fototeca
        $legislacao = new Legislacao();
        $legislacao->setId(1);
        $legislacao->setDataCadastro(new \DateTime('now'));
        $legislacao->setDataInicial(new \DateTime('now'));
        $legislacao->setDataFinal(new \DateTime('now'));
        $legislacao->setDataLegislacao(new \Datetime("now"));
        $legislacao->setTitulo('teste');
        $legislacao->setDescricao('teste');
        $legislacao->setUrl('www.teste.com');
        $legislacao->setSites(new \Doctrine\Common\Collections\ArrayCollection());
        $legislacao->setCategoriaLegislacao(new \Entity\CategoriaLegislacao());
        $legislacao->setPropriedadeSede(1);
        $legislacao->setPublicado(1);
        $legislacao->setArquivo('teste.jpg');

        $array = array(
            "id" => 1,
            "dataCadastro" => new \DateTime('now'),
            "dataIncial" => new \DateTime('now'),
            "dataFinal" => new \DateTime('now'),
            "dataLegislacao" => new \DateTime('now'),
            "titulo" => 'teste',
            "descricao" => 'teste',
            "url" => 'www.teste.com',
            "sites" => new \Doctrine\Common\Collections\ArrayCollection(),
            "categoriaLegislacao" => new \Entity\CategoriaLegislacao(),
            "propriedadeSede" => 1,
            "publicado" => 1,
            "arquivo" => "teste.jpg"
        );


        $this->assertEquals($array, $legislacao->toArray());
    }

}
