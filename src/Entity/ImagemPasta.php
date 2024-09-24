<?php

namespace Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ImagemPasta
 *
 * @ORM\Table(name="tb_imagem_pasta", uniqueConstraints={@ORM\UniqueConstraint(name="uk_imagem_pasta_nome", columns={"no_nome"})}, indexes={@ORM\Index(name="IDX_E28D6929FCDE030A", columns={"id_imagem_categoria"})})
 * @ORM\Entity
 */
class ImagemPasta extends AbstractEntity implements EntityInterface
{

    /**
     * @var integer 
     *
     * @ORM\Column(name="id_imagem_pasta", type="bigint", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\SequenceGenerator(sequenceName="tb_imagem_pasta_id_imagem_pasta_seq", allocationSize=1, initialValue=1)
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="no_nome", type="string", length=100, nullable=false)
     */
    private $nome;

    /**
     * @var string
     *
     * @ORM\Column(name="no_caminho_pasta", type="string", length=120, nullable=false)
     */
    private $caminho;

    /**
     * @var \Entity\ImagemCategoria
     *
     * @ORM\ManyToOne(targetEntity="Entity\ImagemCategoria", fetch="EAGER")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_imagem_categoria", referencedColumnName="id_imagem_categoria")
     * })
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
    public function getLabel()
    {
        return $this->getNome();
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
     * @return string
     */
    public function getCaminho()
    {
        return $this->caminho;
    }

    /**
     * 
     * @return \Entity\ImagemCategoria
     */
    public function getCategoria()
    {
        return $this->categoria;
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
     * @param string $caminho
     */
    public function setCaminho($caminho)
    {
        $this->caminho = $caminho;
    }

    /**
     * 
     * @param \Entity\ImagemCategoria $categoria
     */
    public function setCategoria(\Entity\ImagemCategoria $categoria)
    {
        $this->categoria = $categoria;
    }
    
    public function toArray(){
        return array(
            'id' => $this->getId(),
            'nome' => $this->getNome(),
            'caminho' => $this->getCaminho(),
            'categoria' => $this->getCategoria()
        );
    }

}
