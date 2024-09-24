<?php

namespace Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * StatusLcc
 *
 * @ORM\Table(name="tb_status_lcc", uniqueConstraints={@ORM\UniqueConstraint(name="uk_status_lcc_id_status_lcc", columns={"id_status_lcc"}), @ORM\UniqueConstraint(name="uk_status_lcc_no_status_lcc", columns={"no_status_lcc"})})
 * @ORM\Entity(repositoryClass="Entity\Repository\StatusLccRepository")
 */
class StatusLcc extends AbstractEntity implements EntityInterface
{

    /**
     * @var integer
     *
     * @ORM\Column(name="id_status_lcc", type="bigint", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\SequenceGenerator(sequenceName="tb_status_lcc_id_status_lcc_seq", allocationSize=1, initialValue=1)
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="no_status_lcc", type="string", length=100, nullable=false)
     */
    private $nome;

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
     * 
     * @return integer
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
     * @param integer $id
     */
    public function setId($id)
    {
        $this->id = $id;
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
     * @param String $ordem
     */
    public function setOrdem($ordem)
    {
        $this->ordem = $ordem;
    }
    
    /**
     * 
     * @param String $column
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
            "id" => $this->getId(),
            "nome" => $this->getNome()
        );
    }

    /**
     * 
     * @return String
     */
    public function getLabel()
    {
        return $this->getNome();
    }

}
