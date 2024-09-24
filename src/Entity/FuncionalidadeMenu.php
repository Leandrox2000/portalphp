<?php

namespace Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * FuncionalidadeMenu
 *
 * @ORM\Table(name="tb_funcionalidade_menu")
 * @ORM\Entity
 */
class FuncionalidadeMenu extends AbstractEntity implements EntityInterface
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id_funcionalidade_menu", type="bigint", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\SequenceGenerator(sequenceName="tb_funcionalidade_menu_id_funcionalidade_menu_seq", allocationSize=1, initialValue=1)
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="ds_funcionalidade", type="string", length=100, nullable=false)
     */
    private $funcionalidade;

    /**
     * @var string
     *
     * @ORM\Column(name="no_url", type="string", length=100, nullable=false)
     */
    private $url;

    /**
     * @var string
     *
     * @ORM\Column(name="no_entidade", type="string", length=100, nullable=false)
     */
    private $entidade;

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
    public function getFuncionalidade()
    {
        return $this->funcionalidade;
    }

    /**
     * 
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * 
     * @return string
     */
    public function getEntidade()
    {
        return $this->entidade;
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
     * @param string $funcionalidade
     */
    public function setFuncionalidade($funcionalidade)
    {
        $this->funcionalidade = $funcionalidade;
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
     * @param string $entidade
     */
    public function setEntidade($entidade)
    {
        $this->entidade = $entidade;
    }

    /**
     * 
     * @return string
     */
    public function getLabel()
    {
        return $this->getFuncionalidade();
    }

    /**
     * 
     * @return array
     */
    public function toArray()
    {
        return array(
            'id' => $this->getId(),
            'entidade' => $this->getEntidade(),
            'funcionalidade' => $this->getFuncionalidade(),
            'url' => $this->getUrl(),
        );
    }

}
