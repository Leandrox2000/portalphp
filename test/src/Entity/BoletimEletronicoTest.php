<?php

namespace Entity;

/**
 * Description of BoletimEletronicoTest
 *
 * @author Eduardo
 */
class BoletimEletronicoTest extends \PHPUnit_Framework_TestCase
{

    public function test_metodo_construct()
    {
        $boletim_eletronico = new BoletimEletronico();
        $this->assertEquals((new \DateTime("now")), $boletim_eletronico->getDataCadastro());
    }

    public function test_metodo_get_label_numero_menor_que_dez()
    {
        $boletim_eletronico = new BoletimEletronico();
        $boletim_eletronico->setNumero(1);
        $boletim_eletronico->setAno(2014);

        $this->assertEquals("01/2014", $boletim_eletronico->getLabel());
    }

    public function test_metodo_get_label_numero_maior_que_dez()
    {
        $boletim_eletronico = new BoletimEletronico();
        $boletim_eletronico->setNumero(98);
        $boletim_eletronico->setAno(2014);

        $this->assertEquals("98/2014", $boletim_eletronico->getLabel());
    }

    public function test_metodo_set_id()
    {
        $boletim_eletronico = new BoletimEletronico();
        $boletim_eletronico->setId(1);
        $this->assertEquals(1, $boletim_eletronico->getId());
    }

    public function test_metodo_set_data_cadastro()
    {
        $boletim_eletronico = new BoletimEletronico();
        $boletim_eletronico->setDataCadastro((new \DateTime("now")));
        $this->assertEquals((new \DateTime("now")), $boletim_eletronico->getDataCadastro());
    }

    public function test_metodo_set_periodo_inicial()
    {
        $boletim_eletronico = new BoletimEletronico();
        $boletim_eletronico->setPeriodoInicial(new \DateTime('2014-01-25'));
        $this->assertEquals(new \DateTime('2014-01-25'), $boletim_eletronico->getPeriodoInicial());
    }

    public function test_metodo_set_periodo_final()
    {
        $boletim_eletronico = new BoletimEletronico();
        $boletim_eletronico->setPeriodoFinal(new \DateTime('2014-03-25'));
        $this->assertEquals(new \DateTime('2014-03-25'), $boletim_eletronico->getPeriodoFinal());
    }

    public function test_metodo_set_data_inicial()
    {
        $boletim_eletronico = new BoletimEletronico();
        $boletim_eletronico->setDataInicial(new \DateTime('2014-03-25'));
        $this->assertEquals(new \DateTime('2014-03-25'), $boletim_eletronico->getDataInicial());
    }

    public function test_metodo_set_data_final()
    {
        $boletim_eletronico = new BoletimEletronico();
        $boletim_eletronico->setDataFinal(new \DateTime('2014-03-25'));
        $this->assertEquals(new \DateTime('2014-03-25'), $boletim_eletronico->getDataFinal());
    }

    public function test_metodo_set_numero()
    {
        $boletim_eletronico = new BoletimEletronico();
        $boletim_eletronico->setNumero(12);
        $this->assertEquals(12, $boletim_eletronico->getNumero());
    }

    public function test_metodo_set_ano()
    {
        $boletim_eletronico = new BoletimEletronico();
        $boletim_eletronico->setAno(2014);
        $this->assertEquals(2014, $boletim_eletronico->getAno());
    }

    public function test_metodo_set_arquivo()
    {
        $boletim_eletronico = new BoletimEletronico();
        $boletim_eletronico->setArquivo("arquivo.docx");
        $this->assertEquals("arquivo.docx", $boletim_eletronico->getArquivo());
    }

    public function test_metodo_set_publicado()
    {
        $boletim_eletronico = new BoletimEletronico();
        $boletim_eletronico->setPublicado(1);
        $this->assertEquals(1, $boletim_eletronico->getPublicado());
    }

    /**
     * 
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage O período final deve ser 0 ou 1
     */
    public function test_publicado_aceita_diferente_zero_ou_um()
    {
        $boletim_eletronico = new BoletimEletronico();
        $boletim_eletronico->setPublicado(2);
    }

    public function test_metodo_get_label()
    {
        $boletim_eletronico = new BoletimEletronico();
        $boletim_eletronico->setNumero(12);
        $boletim_eletronico->setAno(2014);
        $this->assertEquals("12/2014", $boletim_eletronico->getLabel());
    }

    public function test_metodo_to_array()
    {
        $boletim_eletronico = new BoletimEletronico();
        $boletim_eletronico->setAno(2014);
        $boletim_eletronico->setNumero(12);
        $boletim_eletronico->setArquivo("arquivo.txt");
        $boletim_eletronico->setPeriodoInicial(new \DateTime("2014-01-23"));
        $boletim_eletronico->setPeriodoFinal(new \DateTime("2014-02-23"));
        $boletim_eletronico->setPublicado(0);

        $array = array(
            'id' => '',
            'data_cadastro' => new \DateTime("now"),
            'periodo_inicial' => new \DateTime("2014-01-23"),
            'periodo_final' => new \DateTime("2014-02-23"),
            'ano' => 2014,
            'numero' => 12,
            'arquivo' => "arquivo.txt"
        );

        $this->assertEquals($array, $boletim_eletronico->toArray());
    }

}
