<?php
namespace Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Site
 *
 * @ORM\Table(name="tb_site")
 * @ORM\Entity(repositoryClass="Entity\Repository\SiteRepository")
 */
class Site extends AbstractEntity implements EntityInterface
{

    /**
     * @var integer
     *
     * @ORM\Column(name="id_site", type="bigint", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\SequenceGenerator(sequenceName="tb_site_id_site_seq", allocationSize=1, initialValue=1)
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
     * @ORM\Column(name="no_site", type="string", length=100, nullable=false)
     */
    private $nome;

    /**
     * @var string
     *
     * @ORM\Column(name="ds_sigla", type="string", length=100, nullable=false)
     */
    private $sigla;

    /**
     * @var string
     *
     * @ORM\Column(name="ds_titulo", type="string", length=200, nullable=false)
     */
    private $titulo;

    /**
     * @var string
     *
     * @ORM\Column(name="ds_descricao", type="string", length=300, nullable=true)
     */
    private $descricao;
    
     /**
     * @var string
     *
     * @ORM\Column(name="ds_facebook", type="string", length=300, nullable=true)
     */
    private $facebook;
      /**
     * @var string
     *
     * @ORM\Column(name="ds_twitter", type="string", length=300, nullable=true)
     */
    private $twitter;
     /**
     * @var string
     *
     * @ORM\Column(name="ds_youtube", type="string", length=300, nullable=true)
     */
    private $youtube;
      /**
     * @var string
     *
     * @ORM\Column(name="ds_flickr", type="string", length=300, nullable=true)
     */
    private $flickr;

    

    /**
     * @var integer
     *
     * @ORM\Column(name="st_sede", type="integer", nullable=false)
     */
    private $sede = 0;

     /**
     * @var integer
     *
     * @ORM\Column(name="st_publicado", type="integer", nullable=false)
     */
    private $publicado = 0;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\ManyToMany(targetEntity="Video", mappedBy="sites")
     */
    private $videos;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\ManyToMany(targetEntity="Edital", mappedBy="sites", fetch="EXTRA_LAZY")
     */
    private $editais;
    
    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\ManyToMany(targetEntity="Edital", mappedBy="paiSites", fetch="EXTRA_LAZY")
     */
    private $paiEditais;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\ManyToMany(targetEntity="Galeria", mappedBy="sites", fetch="EXTRA_LAZY")
     */
    private $galerias;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\ManyToMany(targetEntity="PaginaEstatica", mappedBy="sites", fetch="EXTRA_LAZY")
     */
    private $paginasEstaticas;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\ManyToMany(targetEntity="Noticia", mappedBy="sites", fetch="EXTRA_LAZY")
     */
    private $noticias;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\ManyToMany(targetEntity="Agenda", mappedBy="sites", fetch="EXTRA_LAZY")
     */
    private $agendas;
    
    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\ManyToMany(targetEntity="AgendaDirecao", mappedBy="sites", fetch="EXTRA_LAZY")
     */
    private $agendasDirecao;
    
    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\ManyToMany(targetEntity="Agenda", mappedBy="paiSites", fetch="EXTRA_LAZY")
     */
    private $paiAgendas;
    
    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\ManyToMany(targetEntity="AgendaDirecao", mappedBy="paiSites", fetch="EXTRA_LAZY")
     */
    private $paiAgendasDirecao;
    
    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\ManyToMany(targetEntity="Galeria", mappedBy="paiSites", fetch="EXTRA_LAZY")
     */
    private $paiGalerias;
    
    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\ManyToMany(targetEntity="Legislacao", mappedBy="paiSites", fetch="EXTRA_LAZY")
     */
    private $paiLegislacao;
    
    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\ManyToMany(targetEntity="Noticia", mappedBy="paiSites", fetch="EXTRA_LAZY")
     */
    private $paiNoticias;
    
    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\ManyToMany(targetEntity="PaginaEstatica", mappedBy="paiSites", fetch="EXTRA_LAZY")
     */
    private $paiPaginas;
    
    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\ManyToMany(targetEntity="Video", mappedBy="paiSites", fetch="EXTRA_LAZY")
     */
    private $paiVideos;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\ManyToMany(targetEntity="Legislacao", mappedBy="sites", fetch="EXTRA_LAZY")
     */
    private $legislacoes;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\ManyToMany(targetEntity="SliderHome", mappedBy="sites", fetch="EXTRA_LAZY")
     */
    private $slidersHome;


    
    /**
     * @var \Doctrine\Common\Collections\Collection
     * 
     * @ORM\OneToMany(targetEntity="FuncionalidadeSite", mappedBy="site", cascade={"remove"})
     */
    private $funcionalidadesSite;


    
     /**
     * @var \Doctrine\Common\Collections\Collection
     * 
     * @ORM\OneToMany(targetEntity="GaleriaSite", mappedBy="site", cascade={"remove"})
     */
    private $galeriasSite;
    
    
    /**
     * @var \Doctrine\Common\Collections\Collection
     * 
     * @ORM\OneToMany(targetEntity="VideoSite", mappedBy="site", cascade={"remove"})
     */
    private $videosSite;
    
    /**
     * @var \Doctrine\Common\Collections\Collection
     * 
     * @ORM\OneToMany(targetEntity="AgendaDirecaoSite", mappedBy="site", cascade={"remove"})
     */
    private $agendaDirecaoSite;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->setDataCadastro(new \DateTime('now'));
        $this->setPaginasEstaticas(new \Doctrine\Common\Collections\ArrayCollection());
        $this->setVideos(new \Doctrine\Common\Collections\ArrayCollection());
        $this->setEditais(new \Doctrine\Common\Collections\ArrayCollection());
        $this->setNoticias(new \Doctrine\Common\Collections\ArrayCollection());
        $this->setAgendas(new \Doctrine\Common\Collections\ArrayCollection());
        $this->setAgendasDirecao(new \Doctrine\Common\Collections\ArrayCollection());
        $this->setFuncionalidadesSite(new \Doctrine\Common\Collections\ArrayCollection());
        $this->setGaleriasSite(new \Doctrine\Common\Collections\ArrayCollection());
        $this->setVideosSite(new \Doctrine\Common\Collections\ArrayCollection());
        $this->setAgendaDirecaoSite(new \Doctrine\Common\Collections\ArrayCollection());
        $this->setSlidersHome(new \Doctrine\Common\Collections\ArrayCollection());
        $this->setGalerias(new \Doctrine\Common\Collections\ArrayCollection());
        $this->setLegislacoes(new \Doctrine\Common\Collections\ArrayCollection());
    }
    
    function getPaiVideos() {
        return $this->paiVideos;
    }

    function setPaiVideos(\Doctrine\Common\Collections\Collection $paiVideos) {
        $this->paiVideos = $paiVideos;
    }
    
    function getPaiPaginas() {
        return $this->paiPaginas;
    }

    function setPaiPaginas(\Doctrine\Common\Collections\Collection $paiPaginas) {
        $this->paiPaginas = $paiPaginas;
    }
    
    function getPaiNoticias() {
        return $this->paiNoticias;
    }

    function setPaiNoticias(\Doctrine\Common\Collections\Collection $paiNoticias) {
        $this->paiNoticias = $paiNoticias;
    }

    function getFuncionalidadesSite() 
    {
        return $this->funcionalidadesSite;
    }

    function setFuncionalidadesSite(\Doctrine\Common\Collections\Collection $funcionalidadeSite) 
    {
        $this->funcionalidadesSite = $funcionalidadeSite;
    }
    
    function getPaiLegislacao() {
        return $this->paiLegislacao;
    }

    function setPaiLegislacao(\Doctrine\Common\Collections\Collection $paiLegislacao) {
        $this->paiLegislacao = $paiLegislacao;
    }
    
    function getGaleriasSite() 
    {
        return $this->galeriasSite;
    }

    /**
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getPaiAgendas()
    {
        return $this->paiAgendas;
    }
    
    /**
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getPaiAgendasDirecao()
    {
        return $this->paiAgendasDirecao;
    }

    /**
     * @param \Doctrine\Common\Collections\Collection $paiAgendas
     */
    public function setPaiAgendas($paiAgendas)
    {
        $this->paiAgendas = $paiAgendas;
    }
    
    function getPaiGalerias() {
        return $this->paiGalerias;
    }

    function setPaiGalerias(\Doctrine\Common\Collections\Collection $paiGalerias) {
        $this->paiGalerias = $paiGalerias;
    }

    function getPaiEditais() {
        return $this->paiEditais;
    }

    function setPaiEditais(\Doctrine\Common\Collections\Collection $paiEditais) {
        $this->paiEditais = $paiEditais;
    }

    function setGaleriasSite(\Doctrine\Common\Collections\Collection $galeriasSite) 
    {
        $this->galeriasSite = $galeriasSite; 
    }

    /**
     * @param \Doctrine\Common\Collections\Collection $paiAgendasDirecao
     */
    public function setPaiAgendasDirecao($paiAgendasDirecao)
    {
        $this->paiAgendasDirecao = $paiAgendasDirecao;
    }    
    
    function getVideosSite() 
    {
        return $this->videosSite;
    }

    function setVideosSite(\Doctrine\Common\Collections\Collection $videosSite) 
    {
        $this->videosSite = $videosSite; 
    }
    
    function getAgendaDirecaoSite() 
    {
        return $this->agendaDirecaoSite;
    }

    function setAgendaDirecaoSite(\Doctrine\Common\Collections\Collection $agendaDirecaoSite) 
    {
        $this->agendaDirecaoSite = $agendaDirecaoSite; 
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
     * @param string $titulo
     */
    public function setTitulo($titulo)
    {
        $this->titulo = $titulo;
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
    public function getSigla()
    {
        return $this->sigla;
    }

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
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getPaginasEstaticas()
    {
        return $this->paginasEstaticas;
    }

    /**
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getVideos()
    {
        return $this->videos;
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
     * @param string $sigla
     */
    public function setSigla($sigla)
    {
        $this->sigla = $sigla;
    }

    /**
     *
     * @param int $sede
     */
    public function setSede($sede)
    {
        $this->sede = $sede;
    }

    /**
     *
     * @param \Doctrine\Common\Collections\Collection $paginasEstaticas
     */
    public function setPaginasEstaticas(\Doctrine\Common\Collections\Collection $paginasEstaticas)
    {
        $this->paginasEstaticas = $paginasEstaticas;
    }

    /**
     *
     * @param \Doctrine\Common\Collections\Collection $videos
     */
    public function setVideos(\Doctrine\Common\Collections\Collection $videos)
    {
        $this->videos = $videos;
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
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getEditais()
    {
        return $this->editais;
    }

    /**
     *
     * @param \Doctrine\Common\Collections\Collection $editais
     */
    public function setEditais(\Doctrine\Common\Collections\Collection $editais)
    {
        $this->editais = $editais;
    }

    /**
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getGalerias()
    {
        return $this->galerias;
    }

    /**
     *
     * @param \Doctrine\Common\Collections\Collection $galerias
     */
    public function setGalerias(\Doctrine\Common\Collections\Collection $galerias)
    {
        $this->galerias = $galerias;
    }

    /**
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getNoticias()
    {
        return $this->noticias;
    }

    /**
     *
     * @param \Doctrine\Common\Collections\Collection $noticias
     */
    public function setNoticias(\Doctrine\Common\Collections\Collection $noticias)
    {
        $this->noticias = $noticias;
    }

    /**
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getAgendas()
    {
        return $this->agendas;
    }
    
    /**
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getAgendasDirecao()
    {
        return $this->agendasDirecao;
    }

    /**
     *
     * @param \Doctrine\Common\Collections\Collection $agendas
     */
    public function setAgendas(\Doctrine\Common\Collections\Collection $agendas)
    {
        $this->agendas = $agendas;
    }
    
    /**
     *
     * @param \Doctrine\Common\Collections\Collection $agendasDirecao
     */
    public function setAgendasDirecao(\Doctrine\Common\Collections\Collection $agendasDirecao)
    {
        $this->agendasDirecao = $agendasDirecao;
    }

    /**
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getLegislacoes()
    {
        return $this->legislacoes;
    }

    /**
     *
     * @param \Doctrine\Common\Collections\Collection $legislacoes
     */
    public function setLegislacoes(\Doctrine\Common\Collections\Collection $legislacoes)
    {
        $this->legislacoes = $legislacoes;
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
     * @param integer $publicado
     */
    public function setPublicado($publicado)
    {
        $this->publicado = $publicado;
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
     * @param \DateTime $dataCadastro
     */
    public function setDataCadastro(\DateTime $dataCadastro)
    {
        $this->dataCadastro = $dataCadastro;
    }

    /**
     *
     * @return \DataInicial
     */
    public function getDataInicial()
    {
        return $this->dataInicial;
    }

    /**
     *
     * @return \DataInicial
     */
    public function getDataFinal()
    {
        return $this->dataFinal;
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
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getSlidersHome()
    {
        return $this->slidersHome;
    }

    /**
     *
     * @param \Doctrine\Common\Collections\Collection $slidersHome
     */
    public function setSlidersHome(\Doctrine\Common\Collections\Collection $slidersHome)
    {
        $this->slidersHome = $slidersHome;
    }

    function getFacebook() {
        return $this->facebook;
    }

    function getTwitter() {
        return $this->twitter;
    }

    function getYoutube() {
        return $this->youtube;
    }

    function setFacebook($facebook) {
        $this->facebook = $facebook;
    }

    function setTwitter($twitter) {
        $this->twitter = $twitter;
    }

    function setYoutube($youtube) {
        $this->youtube = $youtube;
    }

    function getFlickr() {
        return $this->flickr;
    }

    function setFlickr($flickr) {
        $this->flickr = $flickr;
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
            "sigla" => $this->getSigla(),
            "sede" => $this->getSede(),
            "paginaEstatica" => $this->getPaginasEstaticas(),
            "videos" => $this->getVideos(),
            "galerias" => $this->getGalerias(),
            "noticias" => $this->getNoticias(),
            "agendas" => $this->getAgendas(),
            "agendasDirecao" => $this->getAgendasDirecao(),
            "editais" => $this->getEditais(),
            "legislacoes" => $this->getLegislacoes(),
            "slidersHome" => $this->getSlidersHome(),
            "publicado" => $this->getPublicado(),
            "titulo" => $this->getTitulo(),
            "descricao" => $this->getDescricao(),
            "funcionalidadesSite" => $this->getFuncionalidadesSite()
        );
    }

}
