<?php
namespace Entity;


/**
 * Description of FuncionarioTest
 *
 * @author Eduardo
 */
class FuncionarioTest extends \PHPUnit_Framework_TestCase
{

    public function test_metodo_get_label()
    {
        //Declara a fototeca
        $funcionario = new Funcionario();
        $funcionario->setNome('teste');

        $this->assertEquals('teste', $funcionario->getLabel());
    }

    public function test_metodo_to_array()
    {

        $funcionario = new Funcionario();
        $funcionario->setId(1);
        $funcionario->setDataCadastro(new \DateTime('now'));
        $funcionario->setDataInicial(new \DateTime('now'));
        $funcionario->setDataFinal(new \DateTime('now'));
        $funcionario->setNome('teste');
        $funcionario->setImagem('teste.jpg');
        $funcionario->setCurriculo('teste');
        $funcionario->setVinculo(new \Entity\Vinculo());
        $funcionario->setUnidade(new \Entity\Unidade());
        $funcionario->setCargo(new \Entity\Cargo());
        $funcionario->setPublicado(1);
        $funcionario->setEmail('teste@teste.com');
        $funcionario->setExibirIntranet(1);
        $funcionario->setDiretoria(1);
        $funcionario->setExibirPortal(1);
        
        $array = array(
            "id" => 1,
            "dataCadastro" => new \DateTime('now'),
            "dataInicial" => new \DateTime('now'),
            "dataFinal" => new \DateTime('now'),
            "nome" => 'teste',
            "imagem" => 'teste.jpg',
            "curriculo" => 'teste',
            "vinculo" => new \Entity\Vinculo(),
            "unidade" => new \Entity\Unidade(),
            "cargo" => new \Entity\Cargo(),
            "publicado" => 1,
            "email" => 'teste@teste.com',
            'exibirPortal' => 1,
            'exibirIntranet' => 1,
            'diretoria' => 1,
        );

        $this->assertEquals($array, $funcionario->toArray());
    }

}
