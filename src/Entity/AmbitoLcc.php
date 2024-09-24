<?php

namespace Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * AmbitoLcc
 *
 * @ORM\Table(name="tb_ambito_lcc", uniqueConstraints={@ORM\UniqueConstraint(name="uk_ambito_lcc_id_ambito_lcc", columns={"id_ambito_lcc"}), @ORM\UniqueConstraint(name="uk_ambito_lcc_no_ambito_lcc", columns={"no_ambito_lcc"})})
 * @ORM\Entity(repositoryClass="Entity\Repository\AmbitoLccRepository")
 */
class AmbitoLcc extends AbstractEntity implements EntityInterface
{

    /**
     * @var integer
     *
     * @ORM\Column(name="id_ambito_lcc", type="bigint", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\SequenceGenerator(sequenceName="tb_ambito_lcc_id_ambito_lcc_seq", allocationSize=1, initialValue=1)
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="no_ambito_lcc", type="string", length=150, nullable=false)
     */
    private $nome;

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
