<?php

namespace Entity;

use Doctrine\ORM\Mapping as ORM;
use Helpers\DatetimeFormat;

/**
 * Noticia
 *
 * @ORM\Table(name="tb_noticia")
 * @ORM\Entity(repositoryClass="Entity\Repository\NoticiaRepository")
 */
class Noticia extends AbstractEntity implements EntityInterface
{ 

    /** 
     * @var integer
     *
     * @ORM\Column(name="id_noticia", type="bigint", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\SequenceGenerator(sequenceName="tb_noticia_id_noticia_seq", allocationSize=1, initialValue=1)
     */
    private $id;
    
    /**
     * @var string
     *
     * @ORM\Column(name="at_username", type="string", length=150, nullable=true)
     */
    private $login;

    /**
     * @var string
     *
     * @ORM\Column(name="no_titulo", type="string", length=100, nullable=false)
     */
    private $titulo;

    /**
     * @var string
     *
     * @ORM\Column(name="ds_conteudo", type="text", nullable=false)
     */
    private $conteudo;

    /**
     * @var string
     *
     * @ORM\Column(name="ds_palavras_chave", type="text", length=200, nullable=true)
     */
    private $palavrasChave;

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
     * @ORM\Column(name="dt_cadastro", type="datetime", nullable=false)
     */
    private $dataCadastro;

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
     * @var \Entity\Imagem
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
     * @ORM\ManyToMany(targetEntity="Entity\Site", inversedBy="noticias", cascade={"persist"})
     * @ORM\JoinTable(name="tb_noticia_site",
     *   joinColumns={
     *     @ORM\JoinColumn(name="id_noticia", referencedColumnName="id_noticia")
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
     * @ORM\ManyToMany(targetEntity="Entity\Site", inversedBy="paiNoticias", cascade={"persist"})
     * @ORM\JoinTable(name="tb_pai_noticia_site",
     *   joinColumns={
     *     @ORM\JoinColumn(name="id_noticia", referencedColumnName="id_noticia")
     *   },
     *   inverseJoinColumns={
     *     @ORM\JoinColumn(name="id_site", referencedColumnName="id_site")
     *   }
     * )
     */
    private $paiSites;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\OneToMany(targetEntity="NoticiaGaleria", mappedBy="noticia", fetch="EXTRA_LAZY")
     */
    private $galerias;
    
    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\OneToMany(targetEntity="ComentarioNoticia", mappedBy="noticia", fetch="EXTRA_LAZY")
     */
    private $comentarios;

    /**
     * @var string
     *
     * @ORM\Column(name="ds_slug", type="text", length=100, nullable=true)
     */
    private $slug;

    /**
     * @var string
     *
     * @ORM\Column(name="ds_flag_noticia", type="text", length=100, nullable=true)
     */
    private $flagNoticia;


    /**
     * Constructor
     */
    public function __construct()
    {
        $this->setSites(new \Doctrine\Common\Collections\ArrayCollection());
        $this->setGalerias(new \Doctrine\Common\Collections\ArrayCollection());
        $this->setDataCadastro(new \DateTime("now"));
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
     * 
     * @param integer $id
     */
    function setId($id) {
        $this->id = $id;
    }

        /**
     *
     * @return string
     */
    public function getPalavrasChave()
    {
        return $this->palavrasChave;
    }

    /**
     *
     * @param string $palavrasChave
     */
    public function setPalavrasChave($palavrasChave)
    {
        $this->palavrasChave = $palavrasChave;
    }

    public function getId()
    {
        return $this->id;
    }

    /**
     *
     * @return \Entity\Imagem
     */
    public function getImagem()
    {
        return $this->imagem;
    }

    /**
     *
     * @param \Entity\Imagem $imagem
     */
    public function setImagem(\Entity\Imagem $imagem = null)
    {
        $this->imagem = $imagem;
    }

    /**
     *
     * @return \Doctrine\Common\Collections\Collection $comentarios
     */
    public function getComentarios()
    {
        return $this->comentarios;
    }

    /**
     *
     * @param \Doctrine\Common\Collections\Collection $comentarios
     */
    public function setComentarios(\Doctrine\Common\Collections\Collection $comentarios)
    {
        $this->comentarios = $comentarios;
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
    public function getConteudo()
    {
        return $this->conteudo;
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
     * @return \DateTime
     */
    public function getDataCadastro()
    {
        return $this->dataCadastro;
    }

    /**
     *
     * @return int
     */
    public function getPublicado()
    {
        return $this->publicado;
    }

    /**
     *
     * @return int
     */
    public function getPropriedadeSede()
    {
        return $this->propriedadeSede;
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
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getGalerias()
    {
        return $this->galerias;
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
     * @param string $conteudo
     */
    public function setConteudo($conteudo)
    {
        $this->conteudo = $conteudo;
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
    // Removida a obrigatoriedade do tipo da variável ser DateTime devido á necessidade de setar datas NULAS
    // Variável deve poder receber DATAS no padrão DateTime ou NULL
    //
    // public function setDataFinal(\DateTime $dataFinal)
    public function setDataFinal($dataFinal)
    {
        $this->dataFinal = $dataFinal;
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
     * @param int $publicado
     */
    public function setPublicado($publicado)
    {
        $this->publicado = $publicado;
    }

    /**
     *
     * @param int $propriedadeSede
     */
    public function setPropriedadeSede($propriedadeSede)
    {
        $this->propriedadeSede = $propriedadeSede;
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
     * @param \Doctrine\Common\Collections\Collection $galerias
     */
    public function setGalerias(\Doctrine\Common\Collections\Collection $galerias)
    {
        $this->galerias = $galerias;
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
     * @return string
     */
    public function getSlug()
    {
        return $this->slug;
    }

    /**
     * 
     * @param type string
     */
    public function setSlug($slug)
    {
        $this->slug = $slug;
    }

    /**
     * 
     * @return string
     */
    public function getFlagNoticia()
    {
        return $this->flagNoticia;
    }

    /**
     * 
     * @param type string
     */
    public function setFlagNoticia($flagNoticia)
    {
        $this->flagNoticia = $flagNoticia;
    }
    
    /**
     *
     * @return array
     */
    public function toArray()
    {
        return array(
            "id" => $this->getId(),
            "titulo" => $this->getTitulo(),
            'palavrasChave' => $this->getPalavrasChave(),
            'imagem' => $this->getImagem(),
            "conteudo" => $this->getConteudo(),
            "dataCadastro" => $this->getDataCadastro(),
            "dataInicial" => $this->getDataInicial(),
            "dataFinal" => $this->getDataFinal(),
            "publicado" => $this->getPublicado(),
            "propriedadeSede" => $this->getPropriedadeSede(),
            "sites" => $this->getSites(),
            "galerias" => $this->getGalerias(),
            "comentarios" => $this->getComentarios(),
            "slug" => $this->getSlug(),
            "flagNoticia" => $this->getFlagNoticia()
        );
    }

    /**
     *
     * @return string
     */
 
     public function getDataCadastroFormatado()
    {
        $hora = $this->getDataCadastro()->format("H i");
        $hora = explode(" ",$hora);
        $hora = $hora[0]."h".$hora[1];
        
        $data = $this->getDataCadastro()->format("Y-m-d");
        $datetime = new DatetimeFormat();
        $data = $datetime->formatDataExtenso($data);
        
        return utf8_encode($data.", ás ".$hora);
    }
    
    public function getDataPublicacaoFormatado() {
         
        $hora = $this->getDataInicial()->format("H i");
        $hora = explode(" ",$hora);
        $hora = $hora[0]."h".$hora[1];
        
        $data = $this->getDataInicial()->format("Y-m-d");
        $datetime = new DatetimeFormat();
        $data = $datetime->formatDataExtenso($data);
         
        return "publicada em ".$data.", às ".$hora;
    }
}
