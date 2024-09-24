<?php

namespace Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Legislacao
 *
 * @ORM\Table(name="tb_legislacao", indexes={@ORM\Index(name="IDX_FAAD2D77D20ECE93", columns={"id_categoria_legislacao"})})
 * @ORM\Entity(repositoryClass="Entity\Repository\LegislacaoRepository")
 */
class Legislacao extends AbstractEntity implements EntityInterface
{

    /**
     * @var integer
     *
     * @ORM\Column(name="id_legislacao", type="bigint", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\SequenceGenerator(sequenceName="tb_legislacao_id_legislacao_seq", allocationSize=1, initialValue=1)
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
     * @var \DateTime
     *
     * @ORM\Column(name="dt_legislacao", type="datetime", nullable=true)
     */
    private $dataLegislacao;

    /**
     * @var string
     *
     * @ORM\Column(name="no_titulo", type="string", length=100, nullable=false)
     */
    private $titulo;

    /**
     * @var string
     *
     * @ORM\Column(name="no_arquivo", type="string", length=150, nullable=true)
     */
    private $arquivo;

    /**
     * @var string
     *
     * @ORM\Column(name="ds_url", type="string", length=150, nullable=true)
     */
    private $url;

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
    private $publicado = '0';

    /**
     * @var integer
     *
     * @ORM\Column(name="st_propriedade_sede", type="integer", nullable=false)
     */
    private $propriedadeSede = '0';

    /**
     * @var CategoriaLegislacao
     *
     * @ORM\ManyToOne(targetEntity="Entity\CategoriaLegislacao")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_categoria_legislacao", referencedColumnName="id_categoria_legislacao")
     * })
     */
    private $categoriaLegislacao;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\ManyToMany(targetEntity="Entity\Site", inversedBy="legislacoes")
     * @ORM\JoinTable(name="tb_legislacao_site",
     *   joinColumns={
     *     @ORM\JoinColumn(name="id_legislacao", referencedColumnName="id_legislacao")
     *   },
     *   inverseJoinColumns={
     *     @ORM\JoinColumn(name="id_site", referencedColumnName="id_site")
     *   }
     * )
     */
    private $sites;
    
    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\ManyToMany(targetEntity="Entity\Site", inversedBy="paiLegislacao", cascade={"persist"})
     * @ORM\JoinTable(name="tb_pai_legislacao_site",
     *   joinColumns={
     *     @ORM\JoinColumn(name="id_legislacao", referencedColumnName="id_legislacao")
     *   },
     *   inverseJoinColumns={
     *     @ORM\JoinColumn(name="id_site", referencedColumnName="id_site")
     *   }
     * )
     */
    private $paiSites;

     /**
     * @var string
     *
     * @ORM\Column(name="at_username", type="string", length=150, nullable=true)
     */
    private $login;
    
    
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->setSites(new \Doctrine\Common\Collections\ArrayCollection());
        $this->setDataCadastro(new \DateTime('now'));
    }
    
    function getPaiSites() {
        return $this->paiSites;
    }

    function setPaiSites(\Doctrine\Common\Collections\Collection $paiSites) {
        $this->paiSites = $paiSites;
    }
    
    /**
     * 
     * @return \DateTime
     */
    public function getDataLegislacao()
    {
        return $this->dataLegislacao;
    }

    /**
     * 
     * @param \DateTime $dataLegislacao
     */
    public function setDataLegislacao(\DateTime $dataLegislacao)
    {
        $this->dataLegislacao = $dataLegislacao;
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
    public function getTitulo()
    {
        return $this->titulo;
    }

    /**
     * 
     * @return string
     */
    public function getArquivo()
    {
        return $this->arquivo;
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
     * @return CategoriaLegislacao
     */
    public function getCategoriaLegislacao()
    {
        return $this->categoriaLegislacao;
    }

    /**
     * 
     * @param string $titulo
     */
     public function getLogin()
    {
        return $this->login;
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
     * @param string $titulo
     */
    public function setTitulo($titulo)
    {
        $this->titulo = $titulo;
    }

    public function setLogin($login)
    {
        $this->login = $login;
    }
    
    /**
     * 
     * @param string $arquivo
     */
    public function setArquivo($arquivo)
    {
        $this->arquivo = $arquivo;
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
     * @param CategoriaLegislacao $categoriaLegislacao
     */
    public function setCategoriaLegislacao(CategoriaLegislacao $categoriaLegislacao)
    {
        $this->categoriaLegislacao = $categoriaLegislacao;
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
        return $this->getTitulo();
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
            "dataIncial" => $this->getDataInicial(),
            "dataFinal" => $this->getDataFinal(),
            "dataLegislacao" => $this->getDataLegislacao(),
            "titulo" => $this->getTitulo(),
            "descricao" => $this->getDescricao(),
            "url" => $this->getUrl(),
            "sites" => $this->getSites(),
            "categoriaLegislacao" => $this->getCategoriaLegislacao(),
            "propriedadeSede" => $this->getPropriedadeSede(),
            "publicado" => $this->getPublicado(),
            "arquivo" => $this->getArquivo()
        );
    }

}
