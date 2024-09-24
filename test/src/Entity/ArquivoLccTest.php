<?php

namespace Entity;

/**
 * Description of ArquivoLccTest
 *
 * @author Jointi
 */
class ArquivoLccTest extends \PHPUnit_Framework_TestCase
{

    public function test_metodo_get_label()
    {
        $entity = new ArquivoLcc();
        $entity->setNome('teste');
        $this->assertEquals('teste', $entity->getLabel());
    }

    public function test_metodo_toArray()
    {
        $entity = new ArquivoLcc();
        $entity->setId(1);
        $entity->setNome('nome');
        $entity->setNomeOriginal('nome original');
        $entity->setLicitacaoConvenioContrato(new \Entity\LicitacaoConvenioContrato());
        

        $arr = array(
            'id' => 1,
            'nome' => 'nome',
            'nomeOriginal' => 'nome original',
            'licitacaoConvenioContrato' => new \Entity\LicitacaoConvenioContrato()
        );

        $toArray = $entity->toArray();

        $this->assertEquals($toArray, $arr);
    }

}
