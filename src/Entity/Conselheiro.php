<?php

namespace Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Conselheiro
 *
 * @ORM\Table(name="tb_conselheiro")
 * @ORM\Entity(repositoryClass="Entity\Repository\ConselheiroRepository")
 */
class Conselheiro extends AbstractEntity implements EntityInterface
{

    /**
     * @var integer
     *
     * @ORM\Column(name="id_conselheiro", type="bigint", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\SequenceGenerator(sequenceName="tb_conselheiro_id_conselheiro_seq", allocationSize=1, initialValue=1)
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="no_conselheiro", type="string", length=50, nullable=false)
     */
    private $nome;

    /**
     * @var string
     *
     * @ORM\Column(name="no_instituicao", type="string", length=120, nullable=false)
     */
    private $instituicao;

    /**
     * @var integer
     *
     * @ORM\Column(name="st_propriedade_sede", type="integer", nullable=false)
     */
    private $propriedadeSede = '0';


    /**
     * @var string
     *
     * @ORM\Column(name="st_tipo", type="string", nullable=false)
     */
    private $tipo;

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
     * @ORM\Column(name="nu_ordem", type="bigint", nullable=false)
     */
    private $ordem;

    /**
     * @var integer
     *
     * @ORM\Column(name="st_publicado", type="integer", nullable=false)
     */
    private $publicado = '0';


    /**
     * Constructor
     */
    public function __construct()
    {
        $this->setDataCadastro(new \DateTime("now"));
    }

    /**
     * 
     * @return int
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
    public function getInstituicao()
    {
        return $this->instituicao;
    }

    /**
     * 
     * @return string
     */
    public function getTipo()
    {
        return $this->tipo;
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
    public function getOrdem()
    {
        return $this->ordem;
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
     * @param string $instituicao
     */
    public function setInstituicao($instituicao)
    {
        $this->instituicao = $instituicao;
    }

    /**
     * 
     * @param string $tipo
     */
    public function setTipo($tipo)
    {
        $this->tipo = $tipo;
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
     * @param \DateTime $dataCadastro
     */
    public function setDataCadastro(\DateTime $dataCadastro)
    {
        $this->dataCadastro = $dataCadastro;
    }

    /**
     * 
     * @param int $ordem
     */
    public function setOrdem($ordem)
    {
        $this->ordem = $ordem;
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
     * @param int $publicado
     */
    public function setPublicado($publicado)
    {
        $this->publicado = $publicado;
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
            "instituicao" => $this->getInstituicao(),
            "propriedadeSede" => $this->getPropriedadeSede(),
            "tipo" => $this->getTipo(),
            "dataInicial" => $this->getDataInicial(),
            "dataFinal" => $this->getDataFinal(),
            "dataCadastro" => $this->getDataCadastro(),
            "publicado" => $this->getPublicado(),
        );
    }

}
