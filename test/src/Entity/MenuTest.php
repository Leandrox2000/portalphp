<?php

namespace Entity;

use Entity\FuncionalidadeMenu;
use Entity\Menu;

/**
 * Description of MenuTest
 *
 * @author Eduardo
 */
class MenuTest extends \PHPUnit_Framework_TestCase
{

    public function test_metodo_get_label()
    {
        //Declara a ata
        $menu = new Menu();
        $menu->setTitulo('teste');

        $this->assertEquals('teste', $menu->getLabel());
    }

    public function test_metodo_to_array()
    {

        $funcionalidadeMenu = new FuncionalidadeMenu();
        $vinculoPai = new Menu();

        $menu = new Menu();
        $menu->setDataCadastro(new \DateTime('now'));
        $menu->setDataInicial(new \DateTime('now'));
        $menu->setDataFinal(new \DateTime('now'));
        $menu->setIdEntidade(1);
        $menu->setTipoMenu("tipo");
        $menu->setTitulo('titulo');
        $menu->setAbrirEm("Teste");
        $menu->setUrlExterna('www.google.com.br');
        $menu->setPublicado(0);
        $menu->setFuncionalidadeMenu(new FuncionalidadeMenu());
        $menu->setVinculoPai(new Menu());
        $menu->setOrdem('ordem');
        $menu->setSite(new \Entity\Site());

        $array = array(
            'id' => null,
            'dataCadastro' => new \DateTime('now'),
            'dataFinal' => new \DateTime('now'),
            'dataInicial' => new \DateTime('now'),
            'idEntidade' => 1,
            'abrirEm' => "Teste",
            'publicado' => 0,
            'tipoMenu' => 'tipo',
            'titulo' => 'titulo',
            'urlExterna' => 'www.google.com.br',
            'site' => new \Entity\Site(),
            'ordem' => 'ordem',
            'funcionalidadeMenu' => $funcionalidadeMenu->toArray(),
            'vinculoPai' => $vinculoPai->toArray()
        );

        $this->assertEquals($array, $menu->toArray());
    }

}
