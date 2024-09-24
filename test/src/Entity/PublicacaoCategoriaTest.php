<?php
namespace Entity;

use Entity\Publicacao;

/**
 * Description of PublicacaoCategoriaTest
 *
 * @author Luciano
 */
class PublicacaoCategoriaTest extends BaseTest
{

    public function test_metodo_set_nome()
    {
        $categoria = new PublicacaoCategoria();

        $categoria->setNome("Categoria");

        $this->assertEquals("Categoria", $categoria->getNome());
    }

    public function test_metodo_get_publicacoes()
    {
         $publicacaoCategoria = new PublicacaoCategoria();
         
         $publicacoes = new \Doctrine\Common\Collections\ArrayCollection();
         $publicacoes->add(new Publicacao());
         
         $publicacaoCategoria->setPublicacoes($publicacoes);
         
         $this->assertEquals($publicacoes, $publicacaoCategoria->getPublicacoes());
         
    }

    /**
     * 
     * @dataProvider data_provider_attributes
     */
    public function test_atributos_existem($attr)
    {
        $this->assertClassHasAttribute($attr, "Entity\\PublicacaoCategoria");
    }

    public function data_provider_attributes()
    {
        return array(
            array("id"),
            array("nome"),
        );
    }

    public function test_metodo_get_label()
    {
        $categoria = new PublicacaoCategoria();

        $categoria->setNome("Categoria");


        $this->assertNotEmpty($categoria->getLabel());
    }

    public function test_metodo_to_string()
    {
        $categoria = new PublicacaoCategoria();

        $categoria->setNome("Categoria");

        $this->assertEquals("Categoria", $categoria);
    }

    public function test_toArray()
    {
        $categoria = new PublicacaoCategoria();

        $categoria->setNome("Nova Categoria");

        $array = array(
            'id' => null,
            'nome' => "Nova Categoria",
        );

        $this->assertEquals($array, $categoria->toArray());
    }

}
