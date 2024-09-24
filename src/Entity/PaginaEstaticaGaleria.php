<?php

namespace Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * PaginaEstaticaGaleria
 *
 * @ORM\Table(name="tb_pagina_estatica_galeria", indexes={@ORM\Index(name="IDX_3135593E478D78F9", columns={"id_galeria"}), @ORM\Index(name="IDX_3135593ED8407959", columns={"id_pagina_estatica"})})
 * @ORM\Entity(repositoryClass="Entity\Repository\PaginaEstaticaGaleriaRepository")
 */
class PaginaEstaticaGaleria extends AbstractEntity implements EntityInterface
{

    /**
     * @var integer
     *
     * @ORM\Column(name="id_pagina_estatica_galeria", type="bigint", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\SequenceGenerator(sequenceName="tb_pagina_estatica_galeria_id_pagina_estatica_galeria_seq", allocationSize=1, initialValue=1)
     */
    private $id;

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
     * @var PaginaEstatica
     *
     * @ORM\ManyToOne(targetEntity="PaginaEstatica")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_pagina_estatica", referencedColumnName="id_pagina_estatica")
     * })
     */
    private $paginaEstatica;

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
     * @return Galeria
     */
    public function getGaleria()
    {
        return $this->galeria;
    }

    /**
     * 
     * @return PaginaEstatica
     */
    public function getPaginaEstatica()
    {
        return $this->paginaEstatica;
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
     * @param Galeria $galeria
     */
    public function setGaleria(Galeria $galeria)
    {
        $this->galeria = $galeria;
    }

    /**
     * 
     * @param PaginaEstatica $paginaEstatica
     */
    public function setPaginaEstatica(PaginaEstatica $paginaEstatica)
    {
        $this->paginaEstatica = $paginaEstatica;
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
            "paginaEstatica" => $this->getPaginaEstatica(),
            "galeria" => $this->getGaleria(),
            "posicaoPagina" => $this->getPosicaoPagina()
        );
    }

}
