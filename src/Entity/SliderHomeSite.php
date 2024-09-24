<?php

namespace Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * SliderHomeSite
 *  
 * @ORM\Table(name="tb_slider_home_site") 
 * @ORM\Entity() 
 */
class SliderHomeSite
{
    /**
     * @var string
     * 
     * @ORM\Column(name="nu_ordem", type="bigint", nullable=false)
     */
    private $ordem;

    /**
     * @var string
     * @ORM\Id
     * @ORM\Column(name="id_slider_home", type="bigint", nullable=false)
     */
    private $idSliderHome;

    /**
     * @var string
     * @ORM\Id
     * @ORM\Column(name="id_site", type="bigint", nullable=false)
     */
    private $idSite;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->setImagens(new \Doctrine\Common\Collections\ArrayCollection());
        
    }
    
    public function getOrdem(){
    	return $this->ordem;
    }

    public function getIdSliderHome(){
    	return $this->idSliderHome;
    }
    
    /**
     * @return array
     */
    public function toArray()
    {
        return array(
            "idSliderHome" => $this->getIdSliderHome(),
            "idSite" => $this->getIdSite(),
            "ordem" => $this->getOrdem()
        );
    }

}
