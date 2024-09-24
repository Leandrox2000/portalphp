<?php
namespace Entity;

use Entity\PaginaEstatica;
use Entity\Galeria ;

/**
 * Description of PaginaEstaticaGaleriaTest
 *
 * @author Eduardo
 */
class PaginaEstaticaGaleriaTest extends \PHPUnit_Framework_TestCase
{

    public function test_metodo_get_label()
    {
        //Declara a categoria
        $paginaEstaticaGaleria = new PaginaEstaticaGaleria();
        $paginaEstaticaGaleria->setPosicaoPagina(1);

        $this->assertEquals(1, $paginaEstaticaGaleria->getLabel());
    }

    public function test_metodo_to_array()
    {
        
        //Intancia a entidade
        $paginaEstaticaGaleria = new PaginaEstaticaGaleria();
        $paginaEstaticaGaleria->setId(1);
        $paginaEstaticaGaleria->setPaginaEstatica(new PaginaEstatica());
        $paginaEstaticaGaleria->setGaleria(new Galeria());
        $paginaEstaticaGaleria->setPosicaoPagina(1);

        //Monta o array com os dados
        $array = array(
            "id" => 1,
            "paginaEstatica" => new PaginaEstatica(),
            "galeria" => new Galeria(),
            "posicaoPagina" => 1
        );

        $this->assertEquals($array, $paginaEstaticaGaleria->toArray());
    }

}
