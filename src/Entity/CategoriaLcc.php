<?php

namespace Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * CategoriaLcc
 *
 * @ORM\Table(name="tb_categoria_lcc", uniqueConstraints={@ORM\UniqueConstraint(name="uk_categoria_lcc_no_categoria_lcc", columns={"no_categoria_lcc"}), @ORM\UniqueConstraint(name="uk_categoria_lcc_id_categoria_lcc", columns={"id_categoria_lcc"})})
 * @ORM\Entity(repositoryClass="Entity\Repository\CategoriaLccRepository")
 */
class CategoriaLcc extends AbstractEntity implements EntityInterface
{

    /**
     * @var integer
     *
     * @ORM\Column(name="id_categoria_lcc", type="bigint", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\SequenceGenerator(sequenceName="tb_categoria_lcc_id_categoria_lcc_seq", allocationSize=1, initialValue=6)
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="no_categoria_lcc", type="string", length=100, nullable=false)
     */
    private $nome;

      /**
     * @var integer
     *
     * @ORM\Column(name="st_permite_excluir", type="smallint", nullable=false)
     */
    private $permiteExcluir = 1;
    
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
     * @return integer
     */
    public function getPermiteExcluir()
    {
        return $this->permiteExcluir;
    }

    /**
     * 
     * @param integer $permiteExcluir
     */
    public function setPermiteExcluir($permiteExcluir)
    {
        $this->permiteExcluir = $permiteExcluir;
    }

        
    /**
     * 
     * @return array
     */
    public function toArray()
    {
        return array(
            "id" => $this->getId(),
            "nome" => $this->getNome(),
            "permiteExcluir" => $this->getPermiteExcluir()
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
