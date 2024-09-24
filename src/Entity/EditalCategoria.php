<?php

namespace Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * EditalCategoria
 *
 * @ORM\Table(name="tb_edital_categoria", uniqueConstraints={@ORM\UniqueConstraint(name="uk_edital_categoria_no_categoria", columns={"no_categoria"}), @ORM\UniqueConstraint(name="uk_edital_categoria_id_edital_categoria", columns={"id_edital_categoria"})})
 * @ORM\Entity(repositoryClass="Entity\Repository\EditalCategoriaRepository")
 */
class EditalCategoria extends AbstractEntity implements EntityInterface
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id_edital_categoria", type="bigint", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\SequenceGenerator(sequenceName="tb_edital_categoria_id_edital_categoria_seq", allocationSize=1, initialValue=1)
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="no_categoria", type="string", length=50, nullable=false)
     */
    private $nome;

    /**
     * @var \Doctrine\Common\Collections\Collection
     * 
     * @ORM\OneToMany(targetEntity="Edital", mappedBy="categoria", fetch="EXTRA_LAZY")
     */
    private $editais;

    
    /**
     * Constructor
     * 
     */
    public function __construct()
    {
        $this->setEditais(new \Doctrine\Common\Collections\ArrayCollection());
    }

    /**
     * 
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }
    
    /**
     * 
     * @return String
     */
    public function getNome()
    {
        return $this->nome;
    }

    /**
     * 
     * @param String $nome
     */
    public function setNome($nome)
    {
        $this->nome = $nome;
    }

    /**
     * 
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getEditais()
    {
        return $this->editais;
    }

    /**
     * 
     * @param \Doctrine\Common\Collections\Collection $editais
     */
    public function setEditais(\Doctrine\Common\Collections\Collection $editais)
    {
        $this->editais = $editais;
    }
        
    /**
     * 
     * @return String
     */
    public function getLabel()
    {
        return $this->getNome();
    }
    
    /**
     * 
     * @return array
     */
    public function toArray()
    {
        return array(
            'id' => $this->getId(),
            'nome' => $this->getNome(),
            'editais' => $this->getEditais()
        );
    }

}
