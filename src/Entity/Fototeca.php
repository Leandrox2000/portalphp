<?php

namespace Entity;

use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Fototeca
 *
 * @ORM\Table(name="tb_fototeca")
 * @ORM\Entity(repositoryClass="Entity\Repository\FototecaRepository")  
 */
class Fototeca extends AbstractEntity implements EntityInterface
{

    /**
     * @var integer
     *
     * @ORM\Column(name="id_fototeca", type="bigint", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\SequenceGenerator(sequenceName="tb_fototeca_id_fototeca_seq", allocationSize=1, initialValue=1)
     */
    private $id;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="dt_cadastro", type="datetime", nullable=false)
     */
    private $dataCadastro;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="dt_inicial", type="datetime", nullable=false)
     */
    private $dataInicial;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="dt_final", type="datetime", nullable=true)
     */
    private $dataFinal;

    /**
     * @var string
     *
     * @ORM\Column(name="no_nome", type="string", length=100, nullable=false)
     */
    private $nome;

    /**
     * @var string
     *
     * @ORM\Column(name="ds_descricao", type="text", nullable=true)
     */
    private $descricao;

    /**
     * @var integer
     *
     * @ORM\Column(name="st_publicado", type="integer", nullable=false)
     */
    private $publicado = 0;

   /**
     * @var Collection
     *
     * @ORM\ManyToMany(targetEntity="Galeria", inversedBy="fototecas")
     * @ORM\JoinTable(name="tb_fototeca_galeria",
     *   joinColumns={
     *     @ORM\JoinColumn(name="id_fototeca", referencedColumnName="id_fototeca")
     *   },
     *   inverseJoinColumns={
     *     @ORM\JoinColumn(name="id_galeria", referencedColumnName="id_galeria")
     *   }
     * )
     */
    private $galerias;
    
    /**
     * @var CategoriaFototeca
     *
     * @ORM\ManyToOne(targetEntity="CategoriaFototeca")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_categoria_fototeca", referencedColumnName="id_categoria_fototeca")
     * })
     */
    private $categoria;

    /**
     * @var Collection
     *
     * @ORM\ManyToMany(targetEntity="Fototeca", inversedBy="fototecasPais", cascade={"all"})
     * @ORM\JoinTable(name="tb_fototeca_fototeca",
     *   joinColumns={
     *     @ORM\JoinColumn(name="id_fototeca_pai", referencedColumnName="id_fototeca")
     *   },
     *   inverseJoinColumns={
     *     @ORM\JoinColumn(name="id_fototeca_filha", referencedColumnName="id_fototeca")
     *   }
     * )
     */
    private $fototecasFilhas;

    /**
     * @var integer
     *
     * @ORM\Column(name="nu_ordem", type="bigint", nullable=true)
     */
    private $ordem;

    /**
     * @var Collection
     *
     * @ORM\ManyToMany(targetEntity="Fototeca", mappedBy="fototecasFilhas", fetch="EXTRA_LAZY")
     */
    private $fototecasPais;



    /**
     * Constructor
     */
    public function __construct()
    {
        $this->setDataCadastro(new \DateTime('now'));
        $this->setFototecasFilhas(new \Doctrine\Common\Collections\ArrayCollection());
        $this->setFototecasPais(new \Doctrine\Common\Collections\ArrayCollection());
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
     * @return \DateTime
     */
    public function getDataCadastro()
    {
        return $this->dataCadastro;
    }

    /**
     * 
     * @return \DateTime
     */
    public function getDataInicial()
    {
        return $this->dataInicial;
    }

    /**
     * 
     * @return \DateTime
     */
    public function getDataFinal()
    {
        return $this->dataFinal;
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
    public function getDescricao()
    {
        return $this->descricao;
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
     * @return integer
     */
    public function getPublicado()
    {
        return $this->publicado;
    }

    /**
     * 
     * @param type $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * 
     * @param \DateTime $dataCadastro
     */
    public function setDataCadastro(\DateTime $dataCadastro)
    {
        $this->dataCadastro = $dataCadastro;
    }

    /**
     * 
     * @param \DateTime $dataInicial
     */
    public function setDataInicial(\DateTime $dataInicial)
    {
        $this->dataInicial = $dataInicial;
    }

    /**
     * 
     * @param \DateTime $dataFinal
     */
    // public function setDataFinal(\DateTime $dataFinal)
    // Alterada a necessidade do tipo de dado ser DateTime 
    // A variável $dataFinal também deve poder ser setada como NULL
    public function setDataFinal($dataFinal)
    {
        $this->dataFinal = $dataFinal;
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
     * @param string $descricao
     */
    public function setDescricao($descricao)
    {
        $this->descricao = $descricao;
    }

    /**
     * 
     * @param integer $publicado
     */
    public function setPublicado($publicado)
    {
        $this->publicado = $publicado;
    }

    /**
     * 
     * @return Collection
     */
    public function getGalerias()
    {
        return $this->galerias;
    }

    /**
     * 
     * @param Collection $galerias
     */
    public function setGalerias(Collection $galerias)
    {
        $this->galerias = $galerias;
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
     * @return CategoriaFototeca
     */
    public function getCategoria()
    {
        return $this->categoria;
    }

    /**
     *
     * @param integer $ordem
     */
    public function setOrdem($ordem)
    {
        $this->ordem = $ordem;
    }

    /**
     * 
     * @param CategoriaFototeca $categoria
     */
    public function setCategoria(CategoriaFototeca $categoria)
    {
        $this->categoria = $categoria;
    }

    function getFototecasFilhas() 
    {
        return $this->fototecasFilhas;
    }

    function getFototecasPais() 
    {
        return $this->fototecasPais;
    }

    function setFototecasFilhas(Collection $fototecasFilhas) 
    {
        $this->fototecasFilhas = $fototecasFilhas;
    }

    function setFototecasPais(Collection $fototecasPais) 
    {
        $this->fototecasPais = $fototecasPais;
    }

    /**
     * 
     * @return array
     */
    public function toArray()
    {
        return array(
            "id" => $this->getId(),
            "dataCadastro" => $this->getDataCadastro(),
            "dataInicial" => $this->getDataInicial(),
            "dataFinal" => $this->getDataFinal(),
            "nome" => $this->getNome(),
            "descricao" => $this->getDescricao(),
            "publicado" => $this->getPublicado(),
            "galerias" => $this->getGalerias(),
            "categoria" => $this->getCategoria()
        );
    }

}
