<?php

namespace Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * FototecaGaleria
 *  
 * @ORM\Table(name="tb_fototeca_galeria") 
 * @ORM\Entity() 
 */
class FototecaGaleria
{
	/**
     * @var integer
     *
     * @ORM\Column(name="id_fototeca_galeria", type="bigint", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\SequenceGenerator(sequenceName="tb_fototeca_galeria_id_fototeca_galeria_seq", allocationSize=1, initialValue=10)
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
     * @ORM\Column(name="id_fototeca", type="bigint", nullable=false)
     */
    private $idFototeca;

    /**
     * @var string
     *
     * @ORM\Column(name="id_galeria", type="bigint", nullable=false)
     */
    private $idGaleria;
        
    /**
     * @var Galeria
     *
     * @ORM\ManyToOne(targetEntity="Entity\Galeria", inversedBy="fototecas")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_galeria", referencedColumnName="id_galeria")
     * })
     */
    private $galeria;

     /**
     * @var Fototecas
     *
     * @ORM\ManyToOne(targetEntity="Entity\Fototeca",  inversedBy="galerias")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_fototeca", referencedColumnName="id_fototeca")
     * })
     */
    private $fototeca;
	
    /**
     * 
     * @return integer
     */
    public function getFototeca()
    {
        return $this->fototeca;
    }
    
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
    public function getOrdem()
    {
        return $this->ordem;
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
            "fototeca" => $this->getFototeca(),
            "galeria" => $this->getGaleria(),
            "ordem" => $this->getOrdem()
        );
    }

}
