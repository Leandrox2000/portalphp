<?php

namespace Entity;

/**
 * Description of PerguntaTest
 *
 * @author Luciano
 */
class PerguntaTest extends BaseTest
{

    public function test_inicializacao_do_construtor()
    {
        $pergunta = new Pergunta();

        $this->assertEquals(new \DateTime("now"), $pergunta->getDataCadastro());
    }

    public function test_metodo_set_pergunta()
    {
        $pergunta = new Pergunta();

        $pergunta->setPergunta("Pergunta");

        $this->assertEquals("Pergunta", $pergunta->getPergunta());
    }

    public function test_metodo_set_resposta()
    {
        $pergunta = new Pergunta();

        $pergunta->setResposta("Resposta");

        $this->assertEquals("Resposta", $pergunta->getResposta());
    }

    /**
     * 
     * @dataProvider data_provider_attributes
     */
    public function test_atributos_existem($attr)
    {
        $this->assertClassHasAttribute($attr, "Entity\\Pergunta");
    }

    /**
     * 
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage Data Cadastro só aceita dados do Tipo DateTime
     */
    public function test_se_a_data__cadastro_aceita_diferente_de_datetime()
    {
        $pergunta = new Pergunta();

        $pergunta->setDataCadastro("TEste");
    }

    public function data_provider_attributes()
    {
        return array(
            array("id"),
            array("pergunta"),
            array("resposta"),
            array("dataCadastro"),
            array("publicado"),
            array("categoria"),
        );
    }

    public function test_metodo_get_label()
    {
        $pergunta = new Pergunta();
        $pergunta->setPergunta("Pergunta?");
        $pergunta->setResposta("Resposta.");
        $pergunta->setPublicado(1);
        $pergunta->setCategoria(new PerguntaCategoria());


        $this->assertNotEmpty($pergunta->getLabel());
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Categoria deve ser do Tipo Objeto PerguntaCategoria
     */
    public function test_se_categoria_recebe_algo_diferente_to_tipo_PerguntaCategoria()
    {
        $pergunta = new Pergunta();
        $pergunta->setCategoria("NovaCategoria");
    }

    public function test_retorno_metodo_getCategoria()
    {
        $pergunta = new Pergunta();
        $pergunta->setCategoria(new PerguntaCategoria());

        $this->assertInstanceOf("Entity\\PerguntaCategoria", $pergunta->getCategoria());
    }

    public function test_toArray()
    {
        $pergunta = new Pergunta();
        $categoria = new PerguntaCategoria();

        $pergunta->setCategoria($categoria);
        $pergunta->setPergunta("Pergunta?");
        $pergunta->setPublicado(1);
        $pergunta->setResposta("Resposta");
        $pergunta->setDataInicial(new \DateTime("2014-01-01"));
        $pergunta->setDataFinal(new \DateTime("2014-01-31"));
        $pergunta->setOrdem(1);
        
        $perguntaArray = array(
            'id' => null,
            'pergunta' => "Pergunta?",
            'resposta' => "Resposta",
            'categoria' => $categoria,
            'publicado' => 1,
            'ordem' => 1,
            'dataCadastro' => new \DateTime("now"),
            'dataInicial' => new \DateTime("2014-01-01"),
            'dataFinal' => new \DateTime("2014-01-31")
        ); 

        $this->assertEquals($perguntaArray, $pergunta->toArray());
    }

}
