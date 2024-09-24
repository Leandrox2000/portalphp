<?php

namespace Entity;

use Entity\Site;

/**
 * Description of GaleriaTest
 *
 * @author Eduardo
 */
class GaleriaTest extends \PHPUnit_Framework_TestCase
{

    public function test_metodo_get_label()
    {
        //Declara a noticia
        $galeria = new Galeria();
        $galeria->setTitulo('teste');

        $this->assertEquals('teste', $galeria);
    }

    public function test_metodo_to_array()
    {
 
        $galeria = new Galeria();
        $galeria->setId(1);
        $galeria->setDataCadastro(new \DateTime('now'));
        $galeria->setDataInicial(new \DateTime('now'));
        $galeria->setDataFinal(new \DateTime('now'));
        $galeria->setDescricao('teste');
        $galeria->setTitulo('teste');
        $galeria->setImagens(new \Doctrine\Common\Collections\ArrayCollection());
        $galeria->setPaginasEstaticas(new \Doctrine\Common\Collections\ArrayCollection());
        $galeria->setPropriedadeSede(1);
        $galeria->setPublicado(1);
        $galeria->setFototecas(new \Doctrine\Common\Collections\ArrayCollection());
        $galeria->setNoticias(new \Doctrine\Common\Collections\ArrayCollection());
        
        $array = array(
            "id" => 1,
            "dataCadastro" => new \DateTime('now'),
            "dataInicial" => new \DateTime('now'),
            "dataFinal" => new \DateTime('now'),
            "titulo" => 'teste',
            "descricao" => 'teste',
            "sites" => new \Doctrine\Common\Collections\ArrayCollection(),
            "noticias" => new \Doctrine\Common\Collections\ArrayCollection(),
            "imagens" => new \Doctrine\Common\Collections\ArrayCollection(),
            "publicado" => 1,
            "propriedadeSede" => 1,
            "paginasEstaticas" => new \Doctrine\Common\Collections\ArrayCollection(),
            "fototecas" => new \Doctrine\Common\Collections\ArrayCollection(),
        );
        
        $this->assertEquals($array, $galeria->toArray());
    }

}
