<?php

namespace Entity;

/**
 * Description of SiteTest
 *
 * @author Eduardo
 */
class SiteTest extends \PHPUnit_Framework_TestCase
{

    public function test_get_label()
    {
        $site = new Site();
        $site->setNome("teste");
        $this->assertEquals("teste", $site->getLabel());
    }

    public function test_metodo_toArray()
    {

        //Instancia da classe, setando todos os parâmetros
        $site = new Site();
        $site->setId(1);
        $site->setDataCadastro(new \DateTime('now'));
        $site->setDataInicial(new \DateTime('now'));
        $site->setDataFinal(new \DateTime('now'));
        $site->setNome('teste');
        $site->setSigla('TT');
        $site->setSede(0);
        $site->setAgendas(new \Doctrine\Common\Collections\ArrayCollection());
        $site->setVideos(new \Doctrine\Common\Collections\ArrayCollection());
        $site->setGalerias(new \Doctrine\Common\Collections\ArrayCollection());
        $site->setNoticias(new \Doctrine\Common\Collections\ArrayCollection());
        $site->setEditais(new \Doctrine\Common\Collections\ArrayCollection());
        $site->setPaginasEstaticas(new \Doctrine\Common\Collections\ArrayCollection());
        $site->setLegislacoes(new \Doctrine\Common\Collections\ArrayCollection());
        $site->setSlidersHome(new \Doctrine\Common\Collections\ArrayCollection());
        $site->setPublicado(1);
        $site->setDescricao('teste');
        $site->setTitulo('teste');
        $site->setFuncionalidadesSite(new \Doctrine\Common\Collections\ArrayCollection());

        //Array que deve retornar quando eu acessar o método toArray() da entidade
        $array = array(
            "titulo" => 'teste',
            "id" => 1,
            "dataCadastro" => new \DateTime('now'),
            "dataInicial" => new \DateTime('now'),
            "dataFinal" => new \DateTime('now'),
            "nome" => 'teste',
            "sigla" => 'TT',
            "sede" => 0,
            "paginaEstatica" => new \Doctrine\Common\Collections\ArrayCollection(),
            "videos" => new \Doctrine\Common\Collections\ArrayCollection(),
            "galerias" => new \Doctrine\Common\Collections\ArrayCollection(),
            "noticias" => new \Doctrine\Common\Collections\ArrayCollection(),
            "agendas" => new \Doctrine\Common\Collections\ArrayCollection(),
            "editais" => new \Doctrine\Common\Collections\ArrayCollection(),
            "legislacoes" => new \Doctrine\Common\Collections\ArrayCollection(),
            "slidersHome" => new \Doctrine\Common\Collections\ArrayCollection(),
            "publicado" => 1,
            "descricao" => 'teste',
            "funcionalidadesSite" => new \Doctrine\Common\Collections\ArrayCollection(),
        );

        //Faz a verificação do array que ele deveria retornar com o array que de fato está retornando
        $this->assertEquals($array, $site->toArray());
    }

}
