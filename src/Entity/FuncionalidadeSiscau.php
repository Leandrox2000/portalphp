<?php

namespace Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * FuncionalidadeSiscau
 *
 * @ORM\Table(name="tb_funcionalidade_siscau")
 * @ORM\Entity(repositoryClass="Entity\Repository\FuncionalidadeSiscauRepository")
 * 
 */
class FuncionalidadeSiscau extends AbstractEntity implements EntityInterface
{

    /**
     * @var integer
     *
     * @ORM\Column(name="id_funcionalidade_siscau", type="bigint", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\SequenceGenerator(sequenceName="tb_funcionalidade_siscau_id_funcionalidade_siscau_seq", allocationSize=1, initialValue=1)
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="no_controller", type="string", length=50, nullable=false)
     */
    private $controller;

    /**
     * @var string
     *
     * @ORM\Column(name="no_acao", type="string", length=50, nullable=false)
     */
    private $acao;

    /**
     * @var string
     *
     * @ORM\Column(name="sg_funcionalidade", type="string", length=30, nullable=false)
     */
    private $sigla;

    

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
     * @return string
     */
    public function getController()
    {
        return $this->controller;
    }

    /**
     * 
     * @return acao
     */
    public function getAcao()
    {
        return $this->acao;
    }

    /**
     * 
     * @return sigla
     */
    public function getSigla()
    {
        return $this->sigla;
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
     * @param string $controller
     */
    public function setController($controller)
    {
        $this->controller = $controller;
    }

    /**
     * 
     * @param string $acao
     */
    public function setAcao($acao)
    {
        $this->acao = $acao;
    }

    /**
     * 
     * @param string $sigla
     */
    public function setSigla($sigla)
    {
        $this->sigla = $sigla;
    }

    /**
     * 
     * @return string
     */
    public function getLabel()
    {
        return $this->getSigla();
    }

    /**
     * 
     * @return array
     */
    public function toArray()
    {
        return array(
            "id" => $this->getId(),
            "controller" => $this->getController(),
            "acao" => $this->getAcao(),
            "sigla" => $this->getSigla()
        );
    }

}
