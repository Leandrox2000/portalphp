<?php

namespace Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Galeria
 *
 * @ORM\Table(name="tb_galeria_ordem")
 * @ORM\Entity(repositoryClass="Entity\Repository\GaleriaSiteRepository")
 */
class GaleriaSite extends AbstractEntity implements EntityInterface
{
    
    /**
     * @var integer
     *
     * @ORM\Column(name="id_galeria_site", type="bigint", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\SequenceGenerator(sequenceName="tb_galeria_ordem_id_galeria_site_seq", allocationSize=1, initialValue=10)
     */
    private $id;
    
    
    /**
     * @var string
     *
     * @ORM\Column(name="nu_ordem", type="bigint", nullable=false)
     */
    private $ordem;
    
    /**
     * @var Site
     *
     * @ORM\ManyToOne(targetEntity="Site", inversedBy="galeriasSite")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_site", referencedColumnName="id_site")
     * })
     */
    private $site;
    
    
     /**
     * @var Galeria
     *
     * @ORM\ManyToOne(targetEntity="Galeria",  inversedBy="galeriasSite")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_galeria", referencedColumnName="id_galeria")
     * })
     */
    private $galeria;
    
    /**
     * 
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }
    public function setId($id)
    {
         $this->id = $id;
    }

    function getOrdem() 
    {
        return $this->ordem;
    }

    function setOrdem($ordem) 
    {
        $this->ordem = $ordem;
    }
    
    function getSite() 
    {
        return $this->site;
    }

    function setSite(Site $site) 
    {
        $this->site = $site;
    }
    
    
    function getGaleria() 
    {
        return $this->galeria;
    }

    function setGaleria(Galeria $galeria) 
    {
        $this->galeria = $galeria;
    }

    /**
     * 
     * @return string
     */
    public function getLabel()
    {
        return $this->ordem;
    }

    public function toArray()
    {
        return array(
            "id" => $this->getId(),
            "ordem" => $this->getOrdem(),
            "site" => $this->getSite(),
            "galeria" => $this->getGaleria(),
        );
    }

}
