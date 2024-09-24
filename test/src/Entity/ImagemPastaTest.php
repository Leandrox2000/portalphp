<?php

namespace Entity;

/**
 * Description of ImagemPastaTest
 *
 * @author Jointi
 */
class ImagemPastaTest extends \PHPUnit_Framework_TestCase
{

    public function test_metodo_get_label()
    {
        $entity = new ImagemPasta();
        $entity->setNome('teste');
        $this->assertEquals('teste', $entity->getLabel());
    }

    public function test_metodo_toArray()
    {
        $entity = new ImagemPasta();
        $entity->setNome('teste');
        $entity->setCaminho('caminho');
        $entity->setCategoria(new \Entity\ImagemCategoria());
    
        $arr = array(
            'id' => null,
            'nome' => 'teste',
            'caminho' => 'caminho',
            'categoria' => new \Entity\ImagemCategoria()
        );

        $toArray = $entity->toArray();

        $this->assertEquals($toArray, $arr);
    }

}
