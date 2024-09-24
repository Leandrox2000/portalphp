<?php
namespace Entity;


/**
 * Description of AgendaTest
 *
 * @author Eduardo
 */
class AgendaTest extends \PHPUnit_Framework_TestCase
{

    public function test_metodo_get_label()
    {
        //Declara a agenda
        $agenda = new Agenda();
        $agenda->setTitulo('teste');

        $this->assertEquals('teste', $agenda->getLabel());
    }

    public function test_metodo_to_array()
    {
        
        $agenda = new Agenda();
        $agenda->setDataCadastro(new \DateTime('now'));
        $agenda->setDataInicial(new \DateTime('now'));
        $agenda->setDataFinal(new \DateTime('now'));
        $agenda->setTitulo('titulo de teste');
        $agenda->setIngresso('ingresso de teste');
        $agenda->setLocal('local de teste');
        $agenda->setUf('RS');
        $agenda->setCep('91120-422');
        $agenda->setEndereco('rua de teste');
        $agenda->setCidade('poa');
        $agenda->setBairro('bairro de teste');
        $agenda->setNumero('1');
        $agenda->setComplemento('apto. 100');
        $agenda->setTelefone('33445566');
        $agenda->setCelular('99998888');
        $agenda->setEmail('teste@teste.com.br');
        $agenda->setSite('www.teste.com.br');
        $agenda->setDescricao('descricao de teste');
        $agenda->setPropriedadeSede(1);
        $agenda->setPublicado(1);
        $agenda->setSites(new \Doctrine\Common\Collections\ArrayCollection());
        $agenda->setPeriodoInicial(new \DateTime('now'));
        $agenda->setPeriodoFinal(new \DateTime('now'));
        
      
        $array = array(
            "id" => null,
            "titulo" => 'titulo de teste',
            "descricao" => "descricao de teste",
            "periodoInicial" => new \DateTime('now'),
            "periodoFinal" => new \DateTime('now'),
            "ingresso" => 'ingresso de teste',
            "local" => 'local de teste',
            "uf" => 'RS',
            "cep" => '91120-422',
            "endereco" => 'rua de teste',
            "cidade" => 'poa',
            "bairro" => 'bairro de teste',
            "numero" => '1',
            "complemento" => 'apto. 100',
            "telefone" => '33445566',
            "celular" => '99998888',
            "email" => 'teste@teste.com.br',
            "site" => 'www.teste.com.br',
            "dataInicial" => new \DateTime('now'),
            "dataFinal" => new \DateTime('now'),
            "dataCadastro" => new \DateTime('now'),
            "publicado" => 1,
            "propriedadeSede" => 1,
            "sites" => new \Doctrine\Common\Collections\ArrayCollection()
        );
        
        $this->assertEquals($array, $agenda->toArray());
    }

}
