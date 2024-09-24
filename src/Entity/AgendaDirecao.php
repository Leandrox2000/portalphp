<?php
namespace Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Agenda da Direção
 *
 * @ORM\Table(name="tb_agenda_direcao")
 * @ORM\Entity(repositoryClass="Entity\Repository\AgendaDirecaoRepository")
 */
class AgendaDirecao extends AbstractEntity implements EntityInterface
{

    /**
     * @var integer
     *
     * @ORM\Column(name="id_agenda_direcao", type="bigint", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\SequenceGenerator(sequenceName="tb_agenda_direcao_id_agenda_direcao_seq", allocationSize=1, initialValue=1)
     */
    private $id;

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
     * @ORM\Column(name="no_titulo", type="string", length=120, nullable=false)
     */
    private $titulo;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="dt_cadastro", type="datetime", nullable=false)
     */
    private $dataCadastro;

    /**
     * @var string
     *
     * @ORM\Column(name="at_username", type="string", length=150, nullable=true)
     */
    private $login;

    /**
     * @var integer
     *
     * @ORM\Column(name="st_publicado", type="integer", nullable=false)
     */
    private $publicado = '0';

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\ManyToMany(targetEntity="Entity\Site", inversedBy="agendasDirecao", cascade={"persist"})
     * @ORM\JoinTable(name="tb_agenda_direcao_site",
     *   joinColumns={
     *     @ORM\JoinColumn(name="id_agenda_direcao", referencedColumnName="id_agenda_direcao")
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
     * @ORM\ManyToMany(targetEntity="Entity\Site", inversedBy="paiAgendasDirecao", cascade={"persist"})
     * @ORM\JoinTable(name="tb_pai_agenda_direcao_site",
     *   joinColumns={
     *     @ORM\JoinColumn(name="id_agenda_direcao", referencedColumnName="id_agenda_direcao")
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
     * @ORM\ManyToMany(targetEntity="Compromisso", mappedBy="agendasDirecao", fetch="EXTRA_LAZY")
     */
    private $compromissos;    

    /**
     * @var \Doctrine\Common\Collections\Collection
     * 
     * @ORM\OneToMany(targetEntity="Entity\AgendaDirecaoResponsavel", mappedBy="agendaDirecao")
     */
    private $responsaveis;
    
    /**
     * @var \Doctrine\Common\Collections\Collection
     * 
     * @ORM\OneToMany(targetEntity="AgendaDirecaoSite", mappedBy="agendaDirecao")
     */
    private $agendaDirecaoSite;    

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->setSites(new \Doctrine\Common\Collections\ArrayCollection());
        $this->setResponsaveis(new \Doctrine\Common\Collections\ArrayCollection());
        $this->setCompromissos(new \Doctrine\Common\Collections\ArrayCollection());
        $this->setAgendaDirecaoSite(new \Doctrine\Common\Collections\ArrayCollection());
        $this->setDataCadastro(new \DateTime("now"));
    }

    /**
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }
    
    /**
     * @return string
     */
    public function getTitulo()
    {
        return $this->titulo;
    }
    
    /**
     * @return \DateTime
     */
    public function getDataInicial()
    {
        return $this->dataInicial;
    }

    /**
     * @return \DateTime
     */
    public function getDataFinal()
    {
        return $this->dataFinal;
    }

    /**
     * @return \DateTime
     */
    public function getDataCadastro()
    {
        return $this->dataCadastro;
    }

    /**
     * @return integer
     */
    public function getPublicado()
    {
        return $this->publicado;
    }

    /**
     * @return string
     */
    public function getLogin()
    {
        return $this->login;
    }

    /**
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getSites()
    {
        return $this->sites;
    }
    
    /**
     * @return \Doctrine\Common\Collections\Collection
     */
    function getPaiSites() 
    {
        return $this->paiSites;
    }
    
    /**
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getResponsaveis()
    {
        return $this->responsaveis;
    }
    
    /**
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getCompromissos()
    {
        return $this->compromissos;
    }
    
    /**
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getAgendaDirecaoSite()
    {
        return $this->agendaDirecaoSite;
    }

    /**
     * @param string $titulo
     */
    public function setTitulo($titulo)
    {
        $this->titulo = $titulo;
    }

    /**
     * @param \DateTime $dataInicial
     */
    public function setDataInicial(\DateTime $dataInicial)
    {
        $this->dataInicial = $dataInicial;
    }

    /**
     * @param \DateTime|null $dataFinal
     */
    public function setDataFinal($dataFinal)
    {
        $this->dataFinal = $dataFinal;
    }

    /**
     * @param \DateTime $dataCadastro
     */
    public function setDataCadastro(\DateTime $dataCadastro)
    {
        $this->dataCadastro = $dataCadastro;
    }

    /**
     * @param integer $publicado
     */
    public function setPublicado($publicado)
    {
        $this->publicado = $publicado;
    }
    
    /**
     * @param string $login
     */
    public function setLogin($login)
    {
        $this->login = $login;
    }

    /**
     * @param \Doctrine\Common\Collections\Collection $sites
     */
    public function setSites(\Doctrine\Common\Collections\Collection $sites)
    {
        $this->sites = $sites;
    }

    /**
     * @param \Doctrine\Common\Collections\Collection $paiSites
     */
    function setPaiSites(\Doctrine\Common\Collections\Collection $paiSites) {
        $this->paiSites = $paiSites;
    }
    
    /**
     *
     * @param \Doctrine\Common\Collections\Collection $compromissos
     */
    public function setCompromissos(\Doctrine\Common\Collections\Collection $compromissos)
    {
        $this->compromissos = $compromissos;
    }
        
    /**
     * @param \Doctrine\Common\Collections\Collection $responsaveis
     */
    public function setResponsaveis(\Doctrine\Common\Collections\Collection $responsaveis)
    {
        $this->responsaveis = $responsaveis;
    }
        
    /**
     * @param \Doctrine\Common\Collections\Collection $agendaDirecaoSite
     */
    public function setAgendaDirecaoSite(\Doctrine\Common\Collections\Collection $agendaDirecaoSite)
    {
        $this->agendaDirecaoSite = $agendaDirecaoSite;
    }

    /**
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
            "titulo" => $this->getTitulo(),
            "dataInicial" => $this->getDataInicial(),
            "dataFinal" => $this->getDataFinal(),
            "dataCadastro" => $this->getDataCadastro(),
            "publicado" => $this->getPublicado(),
            "sites" => $this->getSites(),
            "responsaveis" => $this->getResponsaveis(),
            "agendaDirecaoSite" => $this->getAgendaDirecaoSite(),
        );
    }
}
    
