<?php

namespace Entity;

/**
 * Description of EmailBoletimTest
 *
 * @author Eduardo
 */
class EmailBoletimTest extends \PHPUnit_Framework_TestCase
{

    public function test_metodo_set_nome()
    {
        $email_boletim = new EmailBoletim();
        $email_boletim->setNome("Nome de teste");
        $this->assertEquals("Nome de teste", $email_boletim->getNome());
    }
    
    public function test_metodo_set_email()
    {
        $email_boletim = new EmailBoletim();
        $email_boletim->setEmail("Email de teste");
        $this->assertEquals("Email de teste", $email_boletim->getEmail());
    }
        
    /**
     * 
     * @dataProvider data_provider_attributes
     */
    public function test_atributos_existem($attr)
    {
        $this->assertClassHasAttribute($attr, "Entity\\EmailBoletim");
    }

  
    public function data_provider_attributes()
    {
        return array(
            array("id"),
            array("nome"),
            array("email")
        );
    }

    public function test_metodo_get_label()
    {
        $email_boletim = new EmailBoletim();
        $email_boletim->setNome("Nome de teste 1");
        $email_boletim->setEmail("teste@teste.com");
        $email_boletim->setEmail("teste_2@teste.com");
        $this->assertNotEmpty($email_boletim->getLabel());        
    }
   
    public function test_toArray()
    {
        $email_boletim = new EmailBoletim();
        $email_boletim->setNome("Nome de teste");
        $email_boletim->setEmail("Email de teste");
       
        $emailArray = array(
            'id' => null,
            'dataCadastro' => new \DateTime('now'),
            'nome' => "Nome de teste",
            'email' => "Email de teste",
        );

        $this->assertEquals($emailArray, $email_boletim->toArray());
    }

}
