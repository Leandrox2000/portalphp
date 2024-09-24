<?php

namespace Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Galeria
 *
 * @ORM\Table(name="tb_video_ordem")
 * @ORM\Entity(repositoryClass="Entity\Repository\VideoSiteRepository")
 */
class VideoSite extends AbstractEntity implements EntityInterface
{
    
    /**
     * @var integer
     *
     * @ORM\Column(name="id_video_site", type="bigint", nullable=false)
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
     * @ORM\ManyToOne(targetEntity="Site", inversedBy="videosSite")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_site", referencedColumnName="id_site")
     * })
     */
    private $site;
    
    
     /**
     * @var Video
     *
     * @ORM\ManyToOne(targetEntity="Video",  inversedBy="videosSite")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_video", referencedColumnName="id_video")
     * })
     */
    private $video;
    
    
    
    
    /**
     * 
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }
    function setId($id) 
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
    
    
    function getVideo() 
    {
        return $this->video;
    }

    function setVideo(Video $video) 
    {
        $this->video = $video;
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
            "video" => $this->getVideo(),
        );
    }

}
