<?php

namespace Entity;

/**
 * Description of LicitacaoConvenioContratoTest
 *
 * @author Jointi
 */
class LicitacaoConvenioContratoTest extends \PHPUnit_Framework_TestCase
{

    public function test_metodo_get_label()
    {
        $entity = new LicitacaoConvenioContrato();
        $entity->setObjeto('teste');
        $this->assertEquals('teste', $entity->getLabel());
    }

    public function test_metodo_toArray()
    {
        $entity = new LicitacaoConvenioContrato();
        $entity->setId(1);
        $entity->setDataCadastro(new \DateTime('now'));
        $entity->setDataInicial(new \DateTime('now'));
        $entity->setDataFinal(new \DateTime('now'));
        $entity->setAmbito(new \Entity\AmbitoLcc());
        $entity->setCategoria(new \Entity\CategoriaLcc());
        $entity->setTipo(new \Entity\TipoLcc());
        $entity->setStatus(new \Entity\StatusLcc());
        $entity->setDataPublicacaoDou(new \DateTime('now'));
        $entity->setDataAberturaProposta(new \DateTime('now'));
        $entity->setValorEstimado('20.20');
        $entity->setUasg('uasg');
        $entity->setAno(2014);
        $entity->setProcesso('processo');
        $entity->setObjeto('objeto');
        $entity->setObservacoes('obs');
        $entity->setDataVigenciaFinal(new \DateTime('now'));
        $entity->setDataVigenciaInicial(new \DateTime('now'));
        $entity->setContratada('contratada');
        $entity->setPublicado(1);
        $entity->setArquivos(new \Doctrine\Common\Collections\ArrayCollection());
        $entity->setEdital('teste');
        
        $arr = array(
                    "id" => 1,
                    "dataCadastro" => new \DateTime('now'),
                    "dataInicial" => new \DateTime('now'),
                    "dataFinal" => new \DateTime('now'),
                    "ambito" => new \Entity\AmbitoLcc(),
                    "categoria" => new \Entity\CategoriaLcc(),
                    "tipo" => new \Entity\TipoLcc(),
                    "status" => new \Entity\StatusLcc(),
                    "dataPublicacaoDou" => new \DateTime('now'),
                    "dataAberturaProposta" => new \DateTime('now'),
                    "edital" => 'teste',
                    "valorEstimado" => '20.20',
                    "uasg" => 'uasg',
                    "ano" => 2014,
                    "processo" => 'processo',
                    "objeto" => 'objeto',
                    "observacoes" => 'obs',
                    "dataVigenciaInicial" => new \DateTime('now'),
                    "dataVigenciaFinal" =>  new \DateTime('now'),
                    "contratada" => 'contratada',
                    "publicado" => 1,
                    "arquivos" => new \Doctrine\Common\Collections\ArrayCollection()
        );

        $toArray = $entity->toArray();

        $this->assertEquals($toArray, $arr);
    }

}
