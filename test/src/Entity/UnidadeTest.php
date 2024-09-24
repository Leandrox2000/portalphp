<?php

namespace Entity;

/**
 * Description of UnidadeTest
 *
 * @author Eduardo
 */
class UnidadeTest extends \PHPUnit_Framework_TestCase
{

    public function test_metodo_get_label()
    {
        //Declara a noticia
        $unidade = new Unidade();
        $unidade->setNome('teste');

        $this->assertEquals('teste', $unidade->getLabel());
    }

    public function test_metodo_to_array()
    {
        $unidade = new Unidade();
        $unidade->setId(1);
        $unidade->setDataCadastro(new \DateTime('now'));
        $unidade->setNome('nome de teste');
        $unidade->setCidade('porto alegre');
        $unidade->setCep('91120-415');
        $unidade->setUf('RS');
        $unidade->setBairro('sarandi');
        $unidade->setNumero(12);
        $unidade->setTelefone('(51) 3322 5124');
        $unidade->setCelular('(51) 3366 5445');
        $unidade->setEmail('teste@teste.com');
        $unidade->setSite('www.teste.com.br');
        $unidade->setEndereco('rua de teste');
        $unidade->setComplemento('apto. 108');
        $unidade->setOrdem(1);
        
        $array = array(
            "id" => 1,
            "dataCadastro" => new \DateTime('now'),
            "nome" => 'nome de teste',
            "cidade" => 'porto alegre',
            "cep" => '91120-415',
            "ordem" => 1,
            "uf" => 'RS',
            "bairro" => 'sarandi',
            "complemento" => 'apto. 108',
            "numero" => 12,
            "telefone" => '(51) 3322 5124',
            "celular" => '(51) 3366 5445',
            "email" => 'teste@teste.com',
            "site" => 'www.teste.com.br',
            "endereco" => 'rua de teste'
        );

        $this->assertEquals($array, $unidade->toArray());
    }

}
