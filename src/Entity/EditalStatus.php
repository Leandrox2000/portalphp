<?php

namespace Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * EditalStatus
 *
 * @ORM\Table(name="tb_edital_status", uniqueConstraints={@ORM\UniqueConstraint(name="uk_edital_status_no_status", columns={"no_status"})})
 * @ORM\Entity(repositoryClass="Entity\Repository\EditalStatusRepository")
 */
class EditalStatus extends AbstractEntity implements EntityInterface
{

    /**
     * @var integer
     *
     * @ORM\Column(name="id_status_categoria", type="bigint", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\SequenceGenerator(sequenceName="tb_edital_status_id_status_categoria_seq", allocationSize=1, initialValue=1)
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="no_status", type="string", length=50, nullable=false)
     */
    private $nome;

    /**
     * @var \Doctrine\Common\Collections\Collection
     * 
     * @ORM\OneToMany(targetEntity="Edital", mappedBy="status", fetch="EXTRA_LAZY")
     */
    private $editais;

    /**
     * @var integer
     *
     * @ORM\Column(name="nu_ordem", type="bigint", nullable=false)
     */
    private $ordem;
    
    /**
     * @var integer
     *
     * @ORM\Column(name="nu_ordem_column", type="bigint", nullable=false)
     */
    private $column;

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
     * @return string
     */
    public function getNome()
    {
        return $this->nome;
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
     * @return string
     */
    public function getLabel()
    {
        return $this->getNome();
    }

    /**
     * 
     * @return integer
     */
    public function getOrdem()
    {
        return $this->ordem;
    }
    

    /**
     * 
     * @return integer
     */
    public function getColumn(){
        
        return $this->column;
    }
    /**
     * 
     * @param String $ordem
     */
    public function setOrdem($ordem)
    {
        $this->ordem = $ordem;
    }
    
    /**
     * 
     * @param String $ordem
     */
    public function setColumn($column) {
        
        $this->column = $column;
        return $this;
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
