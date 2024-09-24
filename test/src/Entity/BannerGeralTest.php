<?php

namespace Entity;

/**
 * Description of LegislacaoTest
 *
 * @author Eduardo
 */
class BannerGeralTest extends \PHPUnit_Framework_TestCase
{

    public function test_metodo_get_label()
    {
        $banner = new BannerGeral();
        $banner->setNome('teste');
        
        $this->assertEquals('teste', $banner->getLabel());
    }

    public function test_metodo_to_array()
    {
        $categoria = new BannerGeralCategoria();
        $sites = new \Doctrine\Common\Collections\ArrayCollection();
        $imagem = new Imagem();
        
        
        //Declara a fototeca
        $banner = new BannerGeral();
        $banner->setDataCadastro(new \DateTime("now"));
        $banner->setDataInicial(new \DateTime("now"));
        $banner->setDataFinal(new \DateTime("now"));
        $banner->setId(1);
        $banner->setNome("Teste");
        $banner->setPublicado(1);
        $banner->setDescricao("Teste descricao");
        $banner->setCategoria($categoria);
        $banner->setAbrirEm("Abrir");
        $banner->setTemLink(1);
        $banner->setUrl("URLLInk");
        $banner->setSites($sites);
        $banner->setOrdem(5);
        $banner->setImagem($imagem);
        $banner->setUrlCompleta("www.teste.com.br");
        $banner->setFuncionalidadeMenu(new \Entity\FuncionalidadeMenu());
        $banner->setIdEntidade(1);
        
        $array = array(
            'id' => 1,
            'dataInicial' => new \DateTime("now"),
            'dataCadastro' => new \DateTime("now"),
            'dataFinal' => new \DateTime("now"),
            'publicado' => 1,
            'descricao' => "Teste descricao",
            'categoria' => $categoria,
            'abrirEm' => "Abrir",
            'temLink' => 1,
            'url' => "URLLInk",
            'sites' => $sites,
            'ordem' => 5,
            'imagem' => $imagem,
            'nome' => "Teste",
            'urlCompleta' => "www.teste.com.br",
            'funcionalidadeMenu' => new \Entity\FuncionalidadeMenu(),
            'idEntidade' => 1
        );



        $this->assertEquals($array, $banner->toArray());
    }

}
