<?php

namespace Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * EditalSite
 *
 * @ORM\Entity
 * @ORM\Table(name="tb_edital_site")
 * @property int $role_id
 * @property int $resource_id
 */
class EditalSite extends AbstractEntity {
    
    /**
     * @var Site
     * 
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="Site")
     * @ORM\JoinColumns({ @ORM\JoinColumn(name="id_site", referencedColumnName="id_site") })
     */
    
    private $site;

     /**
     * @var EditalSite
      * 
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="Edital",  inversedBy="editalSite")
     * @ORM\JoinColumns({ @ORM\JoinColumn(name="id_edital", referencedColumnName="id_edital") })
     */
    private $edital;
    
    public function getId(){
        
        return $this->id;
    }
    
    public function setId($id){
        
        $this->id = $id;
    }
    
    
    public function getSite(){
        return $this->site;
    }

    public function setSite(Site $site) {
        $this->site = $site;
    }
    
    function getEdital() {
        return $this->edital;
    }

    function setEdital(Edital $edital) {
        $this->edital = $edital;
    }
}
