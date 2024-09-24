<?php

namespace Entity;

/**
 * Description of PerguntaTest
 *
 * @author Luciano
 */
class PublicacaoTest extends BaseTest
{

    public function test_inicializacao_do_construtor()
    {
        $publicacao = new Publicacao();

        $this->assertEquals(new \DateTime("now"), $publicacao->getDataCadastro());
    }

    public function test_metodo_set_titulo()
    {
        $publicacao = new Publicacao();

        $publicacao->setTitulo("Titulo");

        $this->assertEquals("Titulo", $publicacao->getTitulo());
    }

    public function test_metodo_set_autor()
    {
        $publicacao = new Publicacao();

        $publicacao->setAutor("Autor");

        $this->assertEquals("Autor", $publicacao->getAutor());
    }
    
    public function test_metodo_set_conteudo()
    {
        $publicacao = new Publicacao();

        $publicacao->setConteudo("Conteudo");

        $this->assertEquals("Conteudo", $publicacao->getConteudo());
    }
    
    public function test_metodo_set_paginas()
    {
        $publicacao = new Publicacao();

        $publicacao->setPaginas(157);
        
        $this->assertEquals(157, $publicacao->getPaginas());
    }
    
    public function test_metodo_set_edicao()
    {
        $publicacao = new Publicacao();

        $publicacao->setEdicao(2005);
        
        $this->assertEquals(2005, $publicacao->getEdicao());
    }

    
    public function test_metodo_set_data_inicial()
    {
        $publicacao = new Publicacao();

        $publicacao->setDataInicial(new \DateTime("now"));

        $this->assertEquals(new \DateTime("now"), $publicacao->getDataInicial());
    }
    
    public function test_metodo_set_data_final()
    {
        $publicacao = new Publicacao();

        $publicacao->setDataFinal(new \DateTime("now"));

        $this->assertEquals(new \DateTime("now"), $publicacao->getDataFinal());
    }
    
    public function test_metodo_set_publicado()
    {
        $publicacao = new Publicacao();

        $publicacao->setPublicado(1);
        $publicacao->setPublicado(0);

        $this->assertEquals(0, $publicacao->getPublicado());
    }
    
    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Número da páginas só aceita números
     */
    public function test_excecao_metodo_set_paginas()
    {
        $publicacao = new Publicacao();

        $publicacao->setPaginas("a");
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage O Status de Publicado deve ser 0 ou 1
     */
    public function test_excecao_metodo_set_publicado()
    {
        $publicacao = new Publicacao();

        $publicacao->setPublicado("a");
    }

    
    /**
     * 
     * @dataProvider data_provider_attributes
     */
    public function test_atributos_existem($attr)
    {
        $this->assertClassHasAttribute($attr, "Entity\\Publicacao");
    }

    public function data_provider_attributes()
    {
        return array(
            array("id"),
            array("titulo"),
            array("autor"),
            array("edicao"),
            array("paginas"),
            array("dataCadastro"),
            array("publicado"),
            array("categoria"),
        );
    }

    public function test_metodo_get_label()
    {
        $publicacao = new Publicacao();
        $publicacao->setTitulo("Titulo");
        $publicacao->setAutor("Autor");
        $publicacao->setConteudo("Conteudo");
        $publicacao->setEdicao(300);
        $publicacao->setPaginas(500);
        $publicacao->setPublicado(1);
        $publicacao->setCategoria(new PublicacaoCategoria());


        $this->assertNotEmpty($publicacao->getLabel());
    }

    public function test_retorno_metodo_getCategoria()
    {
        $publicacao = new Publicacao();
        $publicacao->setCategoria(new PublicacaoCategoria());

        $this->assertInstanceOf("Entity\\PublicacaoCategoria", $publicacao->getCategoria());
    }

    public function test_toArray()
    {
        $publicacao = new Publicacao();
        $categoria = new PublicacaoCategoria();
        $agora = new \DateTime("now");
        $imagem = new Imagem();
        
        $publicacao->setTitulo("Titulo");
        $publicacao->setAutor("Autor");
        $publicacao->setConteudo("Conteudo");
        $publicacao->setEdicao(300);
        $publicacao->setPaginas(500);
        $publicacao->setPublicado(1);
        $publicacao->setCategoria($categoria);
        $publicacao->setDataInicial(new \DateTime("2014-05-15"));
        $publicacao->setDataFinal(new \DateTime("2014-05-22"));
        $publicacao->setDataPublicacao(new \DateTime("2005-12-11"));
        $publicacao->setImagem($imagem);
        $publicacao->setOrdem(1);
        
        $publicacaoArray = array(
                'id'                => null,
                'titulo'            => "Titulo",
                'autor'             => "Autor",
                'conteudo'          => "Conteudo",
                'categoria'         => $categoria,
                'edicao'            => 300,
                'paginas'           => 500,
                'ordem'             => 1,
                'dataInicial'       => new \DateTime("2014-05-15"),
                'dataFinal'         => new \DateTime("2014-05-22"),
                'dataCadastro'      => $agora,
                'dataPublicacao'    => new \DateTime("2005-12-11"),
                'imagem'            => $imagem,
        );
        
        $this->assertEquals($publicacaoArray, $publicacao->toArray());
    }

}
