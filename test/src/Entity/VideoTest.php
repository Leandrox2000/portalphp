<?php

namespace Entity;

/**
 * Description of VideoTest
 *
 * @author Eduardo
 */
class VideoTest extends \PHPUnit_Framework_TestCase {

    /**
     * 
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage A publicação deve ser 0 ou 1
     */
    public function test_publicado_aceita_diferente_zero_ou_um() {
        $video = new Video();
        $video->setPublicado(2);
    }

    public function test_metodo_get_label() {
        $video = new Video();
        $video->setNome("teste");
        $this->assertEquals("teste", $video->getLabel());
    }

    public function test_metodo_toArray() {

        $video = new Video();
        $video->setId(1);
        $video->setDataCadastro(new \DateTime('now'));
        $video->setDataInicial(new \DateTime('now'));
        $video->setDataFinal(new \DateTime('now'));
        $video->setNome('teste');
        $video->setLink('www.teste.com.br');
        $video->setPublicado(1);
        $video->setSites(new \Doctrine\Common\Collections\ArrayCollection());
        $video->setPropriedadeSede(1);
        $video->setAutor("autor");
        $video->setResumo("resumo");
        $video->setNomeYoutube("nome");
        $video->setRelacionados(new \Doctrine\Common\Collections\ArrayCollection());
        $video->setDestacado(1);
        
        $array = array(
            "id" => 1,
            "nome" => "teste",
            "dataCadastro" => new \DateTime('now'),
            "dataInicial" => new \DateTime('now'),
            "dataFinal" => new \DateTime('now'),
            "nome" => 'teste',
            "link" => 'www.teste.com.br',
            "sites" => new \Doctrine\Common\Collections\ArrayCollection(),
            "publicado" => 1,
            "propriedadeSede" => 1,
            "autor" => "autor",
            "resumo" => "resumo",
            "nomeYotube" => "nome",
            "relacionados" => new \Doctrine\Common\Collections\ArrayCollection(),
            "destacado" => 1,

        );

        $this->assertEquals($array, $video->toArray());
    }

}
