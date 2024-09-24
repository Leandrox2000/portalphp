<?php

namespace Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ImagemCategoria
 *
 * @ORM\Table(name="tb_imagem_categoria")
 * @ORM\Entity(repositoryClass="Entity\Repository\ImagemCategoriaRepository")  
 */
class ImagemCategoria extends AbstractEntity implements EntityInterface
{

    /**
     * @var integer
     *
     * @ORM\Column(name="id_imagem_categoria", type="bigint", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\SequenceGenerator(sequenceName="tb_imagem_categoria_id_imagem_categoria_seq", allocationSize=1, initialValue=1)
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
     * @param int $id
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
          'id' => $this->getId(),
          'nome' => $this->getNome(),
        );
    }

}
