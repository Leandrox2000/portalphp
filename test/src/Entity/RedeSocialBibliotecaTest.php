<?php

namespace Entity;

/**
 * Description of BibliografiaTest
 *
 * @author Eduardo
 */
class RedeSocialBibliotecaTest extends \PHPUnit_Framework_TestCase
{

    public function test_metodo_get_label()
    {
        $rsb = new RedeSocialBiblioteca();
        $rsb->setRedeSocial('aaa');
        $this->assertEquals('aaa', $rsb->getLabel());
    }

    public function test_metodo_to_array()
    {
        $rsb = new RedeSocialBiblioteca();
        $rsb->setId(1);
        $rsb->setRedeSocial('facebook');
        $rsb->setUrl('http://www.facebook.com.br/teste');
        $rsb->setBiblioteca(new \Entity\Biblioteca());

        $arr = array(
            'id' => 1,
            'redeSocial' => 'facebook',
            'url' => 'http://www.facebook.com.br/teste',
            'biblioteca' => new \Entity\Biblioteca(),
        );

        $this->assertEquals($arr, $rsb->toArray());
    }

}
