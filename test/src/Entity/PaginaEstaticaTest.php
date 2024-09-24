<?php
namespace Entity;

use Entity\PaginaEstaticaGaleria ;

/**
 * Description of PaginaEstaticaTest
 *
 * @author Eduardo
 */
class PaginaEstaticaTest extends \PHPUnit_Framework_TestCase
{

    public function test_metodo_get_label()
    {
        //Declara a categoria
        $paginaEstatica = new PaginaEstatica();
        $paginaEstatica->setTitulo('teste');

        $this->assertEquals('teste', $paginaEstatica->getLabel());
    }

    public function test_metodo_to_array()
    {
        //Declara os sites
        $sites = new \Doctrine\Common\Collections\ArrayCollection();
        $sites->add(new Site());

        //Declara relações entre páginas estáticas e galerias
        $peGalerias = new \Doctrine\Common\Collections\ArrayCollection();
        $peGalerias->add(new PaginaEstaticaGaleria());
        
        //Declara a categoria
        $paginaEstatica = new PaginaEstatica();
        $paginaEstatica->setId(1);
        $paginaEstatica->setTitulo('teste');
        $paginaEstatica->setPalavrasChave('lalala; lololo; lelele');
        $paginaEstatica->setDataCadastro(new \DateTime('now'));
        $paginaEstatica->setDataInicial(new \DateTime('now'));
        $paginaEstatica->setDataFinal(new \DateTime('now'));
        $paginaEstatica->setPropriedadeSede(1);
        $paginaEstatica->setPublicado(1);
        $paginaEstatica->setConteudo('teste');
        $paginaEstatica->setSites($sites);
        $paginaEstatica->setGalerias($peGalerias);

        //Monta o array com os dados
        $array = array(
            "id" => 1,
            "dataCadastro" => new \DateTime('now'),
            "dataInicial" => new \DateTime('now'),
            "dataFinal" => new \DateTime('now'),
            "titulo" => 'teste',
            'palavrasChave' => 'lalala; lololo; lelele',
            "conteudo" => 'teste',
            "publicado" => 1,
            "sites" => $sites,
            "propriedadeSede" => 1,
            "galerias" => $peGalerias
        );

        $this->assertEquals($array, $paginaEstatica->toArray());
    }

}
