<?php

namespace Entity;

use Doctrine\ORM\Mapping as ORM;


/**
 * RedeSocialBiblioteca
 *
 * @ORM\Table(name="tb_rede_social_biblioteca", indexes={@ORM\Index(name="IDX_BAACC35857B763E0", columns={"id_biblioteca"})})
 * @ORM\Entity(repositoryClass="Entity\Repository\RedeSocialBibliotecaRepository")
 */
class RedeSocialBiblioteca extends AbstractEntity implements EntityInterface
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id_rede_social_biblioteca", type="bigint", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\SequenceGenerator(sequenceName="tb_rede_social_biblioteca_id_rede_social_biblioteca_seq", allocationSize=1, initialValue=1)
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="no_rede_social", type="string", length=50, nullable=false)
     */
    private $redeSocial;

    /**
     * @var string
     *
     * @ORM\Column(name="ds_url", type="string", length=100, nullable=false)
     */
    private $url;

    /**
     * @var \Entity\Biblioteca
     *
     * @ORM\ManyToOne(targetEntity="Entity\Biblioteca")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_biblioteca", referencedColumnName="id_biblioteca")
     * })
     */
    private $biblioteca;

    public function getId()
    {
        return $this->id;
    }

    public function getRedeSocial()
    {
        return $this->redeSocial;
    }

    public function getUrl()
    {
        return $this->url;
    }

    public function getBiblioteca()
    {
        return $this->biblioteca;
    }

    /**
     * 
     * @param int $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * 
     * @param string $redeSocial
     */
    public function setRedeSocial($redeSocial)
    {
        $this->redeSocial = $redeSocial;
    }

    /**
     * 
     * @param string $url
     */
    public function setUrl($url)
    {
        $this->url = $url;
    }

    /**
     * 
     * @param \Entity\Biblioteca $biblioteca
     */
    public function setBiblioteca(\Entity\Biblioteca $biblioteca)
    {
        $this->biblioteca = $biblioteca;
    }

    public function getLabel()
    {
        return $this->getRedeSocial();
    }
    
    public function toArray()
    {
        return array(
            'id' => $this->getId(),
            'redeSocial' => $this->getRedeSocial(),
            'url' => $this->getUrl(),
            'biblioteca' => $this->getBiblioteca(),
        );
    }

}
