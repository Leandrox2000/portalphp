<?php

namespace Entity;

/**
 * Description of BackgroundHomeTest
 */
class BackgroundHomeTest extends \PHPUnit_Framework_TestCase  
{

    public function test_metodo_get_Label()
    {
        $entity = new BackgroundHome();
        $entity->setNome('teste');

        $this->assertEquals('teste', $entity->getLabel());
        
    }

    public function test_metodo_toArray()
    {
        $entity = new BackgroundHome();
        $entity->setId(1);
        $entity->setDataCadastro(new \DateTime('now'));
        $entity->setDataFinal(new \DateTime('now'));
        $entity->setDataInicial(new \DateTime('now'));
        $entity->setImagem(new Imagem());
        $entity->setNome('teste');
        $entity->setPublicado(1);
                
        $toArray = $entity->toArray();
        $array = array(
            'id' => 1,
            'dataCadastro' => new \DateTime('now'),
            'dataFinal' => new \DateTime('now'),
            'dataInicial' => new \DateTime('now'),
            'imagem' => new Imagem(),
            'nome' => 'teste',
            'publicado' => 1,
        );

        $this->assertEquals($array, $toArray);
    }

}
