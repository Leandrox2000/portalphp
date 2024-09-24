<?php

namespace Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * CategoriaFototeca
 *
 * @ORM\Table(name="tb_categoria_fototeca")
 * @ORM\Entity(repositoryClass="Entity\Repository\CategoriaFototecaRepository")
 */
class CategoriaFototeca extends AbstractEntity implements EntityInterface
{

    /**
     * @var integer
     *
     * @ORM\Column(name="id_categoria_fototeca", type="bigint", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\SequenceGenerator(sequenceName="tb_categoria_fototeca_id_categoria_fototeca_seq", allocationSize=1, initialValue=1)
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="no_categoria_fototeca", type="string", length=100, nullable=false)
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
    public function toArray(){
        
        return array(
            "id" => $this->getId(),
            "nome" => $this->getNome()
        );
    }

}
