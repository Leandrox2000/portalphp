<?php

namespace Entity;

use Entity\ImagemPasta;

/**
 * Description of ImagemTest
 *
 * @author Eduardo
 */
class ImagemTest extends \PHPUnit_Framework_TestCase
{

    public function test_get_label()
    {
        //Seta o nome da imagem e teste o getLabel
        $imagem = new Imagem();
        $imagem->setNome('teste');

        $this->assertEquals('teste', $imagem->getLabel());
    }

    public function test_metodo_to_array()
    {

        //Seta os dados da imagem
        $imagem = new Imagem();
        $imagem->setId(1);
        $imagem->setNome('Imagem de teste');
        $imagem->setPalavrasChave('lalala; lololo; lelele');
        $imagem->setCredito('crédito de teste');
        $imagem->setLegenda('legenda de teste');
        $imagem->setGalerias(new \Doctrine\Common\Collections\ArrayCollection());
        $imagem->setPasta(new ImagemPasta());
        $imagem->setImagem('teste.jpg');
        $imagem->setPublicacoes(new \Doctrine\Common\Collections\ArrayCollection());
        $imagem->setBibliografia(new \Doctrine\Common\Collections\ArrayCollection());
        $imagem->setBannersHome(new \Doctrine\Common\Collections\ArrayCollection());
        $imagem->setBackgroundsHome(new \Doctrine\Common\Collections\ArrayCollection());

        $array = array(
            'id' => 1,
            'dataCadastro' => new \DateTime('now'),
            'nome' => 'Imagem de teste',
            'palavrasChave' => 'lalala; lololo; lelele',
            'legenda' => 'legenda de teste',
            'credito' => 'crédito de teste',
            'imagem' => 'teste.jpg',
            'pasta' => new ImagemPasta(),
            'galerias' => new \Doctrine\Common\Collections\ArrayCollection(),
            'publicacoes' => new \Doctrine\Common\Collections\ArrayCollection(),
            'bibliografias' => new \Doctrine\Common\Collections\ArrayCollection(),
            'bannersHome' => new \Doctrine\Common\Collections\ArrayCollection(),
            'backgroundsHome' => new \Doctrine\Common\Collections\ArrayCollection(),
        );

        $this->assertEquals($array, $imagem->toArray());
    }
}
