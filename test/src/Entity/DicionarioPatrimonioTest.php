<?php

namespace Entity;

/**
 * Description of DicionarioPatrimonioTest
 *
 * @author Luciano
 */
class DicionarioPatrimonioTest extends BaseTest
{

    public function test_metodo_get_Label()
    {
        $entity = new DicionarioPatrimonioCultural();
        $entity->setTitulo('teste');
        
        $this->assertEquals('teste', $entity->getLabel());
    }

    public function test_metodo_toArray()
    {
        $entity = new DicionarioPatrimonioCultural();
        $entity->setId(1);
        $entity->setCategoria(new CategoriaDicionario());
        $entity->setColaborador('colaborador');
        $entity->setDataCadastro(new \DateTime());
        $entity->setDataInicial(new \DateTime());
        $entity->setDataFinal(new \DateTime());
        $entity->setDescricao('descricao');
        $entity->setFichaTecnica('ficha');
        $entity->setFuncao('funcao');
        $entity->setLink('link');
        $entity->setPublicado(1);
        $entity->setTitulo('titulo');
        $entity->setVerbete('verbete');

        $arr = array(
            'id' => 1,
            'categoria' => new CategoriaDicionario(),
            'colaborador' => 'colaborador',
            'dataCadastro' => new \DateTime(),
            'dataInicial' => new \DateTime(),
            'dataFinal' => new \DateTime(),
            'descricao' => 'descricao',
            'fichaTecnica' => 'ficha',
            'funcao' => 'funcao',
            'link' => 'link',
            'publicado' => 1,
            'titulo' => 'titulo',
            'verbete' => 'verbete',
        );

        $this->assertEquals($arr, $entity->toArray());
    }

}
