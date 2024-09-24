<?php
namespace Entity;

use Entity\Site;

/**
 * Description of NoticiaTest
 *
 * @author Eduardo
 */
class NoticiaTest extends \PHPUnit_Framework_TestCase
{

    public function test_metodo_get_label()
    {
        //Declara a noticia
        $noticia = new Noticia();
        $noticia->setTitulo('teste');

        $this->assertEquals('teste', $noticia->getLabel());
    }

    public function test_metodo_to_array()
    {
        //Declara os sites
        $sites = new \Doctrine\Common\Collections\ArrayCollection();
        $sites->add(new Site());

        //Declara a categoria
        $noticia = new Noticia();
        $noticia->setId(1);
        $noticia->setTitulo('teste');
        $noticia->setImagem(new \Entity\Imagem());
        $noticia->setDataCadastro(new \DateTime('now'));
        $noticia->setDataInicial(new \DateTime('now'));
        $noticia->setDataFinal(new \DateTime('now'));
        $noticia->setPalavrasChave('lalala; lololo; lelele');
        $noticia->setPropriedadeSede(1);
        $noticia->setPublicado(1);
        $noticia->setConteudo('teste');
        $noticia->setSites($sites);
        $noticia->setComentarios(new \Doctrine\Common\Collections\ArrayCollection());
        $noticia->setGalerias(new \Doctrine\Common\Collections\ArrayCollection());
        $noticia->setFlagNoticia("teste");
        $noticia->setSlug('teste');
        
        //Monta o array com os dados
        $array = array(
            'id' => 1,
            "titulo" => 'teste',
            'palavrasChave' => 'lalala; lololo; lelele',
            'imagem' => new \Entity\Imagem(),
            "conteudo" => 'teste',
            "dataCadastro" => new \DateTime('now'),
            "dataInicial" => new \DateTime('now'),
            "dataFinal" => new \DateTime('now'),
            "publicado" => 1,
            "propriedadeSede" => 1,
            "sites" => $sites,
            "galerias" => new \Doctrine\Common\Collections\ArrayCollection(),
            "comentarios" => new \Doctrine\Common\Collections\ArrayCollection(),
            "slug" => "teste",
            "flagNoticia" => "teste"
        );
                
        $this->assertEquals($array, $noticia->toArray());
    }

}
