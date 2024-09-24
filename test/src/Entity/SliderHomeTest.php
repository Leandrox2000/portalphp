<?php

namespace Entity;

/**
 * Description of BannerHomeTest
 *
 * @author Eduardo
 */
class SliderHomeTest extends \PHPUnit_Framework_TestCase {

    public function test_metodo_get_label() {
        //Declara a noticia
        $sliderHome = new SliderHome();
        $sliderHome->setNome('teste');

        $this->assertEquals('teste', $sliderHome->getLabel());
    }

    public function test_metodo_to_array() {
        $sliderHome = new SliderHome();
        $sliderHome->setId(1);
        $sliderHome->setNome('teste');
        $sliderHome->setDataCadastro(new \DateTime('now'));
        $sliderHome->setDataInicial(new \DateTime('now'));
        $sliderHome->setDataFinal(new \DateTime('now'));
        $sliderHome->setDescricao('teste');
        $sliderHome->setImagem(new \Entity\Imagem());
        $sliderHome->setSites(new \Doctrine\Common\Collections\ArrayCollection());
        $sliderHome->setPublicado(1);
        $sliderHome->setPropriedadeSede(1);
        $sliderHome->setW(1);
        $sliderHome->setX1(1);
        $sliderHome->setX2(1);
        $sliderHome->setY1(1);
        $sliderHome->setY2(1);
        $sliderHome->setH(1);

        $array = array(
            "id" => 1,
            "nome" => 'teste',
            "dataCadastro" => new \DateTime('now'),
            "dataInicial" => new \DateTime('now'),
            "dataFinal" => new \DateTime('now'),
            "descricao" => 'teste',
            "imagem" => new \Entity\Imagem(),
            "sites" => new \Doctrine\Common\Collections\ArrayCollection(),
            "publicado" => 1,
            "propriedadeSede" => 1,
            "x1" => 1,
            "x2" => 1,
            "y1" => 1,
            "y2" => 1,
            "w" => 1,
            "h" => 1
        );


        $this->assertEquals($array, $sliderHome->toArray());
    }

}
