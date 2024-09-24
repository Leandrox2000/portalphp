<?php

namespace Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Edital
 *
 * @ORM\Table(name="tb_edital", uniqueConstraints={@ORM\UniqueConstraint(name="uk_edital_id_edital", columns={"id_edital"})}, indexes={@ORM\Index(name="IDX_15AAF81BCEBB1C18", columns={"id_edital_categoria"}), @ORM\Index(name="IDX_15AAF81B8EEC5970", columns={"id_edital_status"})})
 * @ORM\Entity(repositoryClass="Entity\Repository\EditalRepository")
 */
class Edital extends AbstractEntity implements EntityInterface
{

    /**
     * @var integer
     *
     * @ORM\Column(name="id_edital", type="bigint", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\SequenceGenerator(sequenceName="tb_edital_id_edital_seq", allocationSize=1, initialValue=1)
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="no_edital", type="string", length=255, nullable=false)
     */
    private $nome;

    /**
     * @var string
     *
     * @ORM\Column(name="ds_conteudo", type="text", nullable=false)
     */
    private $conteudo;

    /**
     * @var string
     *
     * @ORM\Column(name="ds_arquivo", type="text", nullable=true)
     */
    private $arquivo;

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
     * @var \Entity\EditalCategoria
     *
     * @ORM\ManyToOne(targetEntity="Entity\EditalCategoria")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_edital_categoria", referencedColumnName="id_edital_categoria")
     * })
     */
    private $categoria;
       
    /**
     * @var \Entity\EditalStatus
     *
     * @ORM\ManyToOne(targetEntity="Entity\EditalStatus")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_edital_status", referencedColumnName="id_status_categoria")
     * })
     */
    private $status;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\ManyToMany(targetEntity="Entity\Site", inversedBy="editais", cascade={"persist"})
     * @ORM\JoinTable(name="tb_edital_site",
     *   joinColumns={
     *     @ORM\JoinColumn(name="id_edital", referencedColumnName="id_edital")
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
     * @ORM\ManyToMany(targetEntity="Entity\Site", inversedBy="paiEditais", cascade={"persist"})
     * @ORM\JoinTable(name="tb_pai_edital_site",
     *   joinColumns={
     *     @ORM\JoinColumn(name="id_edital", referencedColumnName="id_edital")
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
        $this->setDataCadstro(new \DateTime("now"));
    }

    /**
     * 
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }
    
    function getPaiSites() {
        return $this->paiSites;
    }

    function setPaiSites(\Doctrine\Common\Collections\Collection $paiSites) {
        $this->paiSites = $paiSites;
    }

    /**
     * 
     * @param string $titulo
     */
     public function getLogin()
    {
        return $this->login;
    }
       public function setLogin($login)
    {
        $this->login = $login;
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
    public function getConteudo()
    {
        return $this->conteudo;
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
     * @return \Entity\EditalCategoria
     */
    public function getCategoria()
    {
        return $this->categoria;
    }

    /**
     * 
     * @return \Entity\EditalStatus
     */
    public function getStatus()
    {
        return $this->status;
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
     * @param string $nome
     */
    public function setNome($nome)
    {
        $this->nome = $nome;
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
     * @param string $arquivo
     */
    public function setArquivo($arquivo)
    {
        $this->arquivo = $arquivo;
    }

    /**
     * 
     * @param \DateTime $datainicial
     */
    public function setDataInicial(\DateTime $datainicial)
    {
        $this->dataInicial = $datainicial;
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
     * @param \DateTime $dataCAdstro
     */
    public function setDataCadstro(\DateTime $dataCadastro)
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
     * @param \Entity\EditalCategoria $categoria
     */
    public function setCategoria(\Entity\EditalCategoria $categoria)
    {
        $this->categoria = $categoria;
    }

    /**
     * 
     * @param \Entity\EditalStatus $status
     */
    public function setStatus(\Entity\EditalStatus $status)
    {
        $this->status = $status;
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
     * @return int
     */
    public function getPropriedadeSede()
    {
        return $this->propriedadeSede;
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
            "conteudo" => $this->getConteudo(),
            "arquivo" => $this->getArquivo(),
            "dataInicial" => $this->getDataInicial(),
            "dataFinal" => $this->getDataFinal(),
            "dataCadastro" => $this->getDataCadastro(),
            "publicado" => $this->getPublicado(),
            "propriedadeSede" => $this->getPropriedadeSede(),
            "categoria" => $this->getCategoria(),
            "status" => $this->getStatus(),
            "sites" => $this->getSites()
        );
    }

}
