<?php

namespace Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Funcionalidade
 *
 * @ORM\Table(name="tb_funcionalidade_site")
 * @ORM\Entity(repositoryClass="Entity\Repository\FuncionalidadeSiteRepository")
 */
class FuncionalidadeSite extends AbstractEntity implements EntityInterface
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id_funcionalidade_site", type="bigint", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\SequenceGenerator(sequenceName="tb_funcionalidade_site_id_funcionalidade_site_seq", allocationSize=1, initialValue=10)
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
     * @ORM\ManyToOne(targetEntity="Site", inversedBy="funcionalidadesSite")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_site", referencedColumnName="id_site")
     * })
     */
    private $site;
    
    /**
     * @var Funcionalidade
     *
     * @ORM\ManyToOne(targetEntity="Funcionalidade",  inversedBy="funcionalidadesSite")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_funcionalidade", referencedColumnName="id_funcionalidade")
     * })
     */
    private $funcionalidade;
    
    /**
     * 
     * @return integer
     */
    public function getId()
    {
        return $this->id;
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

    function getFuncionalidade() {
        return $this->funcionalidade;
    }

    function setFuncionalidade(Funcionalidade $funcionalidade) {
        $this->funcionalidade = $funcionalidade;
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
        );
    }

}
