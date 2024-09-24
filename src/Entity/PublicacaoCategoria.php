<?php

namespace Entity;


use Doctrine\ORM\Mapping as ORM;



/**
 * PublicacaoCategoria
 *
 * @ORM\Table(name="tb_publicacao_categoria", uniqueConstraints={@ORM\UniqueConstraint(name="uk_publicacao_categoria_no_categoria", columns={"no_categoria"})})
 * @ORM\Entity(repositoryClass="Entity\Repository\PublicacaoCategoriaRepository")
 */
class PublicacaoCategoria extends AbstractEntity implements EntityInterface
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id_publicacao_categoria", type="bigint", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\SequenceGenerator(sequenceName="tb_publicacao_categoria_id_publicacao_categoria_seq", allocationSize=1, initialValue=1)
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="no_categoria", type="string", length=50, nullable=false)
     */
    private $nome;

    /**
     * @var string
     *
     * @ORM\Column(name="ds_descricao", type="text", nullable=false)
     */
    private $descricao;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\OneToMany(targetEntity="Publicacao", mappedBy="categoria", fetch="EXTRA_LAZY")
     */
    private $publicacoes;

    /**
     * @var integer
     *
     * @ORM\Column(name="nu_ordem", type="bigint", nullable=false)
     */
    private $ordem;

    /**
     *
     * @contrutor
     */
    public function __construct()
    {
        $this->setPublicacoes(new \Doctrine\Common\Collections\ArrayCollection());
    }

    /**
     *
     * @return string
     */
    public function getDescricao()
    {
        return $this->descricao;
    }

    /**
     *
     * @param string $descricao
     */
    public function setDescricao($descricao)
    {
        $this->descricao = $descricao;
    }

    /**
     *
     * @return int
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
     * @return integer
     */
    public function getOrdem()
    {
        return $this->ordem;
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
     * @return string
     */
    public function getLabel()
    {
        return $this->getNome();
    }

    /**
     * 
     * @param String $ordem
     */
    public function setOrdem($ordem)
    {
        $this->ordem = $ordem;
    }

    /**
     *
     * @return array
     */
    public function toArray()
    {
        return array(
                'id'=>$this->getId(),
                'nome'=>$this->getNome(),
        );
    }

    /**
     *
     * @return type
     */
    public function getPublicacoes()
    {
        return $this->publicacoes;
    }

    /**
     *
     * @param \Doctrine\Common\Collections\Collection $publicacoes
     */
    public function setPublicacoes(\Doctrine\Common\Collections\Collection $publicacoes)
    {
        $this->publicacoes = $publicacoes;
    }


}
