<?php

namespace Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * EmailBoletim
 *
 * @ORM\Table(name="tb_email_boletim", uniqueConstraints={@ORM\UniqueConstraint(name="UK_email_boletim_id_email_boletim", columns={"id_email_boletim"})})
 * @ORM\Entity(repositoryClass="Entity\Repository\EmailBoletimRepository")
 */

class EmailBoletim extends AbstractEntity implements EntityInterface
{

    /**
     * @var integer
     *
     * @ORM\Column(name="id_email_boletim", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

  /**
     * @var \DateTime
     *
     * @ORM\Column(name="dt_cadastro", type="datetime", nullable=false)
     */
    private $dataCadastro;
    
    /**
     * @var string
     *
     * @ORM\Column(name="no_completo", type="string", length=150, nullable=false)
     */
    private $nome;

    /**
     * @var string
     *
     * @ORM\Column(name="ds_email", type="string", length=100, nullable=false)
     */
    private $email;
    
    public function __construct()
    {
        $this->setDataCadastro(new \DateTime('now'));
    }
    
    /**
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * 
     * @return string
     */
    public function getNome()
    {
        return $this->nome;
    }

    /**
     * 
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }
    
    /**
     * 
     * @param string $nome
     */
    public function setNome($nome)
    {
        $this->nome = $nome;
    }
    
    /**
     * 
     * @param string $email
     */
    public function setEmail($email)
    {
        $this->email = $email;
    }

    /**
     * 
     * @return \DateTime
     */
    public function getDataCadastro()
    {
        return $this->dataCadastro;
    }

    /**
     * 
     * @param \DateTime $dataCadastro
     */
    public function setDataCadastro(\DateTime $dataCadastro)
    {
        $this->dataCadastro = $dataCadastro;
    }

    /**
     * Similiar à um __toString
     * Retorna o campo que for mais importante do objeto 
     *  
     * @return type
     */
    public function getLabel()
    {
        return $this->email;
    }
    

    /**
     * Converte  o Objeto para Array
     * 
     * @return array
     */
    public function toArray()
    {
        return array(
            'id' => $this->getId(),
            'dataCadastro' => $this->getDataCadastro(),
            'nome' => $this->getNome(),
            'email' => $this->getEmail()
        );
    }

}
