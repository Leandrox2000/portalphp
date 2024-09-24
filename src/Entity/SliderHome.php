<?php

namespace Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * SliderHome
 *
 * @ORM\Table(name="tb_slider_home", indexes={@ORM\Index(name="IDX_7FF7E21D3C2736E4", columns={"id_imagem"})})
 * @ORM\Entity(repositoryClass="Entity\Repository\SliderHomeRepository")  
 */
class SliderHome extends AbstractEntity implements EntityInterface
{

    /**
     * @var integer
     *
     * @ORM\Column(name="id_slider_home", type="bigint", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\SequenceGenerator(sequenceName="tb_slider_home_id_slider_home_seq", allocationSize=1, initialValue=1)
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
     * @ORM\Column(name="no_slider", type="string", length=120, nullable=false)
     */
    private $nome;

    /**
     * @var string
     *
     * @ORM\Column(name="ds_descricao", type="text", nullable=false)
     */
    private $descricao;

    /**
     * @var integer
     *
     * @ORM\Column(name="st_publicado", type="integer", nullable=false)
     */
    private $publicado = '0';

    /**
     * @var integer
     *
     * @ORM\Column(name="st_propriedade_sede", type="integer", nullable=false)
     */
    private $propriedadeSede = '0';

    /**
     * @var Imagem
     *
     * @ORM\ManyToOne(targetEntity="Entity\Imagem")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_imagem", referencedColumnName="id_imagem")
     * })
     */
    private $imagem;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\ManyToMany(targetEntity="Entity\Site", inversedBy="slidersHome")
     * @ORM\JoinTable(name="tb_slider_home_site",
     *   joinColumns={
     *     @ORM\JoinColumn(name="id_slider_home", referencedColumnName="id_slider_home")
     *   },
     *   inverseJoinColumns={
     *     @ORM\JoinColumn(name="id_site", referencedColumnName="id_site")
     *   }
     * )
     */
    private $sites;

    /**
     * @var string
     *
     * @ORM\Column(name="ds_x1", type="string", length=30, nullable=false)
     */
    private $x1;

    /**
     * @var string
     *
     * @ORM\Column(name="ds_y1", type="string", length=30, nullable=false)
     */
    private $y1;

    /**
     * @var string
     *
     * @ORM\Column(name="ds_x2", type="string", length=30, nullable=false)
     */
    private $x2;

    /**
     * @var string
     *
     * @ORM\Column(name="ds_y2", type="string", length=30, nullable=false)
     */
    private $y2;

    /**
     * @var string
     *
     * @ORM\Column(name="ds_w", type="string", length=30, nullable=false)
     */
    private $w;

    /**
     * @var string
     *
     * @ORM\Column(name="ds_h", type="string", length=30, nullable=false)
     */
    private $h;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->setSites(new \Doctrine\Common\Collections\ArrayCollection());
        $this->setDataCadastro(new \DateTime('now'));
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
    public function getPublicado()
    {
        return $this->publicado;
    }

    /**
     * 
     * @return integer
     */
    public function getPropriedadeSede()
    {
        return $this->propriedadeSede;
    }

    /**
     * 
     * @return Imagem
     */
    public function getImagem()
    {
        return $this->imagem;
    }

    /**
     * 
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getSites()
    {
        return $this->sites;
    }

    /**
     * 
     * @return String
     */
    public function getX1()
    {
        return $this->x1;
    }

    /**
     * 
     * @return String
     */
    public function getX2()
    {
        return $this->x2;
    }

    /**
     * 
     * @return String
     */
    public function getY1()
    {
        return $this->y1;
    }

    /**
     * 
     * @return String
     */
    public function getY2()
    {
        return $this->y2;
    }

    /**
     * 
     * @return String
     */
    public function getW()
    {
        return $this->w;
    }

    /**
     * 
     * @return String
     */
    public function getH()
    {
        return $this->h;
    }

    public function setX1($x1)
    {
        $this->x1 = $x1;
    }

    public function setX2($x2)
    {
        $this->x2 = $x2;
    }

    public function setY1($y1)
    {
        $this->y1 = $y1;
    }

    public function setY2($y2)
    {
        $this->y2 = $y2;
    }

    public function setW($w)
    {
        $this->w = $w;
    }

    public function setH($h)
    {
        $this->h = $h;
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
     * @param integer $propriedadeSede
     */
    public function setPropriedadeSede($propriedadeSede)
    {
        $this->propriedadeSede = $propriedadeSede;
    }

    /**
     * 
     * @param Imagem $imagem
     */
    public function setImagem(Imagem $imagem)
    {
        $this->imagem = $imagem;
    }

    /**
     * 
     * @param \Doctrine\Common\Collections\Collection $sites
     */
    public function setSites(\Doctrine\Common\Collections\Collection $sites)
    {
        $this->sites = $sites;
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
          "id" => $this->getId(),
          "nome" => $this->getNome(),
          "dataCadastro" => $this->getDataCadastro(),
          "dataInicial" => $this->getDataInicial(),
          "dataFinal" => $this->getDataFinal(),
          "descricao" => $this->getDescricao(),
          "imagem" => $this->getImagem(),
          "sites" => $this->getSites(),
          "publicado" => $this->getPublicado(),
          "propriedadeSede" => $this->getPropriedadeSede(),
          "x1" => $this->getX1(),
          "x2" => $this->getX2(),
          "y1" => $this->getY1(),
          "y2" => $this->getY2(),
          "w" => $this->getW(),
          "h" => $this->getH()
        );
    }

}
