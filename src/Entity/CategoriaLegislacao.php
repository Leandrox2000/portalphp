<?php
namespace Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * CategoriaLegislacao
 *
 * @ORM\Table(name="tb_categoria_legislacao", uniqueConstraints={@ORM\UniqueConstraint(name="uk_categoria_legislacao_id_categoria_legislacao", columns={"id_categoria_legislacao"})})
 * @ORM\Entity(repositoryClass="Entity\Repository\CategoriaLegislacaoRepository")
 */
class CategoriaLegislacao extends AbstractEntity implements EntityInterface
{

    /**
     * @var integer
     *
     * @ORM\Column(name="id_categoria_legislacao", type="bigint", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\SequenceGenerator(sequenceName="tb_categoria_legislacao_id_categoria_legislacao_seq", allocationSize=1, initialValue=1)
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="no_categoria", type="string", length=100, nullable=false)
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
