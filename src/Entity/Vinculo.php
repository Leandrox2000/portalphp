<?php
namespace Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Vinculo
 *
 * @ORM\Table(name="tb_vinculo", uniqueConstraints={@ORM\UniqueConstraint(name="uk_vinculo_no_vinculo", columns={"no_vinculo"}), @ORM\UniqueConstraint(name="uk_vinculo_id_vinculo", columns={"id_vinculo"})})
 * @ORM\Entity(repositoryClass="Entity\Repository\VinculoRepository")
 */
class Vinculo extends AbstractEntity implements EntityInterface
{

    /**
     * @var integer
     *
     * @ORM\Column(name="id_vinculo", type="bigint", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\SequenceGenerator(sequenceName="tb_vinculo_id_vinculo_seq", allocationSize=1, initialValue=1)
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="no_vinculo", type="string", length=100, nullable=false)
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

}
