<?php

namespace Entity;

/**
 * Description of BibliografiaTest
 *
 * @author Eduardo
 */
class BibliotecaTest extends \PHPUnit_Framework_TestCase
{

    public function test_metodo_get_label()
    {
        $biblioteca = new Biblioteca();
        $biblioteca->setNome('aaa');

        $this->assertEquals('aaa', $biblioteca->getLabel());
    }

    public function test_metodo_to_array()
    {
        $biblioteca = new Biblioteca();
        $biblioteca->setId(1);
        $biblioteca->setBairro('teste');
        $biblioteca->setCelular('0000.0000');
        $biblioteca->setCep('00000-000');
        $biblioteca->setCidade('teste');
        $biblioteca->setComplemento('teste');
        $biblioteca->setDataCadastro(new \DateTime('now'));
        $biblioteca->setDataInicial(new \DateTime('now'));
        $biblioteca->setDataFinal(new \DateTime('now'));
        $biblioteca->setDescricao('teste');
        $biblioteca->setEmail('teste@teste.com.br');
        $biblioteca->setEndereco('teste');
        $biblioteca->setHorarioFuncionamento('teste');
        $biblioteca->setImagem(new \Entity\Imagem());
        $biblioteca->setNome('teste');
        $biblioteca->setNumero('123');
        $biblioteca->setPublicado(1);
        $biblioteca->setResponsavel('teste');
        $biblioteca->setTelefone('0000.0000');
        $biblioteca->setUf('aa');
        $biblioteca->setRedesSociais(new \Doctrine\Common\Collections\ArrayCollection());
        $biblioteca->setOrdem(1);

        $arr = array(
            'id' => 1,
            'bairro' => 'teste',
            'celular' => '0000.0000',
            'cep' => '00000-000',
            'cidade' => 'teste',
            'complemento' => 'teste',
            'dataCadastro' => new \DateTime('now'),
            'dataFinal' => new \DateTime('now'),
            'dataInicial' => new \DateTime('now'),
            'descricao' => 'teste',
            'email' => 'teste@teste.com.br',
            'endereco' => 'teste',
            'horarioFuncionamento' => 'teste',
            'imagem' => new \Entity\Imagem(),
            'nome' => 'teste',
            'numero' => '123',
            'publicado' => 1,
            'ordem' => 1,
            'responsavel' => 'teste',
            'telefone' => '0000.0000',
            'uf' => 'aa',
            'redesSociais' => new \Doctrine\Common\Collections\ArrayCollection()
        );

        $this->assertEquals($arr, $biblioteca->toArray());
    }

}
