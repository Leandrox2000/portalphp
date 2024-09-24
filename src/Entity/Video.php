<?php
namespace Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Video
 *
 * @ORM\Table(name="tb_video")
 * @ORM\Entity(repositoryClass="Entity\Repository\VideoRepository")
 */
class Video extends AbstractEntity implements EntityInterface
{

    /**
     * @var integer
     *
     * @ORM\Column(name="id_video", type="bigint", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\SequenceGenerator(sequenceName="tb_video_id_video_seq", allocationSize=1, initialValue=1)
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
     * @ORM\Column(name="no_video", type="string", length=150, nullable=false)
     */
    private $nome;

    /**
     * @var string
     *
     * @ORM\Column(name="ds_link", type="string", length=100, nullable=false)
     */
    private $link;

    /**
     * @var integer
     *
     * @ORM\Column(name="st_publicado", type="integer", nullable=false)
     */
    private $publicado = 0;

    /**
     * @var string
     *
     * @ORM\Column(name="ds_resumo", type="string", nullable=true)
     */
    private $resumo;

    /**
     * @var string
     *
     * @ORM\Column(name="no_youtube", type="string", length=150, nullable=true)
     */
    private $nomeYoutube;

    /**
     * @var string
     *
     * @ORM\Column(name="no_autor", type="string", length=150,nullable=true)
     */
    private $autor;

    /**
     * @var integer
     *
     * @ORM\Column(name="st_propriedade_sede", type="integer", nullable=false)
     */
    private $propriedadeSede = 0;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\ManyToMany(targetEntity="Site", inversedBy="videos")
     * @ORM\JoinTable(name="tb_video_site",
     *   joinColumns={
     *     @ORM\JoinColumn(name="id_video", referencedColumnName="id_video")
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
     * @ORM\ManyToMany(targetEntity="Entity\Site", inversedBy="paiVideos", cascade={"persist"})
     * @ORM\JoinTable(name="tb_pai_video_site",
     *   joinColumns={
     *     @ORM\JoinColumn(name="id_video", referencedColumnName="id_video")
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
     * @var \Doctrine\Common\Collections\Collection
     * 
     * @ORM\OneToMany(targetEntity="VideoRelacionado", mappedBy="video")
     */
    private $relacionados;

    
    
    /**
     * @var Collection
     * 
     * @ORM\OneToMany(targetEntity="VideoSite", mappedBy="video")
     */
    private $videosSite;

    function getVideosSite() {
        return $this->videosSite;
    }
    
    public function setVideossSite($videosSite)
    {
        $this->videosSite = $videosSite;
    }
    
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->setSites(new \Doctrine\Common\Collections\ArrayCollection());
        $this->setRelacionados(new \Doctrine\Common\Collections\ArrayCollection());
        $this->setDataCadastro(new \DateTime('now'));
    }
    
    function getLogin() {
        return $this->login;
    }

    function setLogin($login) {
        $this->login = $login;
    }
    
    function getPaiSites() {
        return $this->paiSites;
    }

    function setPaiSites(\Doctrine\Common\Collections\Collection $paiSites) {
        $this->paiSites = $paiSites;
    }

    /**
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function getRelacionados()
    {
        return $this->relacionados;
    }

    public function setRelacionados(\Doctrine\Common\Collections\Collection $relacionados)
    {
        $this->relacionados = $relacionados;
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
    public function getLink()
    {
        return $this->link;
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
     * @param string $nome
     */
    public function setNome($nome)
    {
        $this->nome = $nome;
    }

    /**
     *
     * @param string $link
     */
    public function setLink($link)
    {
        $this->link = str_replace("http://", "", $link);
    }

    /**
     *
     * @param integer $publicado
     */
    public function setPublicado($publicado)
    {
        if ($publicado !== 1 && $publicado !== 0) {
            throw new \InvalidArgumentException("A publicação deve ser 0 ou 1");
        }
        $this->publicado = $publicado;
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
     * @return integer
     */
    public function getPropriedadeSede()
    {
        return $this->propriedadeSede;
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
     * @return String
     */
    public function getResumo()
    {
        return $this->resumo;
    }


    /**
     *
     * @return String
     */
    public function getNomeYoutube()
    {
        return $this->nomeYoutube;
    }


    /**
     *
     * @return String
     */
    public function getAutor()
    {
        return $this->autor;
    }


    /**
     *
     * @param String $resumo
     */
    public function setResumo($resumo)
    {
        $this->resumo = $resumo;
    }

    /**
     *
     * @param String $nomeYoutube
     */
    public function setNomeYoutube($nomeYoutube)
    {
        $this->nomeYoutube = $nomeYoutube;
    }

    /**
     *
     * @param String $autor
     */
    public function setAutor($autor)
    {
        $this->autor = $autor;
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
            "dataCadastro" => $this->getDataCadastro(),
            "dataInicial" => $this->getDataInicial(),
            "dataFinal" => $this->getDataFinal(),
            "nome" => $this->getNome(),
            "link" => $this->getLink(),
            "sites" => $this->getSites(),
            "publicado" => $this->getPublicado(),
            "propriedadeSede" => $this->getPropriedadeSede(),
            "autor" => $this->getAutor(),
            "resumo" => $this->getResumo(),
            "nomeYotube" => $this->getNomeYoutube(),
            "relacionados" => $this->getRelacionados(),
        );
    }

}
