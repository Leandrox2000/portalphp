<?php

namespace Entity;

use Entity\Site;

/**
 * Description of UsuarioTest
 *
 * @author Eduardo
 */
class UsuarioTest extends \PHPUnit_Framework_TestCase
{

    public function test_get_label()
    {
        $usuario = new Usuario();
        $usuario->setNome('teste');

        $this->assertEquals('teste', $usuario->getLabel());
    }

    public function test_get_id()
    {
        $usuario = new Usuario();
        $this->assertEquals(0, $usuario->getId());
    }

    public function test_get_site()
    {
        $usuario = new Usuario();
        $site = new Site();

        $usuario->setSite($site);

        $this->assertEquals($site, $usuario->getSite());
    }

    public function test_get_nome()
    {
        $usuario = new Usuario();
        $usuario->setNome('teste');

        $this->assertEquals('teste', $usuario->getNome());
    }
    
    public function test_metodo_toArray()
    {
        $usuario = new Usuario();
        $usuario->setId(1);
        $usuario->setNome('teste');
        $usuario->setSite(new \Entity\Site());

        $arr = array(
            'id' => 1,
            'nome' => 'teste',
            'site' => new \Entity\Site(),
        );

        $this->assertEquals($arr, $usuario->toArray());
    }

}
