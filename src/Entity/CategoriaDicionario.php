<?php

namespace Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * CategoriaDicionario
 *
 * @ORM\Table(name="tb_categoria_dpc", uniqueConstraints={@ORM\UniqueConstraint(name="uk_categoria_dpc_id_categoria_dpc", columns={"id_categoria_dpc"})})
 * @ORM\Entity(repositoryClass="Entity\Repository\CategoriaDicionarioRepository")
 */
class CategoriaDicionario extends AbstractEntity implements EntityInterface
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id_categoria_dpc", type="bigint", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\SequenceGenerator(sequenceName="tb_categoria_dpc_id_categoria_dpc_seq", allocationSize=1, initialValue=1)
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="no_categoria", type="string", length=50, nullable=false)
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
    
    public function getLabel()
    {
        return $this->getNome();
    }
    
    public function toArray()
    {
        return array(
            'id' => $this->getId(),
            'nome' => $this->getNome(),
        );
    }

}
