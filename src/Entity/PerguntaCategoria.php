<?php

namespace Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * PerguntaCategoria
 *
 * @ORM\Table(name="tb_pergunta_categoria", uniqueConstraints={@ORM\UniqueConstraint(name="UK_pergunta_categoria_no_categoria", columns={"no_categoria"}), @ORM\UniqueConstraint(name="UK_pergunta_categoria_id_pergunta_categoria", columns={"id_pergunta_categoria"})})
 * @ORM\Entity(repositoryClass="Entity\Repository\PerguntaCategoriaRepository")
 * 
 */
class PerguntaCategoria extends AbstractEntity implements EntityInterface
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id_pergunta_categoria", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="no_categoria", type="string", length=50, nullable=false)
     */
    private $categoria;

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
    public function getCategoria()
    {
        return $this->categoria;
    }

    /**
     * 
     * @return string
     */
    public function getLabel()
    {
        return $this->categoria;
    }

    
    /**
     * 
     * @param string $categoria
     */
    public function setCategoria($categoria)
    {
        $this->categoria = $categoria;
    }

    /**
     * Converte  o Objeto para Array
     * 
     * @return array
     */
    public function toArray()
    {
        return array(
                'id'        =>  $this->getId(),
                'categoria' =>  $this->getCategoria(),
        );
    }


}
