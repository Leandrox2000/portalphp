<?php
namespace Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Compromissos da Agenda da Direção
 *
 * @ORM\Table(name="tb_compromisso")
 * @ORM\Entity(repositoryClass="Entity\Repository\CompromissoRepository")
 */
class Compromisso extends AbstractEntity implements EntityInterface
{

    /**
     * @var integer
     *
     * @ORM\Column(name="id_compromisso", type="bigint", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\SequenceGenerator(sequenceName="tb_compromisso_id_compromisso_seq", allocationSize=1, initialValue=1)
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
     * @ORM\Column(name="dt_compromisso_inicial", type="datetime", nullable=false)
     */
    private $compromissoInicial;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="dt_compromisso_final", type="datetime", nullable=true)
     */
    private $compromissoFinal;
    
    /**
     * @var string
     *
     * @ORM\Column(name="ds_local", type="string", length=150, nullable=false)
     */
    private $local;
    
    /**
     * @var string
     *
     * @ORM\Column(name="ds_participantes", type="text", nullable=true)
     */
    private $participantes;
    
    /**
     * @var string
     *
     * @ORM\Column(name="ds_observacoes", type="string", length=500, nullable=true)
     */
    private $observacoes;

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
     * @ORM\ManyToMany(targetEntity="Entity\AgendaDirecao", inversedBy="compromissos", cascade={"persist"})
     * @ORM\JoinTable(name="tb_compromisso_agenda_direcao",
     *   joinColumns={
     *     @ORM\JoinColumn(name="id_compromisso", referencedColumnName="id_compromisso")
     *   },
     *   inverseJoinColumns={
     *     @ORM\JoinColumn(name="id_agenda_direcao", referencedColumnName="id_agenda_direcao")
     *   }
     * )
     */
    private $agendasDirecao;
    
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->setAgendasDirecao(new \Doctrine\Common\Collections\ArrayCollection());
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
     * @return string
     */
    public function getTitulo()
    {
        return $this->titulo;
    }
    
    /**
     * @return \DateTime
     */
    public function getCompromissoInicial()
    {
        return $this->compromissoInicial;
    }

    /**
     * @return \DateTime
     */
    public function getCompromissoFinal()
    {
        return $this->compromissoFinal;
    }
    
    /**
     * @return string
     */
    public function getLocal()
    {
        return $this->local;
    }
    
    /**
     * @return string
     */
    public function getParticipantes()
    {
        return $this->participantes;
    }
    
    /**
     * @return string
     */
    public function getObservacoes()
    {
        return $this->observacoes;
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
    public function getAgendasDirecao()
    {
        return $this->agendasDirecao;
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
     * @param string $titulo
     */
    public function setTitulo($titulo)
    {
        $this->titulo = $titulo;
    }    
    
    /**
     * @param \DateTime $compromissoInicial
     */
    public function setCompromissoInicial(\DateTime $compromissoInicial)
    {
        $this->compromissoInicial = $compromissoInicial;
    }

    /**
     * @param \DateTime|null $compromissoFinal
     */
    public function setCompromissoFinal($compromissoFinal)
    {
        $this->compromissoFinal = $compromissoFinal;
    }

    /**
     * @param string $local
     */
    public function setLocal($local)
    {
        $this->local = $local;
    }

    /**
     * @param string $participantes
     */
    public function setParticipantes($participantes)
    {
        $this->participantes = $participantes;
    }

    /**
     * @param string $observacoes
     */
    public function setObservacoes($observacoes)
    {
        $this->observacoes = $observacoes;
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
     * @param \Doctrine\Common\Collections\Collection $agendasDirecao
     */
    public function setAgendasDirecao(\Doctrine\Common\Collections\Collection $agendasDirecao)
    {
        $this->agendasDirecao = $agendasDirecao;
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
            "local" => $this->getLocal(),
            "participantes" => $this->getParticipates(),
            "observacoes" => $this->getObservacoes(),
            "compromissoInicial" => $this->getCompromissoInicial(),
            "compromissoFinal" => $this->getCompromissoFinal(),
            "dataInicial" => $this->getDataInicial(),
            "dataFinal" => $this->getDataFinal(),
            "dataCadastro" => $this->getDataCadastro(),
            "publicado" => $this->getPublicado(),
            "agendasDirecao" => $this->getAgendasDirecao(),
        );
    }
}
    
