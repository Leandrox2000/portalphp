<?php

namespace Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Funcionalidade
 *
 * @ORM\Table(name="tb_funcionalidade", uniqueConstraints={@ORM\UniqueConstraint(name="uk_funcionalidade_nome", columns={"no_nome"})})
 * @ORM\Entity
 */
class Funcionalidade extends AbstractEntity implements EntityInterface
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id_funcionalidade", type="bigint", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\SequenceGenerator(sequenceName="tb_funcionalidade_id_funcionalidade_seq", allocationSize=1, initialValue=10)
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="no_nome", type="string", length=100, nullable=false)
     */
    private $nome;
    
    /**
     * @var Collection
     * 
     * @ORM\OneToMany(targetEntity="FuncionalidadeSite", mappedBy="funcionalidade")
     */
    private $funcionalidadesSite;

    
    public function __construct() 
    {
        $this->setFuncionalidadesSite(new ArrayCollection());
    }
    
    /**
     * 
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
     * @param integer $id
     */
    public function setId($id)
    {
        $this->id = $id;
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
     * @return string
     */
    public function getLabel()
    {
        return $this->getNome();
    }
    
    function getFuncionalidadesSite() {
        return $this->funcionalidadesSite;
    }

    function setFuncionalidadesSite(Collection $funcionalidadesSite) {
        $this->funcionalidadesSite = $funcionalidadesSite;
    }

    
    public function toArray()
    {
        return array(
            "id" => $this->getId(),
            "nome" => $this->getNome()
        );
    }

}
