<?php

namespace Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * TipoLcc
 *
 * @ORM\Table(name="tb_tipo_lcc", uniqueConstraints={@ORM\UniqueConstraint(name="uk_tipo_lcc_id_tipo_lcc", columns={"id_tipo_lcc"}), @ORM\UniqueConstraint(name="uk_tipo_lcc_no_tipo_lcc", columns={"no_tipo_lcc"})})
 * @ORM\Entity(repositoryClass="Entity\Repository\TipoLccRepository")
 */
class TipoLcc extends AbstractEntity implements EntityInterface
{

    /**
     * @var integer
     *
     * @ORM\Column(name="id_tipo_lcc", type="bigint", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\SequenceGenerator(sequenceName="tb_tipo_lcc_id_tipo_lcc_seq", allocationSize=1, initialValue=1)
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="no_tipo_lcc", type="string", length=100, nullable=false)
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
