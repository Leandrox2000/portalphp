<?php

namespace Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Agenda
 *
 * @ORM\Table(name="tb_agenda")
 * @ORM\Entity(repositoryClass="Entity\Repository\AgendaRepository")
 */
class Agenda extends AbstractEntity implements EntityInterface
{

    /**
     * @var integer
     *
     * @ORM\Column(name="id_agenda", type="bigint", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\SequenceGenerator(sequenceName="tb_agenda_id_agenda_seq", allocationSize=1, initialValue=1)
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="no_titulo", type="string", length=100, nullable=false)
     */
    private $titulo;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="dt_periodo_inicial", type="datetime", nullable=false)
     */
    private $periodoInicial;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="dt_periodo_final", type="datetime", nullable=true)
     */
    private $periodoFinal;

    /**
     * @var string
     *
     * @ORM\Column(name="ds_ingresso", type="string", length=100, nullable=false)
     */
    private $ingresso;

    /**
     * @var string
     *
     * @ORM\Column(name="no_local", type="string", length=100, nullable=false)
     */
    private $local;

    /**
     * @var string
     *
     * @ORM\Column(name="sg_uf", type="string", nullable=false)
     */
    private $uf;

    /**
     * @var string
     *
     * @ORM\Column(name="ds_cep", type="string", nullable=true)
     */
    private $cep;

    /**
     * @var string
     *
     * @ORM\Column(name="ds_endereco", type="text", nullable=false)
     */
    private $endereco;

    /**
     * @var string
     *
     * @ORM\Column(name="no_cidade", type="string", length=100, nullable=false)
     */
    private $cidade;

    /**
     * @var string
     *
     * @ORM\Column(name="no_bairro", type="string", length=50, nullable=true)
     */
    private $bairro;

    /**
     * @var string
     *
     * @ORM\Column(name="ds_numero", type="string", length=10, nullable=true)
     */
    private $numero;

    /**
     * @var string
     *
     * @ORM\Column(name="no_complemento", type="string", length=50, nullable=true)
     */
    private $complemento;

    /**
     * @var string
     *
     * @ORM\Column(name="ds_telefone", type="string", length=20, nullable=true)
     */
    private $telefone;

    /**
     * @var string
     *
     * @ORM\Column(name="ds_celular", type="string", length=20, nullable=true)
     */
    private $celular;

    /**
     * @var string
     *
     * @ORM\Column(name="ds_email", type="string", length=100, nullable=true)
     */
    private $email;

    /**
     * @var string
     *
     * @ORM\Column(name="ds_site", type="string", length=10, nullable=true)
     */
    private $site;

    /**
     * @var string
     *
     * @ORM\Column(name="at_username", type="string", length=150, nullable=true)
     */
    private $login;

    /**
     * @var string
     *
     * @ORM\Column(name="ds_evento", type="text", nullable=true)
     */
    private $descricao;

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
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\ManyToMany(targetEntity="Entity\Site", inversedBy="agendas", cascade={"persist"})
     * @ORM\JoinTable(name="tb_agenda_site",
     *   joinColumns={
     *     @ORM\JoinColumn(name="id_agenda", referencedColumnName="id_agenda")
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
     * @ORM\ManyToMany(targetEntity="Entity\Site", inversedBy="paiAgendas", cascade={"persist"})
     * @ORM\JoinTable(name="tb_pai_agenda_site",
     *   joinColumns={
     *     @ORM\JoinColumn(name="id_agenda", referencedColumnName="id_agenda")
     *   },
     *   inverseJoinColumns={
     *     @ORM\JoinColumn(name="id_site", referencedColumnName="id_site")
     *   }
     * )
     */
    private $paiSites;


    /**
     * Constructor
     */
    public function __construct()
    {
        $this->setSites(new \Doctrine\Common\Collections\ArrayCollection());
        $this->setDataCadastro(new \DateTime("now"));
    }

    public function getId()
    {
        return $this->id;
    }
    
    public function getTitulo()
    {
        return $this->titulo;
    }
    
    function getPaiSites() {
        return $this->paiSites;
    }

    function setPaiSites(\Doctrine\Common\Collections\Collection $paiSites) {
        $this->paiSites = $paiSites;
    }
    
    public function getPeriodoInicial()
    {
        return $this->periodoInicial;
    }

    public function getPeriodoFinal()
    {
        return $this->periodoFinal;
    }

    public function getLocal()
    {
        return $this->local;
    }

    public function getUf()
    {
        return $this->uf;
    }

    public function getCep()
    {
        return $this->cep;
    }

    public function getEndereco()
    {
        return $this->endereco;
    }

    public function getCidade()
    {
        return $this->cidade;
    }

    public function getBairro()
    {
        return $this->bairro;
    }

    public function getNumero()
    {
        return $this->numero;
    }

    public function getComplemento()
    {
        return $this->complemento;
    }

    public function getTelefone()
    {
        return $this->telefone;
    }

    public function getCelular()
    {
        return $this->celular;
    }

    public function getEmail()
    {
        return $this->email;
    }

    public function getSite()
    {
        return $this->site;
    }

    public function getDescricao()
    {
        return $this->descricao;
    }

    public function getDataCadastro()
    {
        return $this->dataCadastro;
    }

    public function getDataInicial()
    {
        return $this->dataInicial;
    }

    public function getDataFinal()
    {
        return $this->dataFinal;
    }

    public function getPublicado()
    {
        return $this->publicado;
    }

    public function getLogin()
    {
        return $this->login;
    }

    public function getPropriedadeSede()
    {
        return $this->propriedadeSede;
    }

    public function getSites()
    {
        return $this->sites;
    }

    public function setTitulo($titulo)
    {
        $this->titulo = $titulo;
    }

    public function setPeriodoInicial(\DateTime $periodoInicial)
    {
        $this->periodoInicial = $periodoInicial;
    }

    public function setPeriodoFinal(\DateTime $periodoFinal)
    {
        $this->periodoFinal = $periodoFinal;
    }

    public function setLocal($local)
    {
        $this->local = $local;
    }

    public function setUf($uf)
    {
        $this->uf = $uf;
    }

    public function setCep($cep)
    {
        $this->cep = $cep;
    }

    public function setEndereco($endereco)
    {
        $this->endereco = $endereco;
    }

    public function setCidade($cidade)
    {
        $this->cidade = $cidade;
    }

    public function setBairro($bairro)
    {
        $this->bairro = $bairro;
    }

    public function setNumero($numero)
    {
        $this->numero = $numero;
    }

    public function setComplemento($complemento)
    {
        $this->complemento = $complemento;
    }

    public function setTelefone($telefone)
    {
        $this->telefone = $telefone;
    }

    public function setCelular($celular)
    {
        $this->celular = $celular;
    }

    public function setEmail($email)
    {
        $this->email = $email;
    }

    public function setSite($site)
    {
        $this->site = $site;
    }

    public function setDescricao($descricao)
    {
        $this->descricao = $descricao;
    }

    public function setDataCadastro(\DateTime $dataCadastro)
    {
        $this->dataCadastro = $dataCadastro;
    }

    public function setDataInicial(\DateTime $dataInicial)
    {
        $this->dataInicial = $dataInicial;
    }


    /**
     *
     * @param \DateTime or null $dataFinal
     */
    // public function setDataFinal(\DateTime $dataFinal)
    // Alterada a necessidade do tipo de dado ser DateTime 
    // A variável $dataFinal também deve poder ser setada como NULL
    public function setDataFinal($dataFinal)
    {
        $this->dataFinal = $dataFinal;
    }

    public function setPublicado($publicado)
    {
        $this->publicado = $publicado;
    }

    public function setLogin($login)
    {
        $this->login = $login;
    }

    public function setPropriedadeSede($propriedadeSede)
    {
        $this->propriedadeSede = $propriedadeSede;
    }

    public function setSites(\Doctrine\Common\Collections\Collection $sites)
    {
        $this->sites = $sites;
    }

    public function getLabel()
    {
        return $this->getTitulo();
    }

    public function getIngresso()
    {
        return $this->ingresso;
    }

    public function setIngresso($ingresso)
    {
        $this->ingresso = $ingresso;
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
            "descricao" => $this->getDescricao(),
            "periodoInicial" => $this->getPeriodoInicial(),
            "periodoFinal" => $this->getPeriodoFinal(),
            "ingresso" => $this->getIngresso(),
            "local" => $this->getLocal(),
            "uf" => $this->getUf(),
            "cep" => $this->getCep(),
            "endereco" => $this->getEndereco(),
            "cidade" => $this->getCidade(),
            "bairro" => $this->getBairro(),
            "numero" => $this->getNumero(),
            "complemento" => $this->getComplemento(),
            "telefone" => $this->getTelefone(),
            "celular" => $this->getCelular(),
            "email" => $this->getEmail(),
            "site" => $this->getSite(),
            "dataInicial" => $this->getDataInicial(),
            "dataFinal" => $this->getDataFinal(),
            "dataCadastro" => $this->getDataCadastro(),
            "login" => $this->getLogin(),
            "publicado" => $this->getPublicado(),
            "propriedadeSede" => $this->getPropriedadeSede(),
            "sites" => $this->getSites()
        );
    }
}
    
