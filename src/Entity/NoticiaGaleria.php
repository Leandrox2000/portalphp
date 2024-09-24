<?php

namespace Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * NoticiaGaleria
 *
 * @ORM\Table(name="tb_noticia_galeria", indexes={@ORM\Index(name="IDX_3135593E478D78F9", columns={"id_galeria"}), @ORM\Index(name="IDX_3135593ED8407959", columns={"id_noticia"})})
 * @ORM\Entity(repositoryClass="Entity\Repository\NoticiaGaleriaRepository")
 */  
class NoticiaGaleria extends AbstractEntity implements EntityInterface
{

    /**
     * @var integer
     *
     * @ORM\Column(name="id_noticia_galeria", type="bigint", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\SequenceGenerator(sequenceName="tb_noticia_galeria_id_noticia_galeria_seq", allocationSize=1, initialValue=1)
     */
    private $id;

    /**
     * @var integer
     *
     * @ORM\Column(name="ds_ordem_galeria", type="bigint", nullable=true)
     */
    private $ordemGaleria;

    /**
     * @var integer
     *
     * @ORM\Column(name="st_posicao_pagina", type="bigint", nullable=false)
     */
    private $posicaoPagina;

    /**
     * @var Galeria
     *
     * @ORM\ManyToOne(targetEntity="Galeria")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_galeria", referencedColumnName="id_galeria")
     * })
     */
    private $galeria;

    /**
     * @var Noticia
     *
     * @ORM\ManyToOne(targetEntity="Noticia")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_noticia", referencedColumnName="id_noticia")
     * })
     */
    private $noticia;

    /**
     * 
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * 
     * @return integer
     */
    public function getPosicaoPagina()
    {
        return $this->posicaoPagina;
    }

    /**
     * 
     * @return integer
     */
    public function getOrdemGaleria()
    {
        return $this->ordemGaleria;
    }

    /**
     * 
     * @return Galeria
     */
    public function getGaleria()
    {
        return $this->galeria;
    }

    /**
     * 
     * @return Noticia
     */
    public function getNoticia()
    {
        return $this->Noticia;
    }

    /**
     * 
     * @param integer $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * 
     * @param integer $posicaoPagina
     */
    public function setPosicaoPagina($posicaoPagina)
    {
        $this->posicaoPagina = $posicaoPagina;
    }

    /**
     * 
     * @param integer $ordemGaleria
     */
    public function setOrdemGaleria($ordemGaleria)
    {
        $this->ordemGaleria = $ordemGaleria;
    }

    /**
     * 
     * @param Galeria $galeria
     */
    public function setGaleria(Galeria $galeria)
    {
        $this->galeria = $galeria;
    }

    /**
     * 
     * @param Noticia $noticia
     */
    public function setNoticia(Noticia $noticia)
    {
        $this->noticia = $noticia;
    }

    /**
     * 
     * @return integer
     */
    public function getLabel()
    {
        return $this->getPosicaoPagina();
    }

    /**
     * 
     * @return array
     */
    public function toArray()
    {
        return array(
            "id" => $this->getId(),
            "noticia" => $this->getNoticia(),
            "galeria" => $this->getGaleria(),
            "posicaoPagina" => $this->getPosicaoPagina(),
            "ordemGaleria" => $this->getOrdemGaleria()
        );
    }

}
