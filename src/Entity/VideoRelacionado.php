<?php

namespace Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * VideoRelacionado
 *
 * @ORM\Table(name="tb_video_relacionado")
 * @ORM\Entity(repositoryClass="Entity\Repository\VideoRelacionadoRepository")
 */
class VideoRelacionado extends AbstractEntity implements EntityInterface
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="bigint", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\SequenceGenerator(sequenceName="tb_video_relacionado_id_seq", allocationSize=1, initialValue=10)
     */
    private $id;
    
    
    /**
     * @var integer
     *
     * @ORM\Column(name="nu_ordem", type="bigint", nullable=false)
     */
    private $ordem;
    
    /**
     * @var \Entity\Video
     *
     * @ORM\ManyToOne(targetEntity="Entity\Video", inversedBy="relacionados")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_video_relacionado", referencedColumnName="id_video")
     * })
     */
    private $video;
    
    /**
     * @var \Entity\Video
     *
     * @ORM\ManyToOne(targetEntity="Entity\Video")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_relacionado", referencedColumnName="id_video")
     * })
     */
    private $relacionado;
    
    /**
     * @return integer
     */
    function getId() {
        return $this->id;
    }

    /**
     * @return integer
     */
    function getOrdem() {
        return $this->ordem;
    }

    /**
     * @return \Entity\Video
     */
    function getVideo() {
        return $this->video;
    }

    /**
     * @return \Entity\Video
     */
    function getRelacionado() {
        return $this->relacionado;
    }

    /**
     * @param integer $id
     * @return \Entity\VideoRelacionado
     */
    function setId($id) {
        $this->id = $id;
        return $this;
    }

    /**
     * @param integer $ordem
     * @return \Entity\VideoRelacionado
     */
    function setOrdem($ordem) {
        $this->ordem = $ordem;
        return $this;
    }

    /**
     * @param \Entity\Video $video
     * @return \Entity\VideoRelacionado
     */
    function setVideo(\Entity\Video $video) {
        $this->video = $video;
        return $this;
    }

    /**
     * @param \Entity\Video $relacionado
     * @return \Entity\VideoRelacionado
     */
    function setRelacionado(\Entity\Video $relacionado) {
        $this->relacionado = $relacionado;
        return $this;
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
            'id' => $this->getId(),
            'ordem' => $this->getOrdem(),
            'video' => $this->getVideo(),
            'relacionado' => $this->getRelacionado(),
        );
    }
    
}