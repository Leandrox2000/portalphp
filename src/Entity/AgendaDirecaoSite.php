<?php

namespace Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * AgendaDirecaoSite
 *
 * @ORM\Table(name="tb_agenda_direcao_site")
 * @ORM\Entity(repositoryClass="Entity\Repository\AgendaDirecaoSiteRepository")
 */
class AgendaDirecaoSite extends AbstractEntity implements EntityInterface
{    
    /**
     * @var integer
     *
     * @ORM\Column(name="id_agenda_direcao_site", type="bigint", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\SequenceGenerator(sequenceName="tb_agenda_direcao_site_id_agenda_direcao_site_seq", allocationSize=1, initialValue=10)
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
     * @ORM\ManyToOne(targetEntity="Site", inversedBy="agendaDirecaoSite")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_site", referencedColumnName="id_site")
     * })
     */
    private $site;    
    
    /**
     * @var AgendaDirecao
     *
     * @ORM\ManyToOne(targetEntity="AgendaDirecao",  inversedBy="agendaDirecaoSite")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_agenda_direcao", referencedColumnName="id_agenda_direcao")
     * })
     */
    private $agendaDirecao;
        
    /**
     * 
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }
    
    /**
     * @param integer $id
     */
    function setId($id) 
    {
        $this->id = $id;
    }
    
    /**
     * @return integer
     */
    function getOrdem() 
    {
        return $this->ordem;
    }
    
    /**
     * @param integer $ordem
     */
    function setOrdem($ordem) 
    {
        $this->ordem = $ordem;
    }    
    
    /**
     * @return \Entity\Site
     */
    function getSite() 
    {
        return $this->site;
    }
    
    /**
     * @param \Entity\Site $site
     */
    function setSite(Site $site) 
    {
        $this->site = $site;
    }
    
    /**
     * @return \Entity\AgendaDirecao
     */
    function getAgendaDirecao() 
    {
        return $this->agendaDirecao;
    }

    /**
     * @param \Entity\AgendaDirecao $agendaDirecao
     */
    function setAgendaDirecao(AgendaDirecao $agendaDirecao) 
    {
        $this->agendaDirecao = $agendaDirecao;
    }

    /**
     * 
     * @return string
     */
    public function getLabel()
    {
        return $this->ordem;
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return array(
            "id" => $this->getId(),
            "ordem" => $this->getOrdem(),
            "site" => $this->getSite(),
            "agendaDirecao" => $this->getAgendaDirecao(),
        );
    }

}
