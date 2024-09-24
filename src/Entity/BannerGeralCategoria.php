<?php

namespace Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * BannerGeralCategoria
 *
 * @ORM\Table(name="tb_banner_geral_categoria", uniqueConstraints={@ORM\UniqueConstraint(name="uk_banner_geral_categoria_banner_geral_categoria", columns={"id_banner_geral_categoria"}), @ORM\UniqueConstraint(name="uk_banner_geral_categoria_categoria", columns={"no_categoria"})})
 * @ORM\Entity(repositoryClass="Entity\Repository\BannerGeralCategoriaRepository")
 */
class BannerGeralCategoria extends AbstractEntity implements EntityInterface
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id_banner_geral_categoria", type="bigint", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\SequenceGenerator(sequenceName="tb_banner_geral_categoria_id_banner_geral_categoria_seq", allocationSize=1, initialValue=1)
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="no_categoria", type="string", length=100, nullable=false)
     */
    private $nome;

    /**
     * @var integer
     *
     * @ORM\Column(name="st_sede", type="bigint", nullable=false)
     */
    private $sede;

    
    /**
     * @var string
     *
     * @ORM\Column(name="no_nome_categoria_siscau", type="string", length=100, nullable=false)
     */
    private $nomeCategoriaSiscau;
    
    /**
     * 
     * @return integer
     */
    public function getSede()
    {
        return $this->sede;
    }

    /**
     * 
     * @param integer $sede
     */
    public function setSede($sede)
    {
        $this->sede = $sede;
    }

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
    public function getNome()
    {
        return $this->nome;
    }
    
    /**
     * 
     * @return string
     */
    public function getNomeCategoriaSiscau()
    {
        return $this->nomeCategoriaSiscau;
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
     * @param string $nome
     */
    public function setNome($nome)
    {
        $this->nome = $nome;
    }
    
    /**
     * 
     * @param string $nomeCategoriaSiscau
     */
    public function setNomeCategoriaSiscau($nomeCategoriaSiscau)
    {
        $this->nomeCategoriaSiscau = $nomeCategoriaSiscau;
    }
    
    /**
     * 
     * @return string
     */
    public function getLabel()
    {
        return $this->getNome();
    }

    /**
     * 
     * @return array
     */
    public function toArray()
    {
        return array(
            'id' => $this->getId(),
            'nome' => $this->getNome(),
            'sede' => $this->getSede(),
            'nomeCategoriaSiscau' => $this->getNomeCategoriaSiscau(),
        );
    }

}
