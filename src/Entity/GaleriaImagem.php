<?php

namespace Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * GaleriaImagem
 *  
 * @ORM\Table(name="tb_galeria_imagem") 
 * @ORM\Entity() 
 */
class GaleriaImagem
{
	/**
     * @var integer
     *
     * @ORM\Column(name="id_galeria_imagem", type="bigint", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\SequenceGenerator(sequenceName="tb_galeria_ordem_id_galeria_imagem_seq", allocationSize=1, initialValue=10)
     */
    private $id;


    /**
     * @var string
     *
     * @ORM\Column(name="nu_ordem", type="bigint", nullable=false)
     */
    private $ordem;

    /**
     * @var string
     *
     * @ORM\Column(name="id_imagem", type="bigint", nullable=false)
     */
    private $imagemId;
    
    /**
     * @var Imagem
     *
     * @ORM\ManyToOne(targetEntity="Entity\Imagem", inversedBy="galerias")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_imagem", referencedColumnName="id_imagem")
     * })
     */
    private $imagem;

     /**
     * @var Galeria
     *
     * @ORM\ManyToOne(targetEntity="Entity\Galeria",  inversedBy="imagens")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_galeria", referencedColumnName="id_galeria")
     * })
     */
    private $galeria;
	
    /**
     * 
     * @return integer
     */
    public function getGaleria()
    {
        return $this->galeria;
    }
    
    /**
     * 
     * @return integer
     */
    public function getImagem()
    {
        return $this->imagem;
    }
    
    /**
     * 
     * @return integer
     */
    public function getOrdem()
    {
        return $this->ordem;
    }
    
    /**
     * 
     * @param \Doctrine\Common\Collections\Collection $imagem
     */
    public function setImagens(\Doctrine\Common\Collections\Collection $imagens)
    {
        $this->imagens = $imagens;
    }
    
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->setImagens(new \Doctrine\Common\Collections\ArrayCollection());
        
    }
    
    /**
     * 
     * @return array
     */
    public function toArray()
    {
        return array(
            "galeria" => $this->getGaleria(),
            "imagem" => $this->getImagem(),
            "ordem" => $this->getOrdem()
        );
    }

}
