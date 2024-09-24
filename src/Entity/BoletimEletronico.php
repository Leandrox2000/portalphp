<?php

namespace Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * BoletimEletronico
 *
 * @ORM\Table(name="tb_boletim_eletronico", uniqueConstraints={@ORM\UniqueConstraint(name="UK_boletim_eletronico_id_boletim_eletronico", columns={"id_boletim_eletronico"})})
 * @ORM\Entity(repositoryClass="Entity\Repository\BoletimEletronicoRepository")
 */
class BoletimEletronico extends AbstractEntity implements EntityInterface
{

    /**
     * @var integer
     *
     * @ORM\Column(name="id_boletim_eletronico", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
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
     * @ORM\Column(name="dt_periodo_inicial", type="datetime", nullable=false)
     */
    private $periodoInicial;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="dt_periodo_final", type="datetime", nullable=false)
     */
    private $periodoFinal;

    /**
     * @var integer
     *
     * @ORM\Column(name="nu_numero", type="integer", nullable=false)
     */
    private $numero;

    /**
     * @var integer
     *
     * @ORM\Column(name="nu_ano", type="integer", nullable=false)
     */
    private $ano;

    /**
     * @var string
     *
     * @ORM\Column(name="no_arquivo", type="string", length=50, nullable=false)
     */
    private $arquivo;

    /**
     * @var integer
     *
     * @ORM\Column(name="st_publicado", type="smallint", nullable=false)
     */
    private $publicado = 0;

    /**
     * Seta a data de cadastro
     */
    public function __construct()
    {
        $this->setDataCadastro(new \DateTime('now'));
    }

    /**
     * 
     * @return id
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * 
     * @return string
     */
    public function getDataCadastro()
    {
        return $this->dataCadastro;
    }

    /**
     * 
     * @return string
     */
    public function getDataInicial()
    {
        return $this->dataInicial;
    }

    /**
     * 
     * @return string
     */
    public function getDataFinal()
    {
        return $this->dataFinal;
    }

    /**
     * 
     * @return string
     */
    public function getPeriodoInicial()
    {
        return $this->periodoInicial;
    }

    /**
     * 
     * @return string
     */
    public function getPeriodoFinal()
    {
        return $this->periodoFinal;
    }

    /**
     * 
     * @return int
     */
    public function getNumero()
    {
        return $this->numero;
    }

    /**
     * 
     * @return int
     */
    public function getAno()
    {
        return $this->ano;
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
     * @return int
     */
    public function getPublicado()
    {
        return $this->publicado;
    }

    /**
     * 
     * @param int $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * 
     * @param \DateTime $dataCadastro
     */
    public function setDataCadastro($dataCadastro)
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
    public function setDataFinal(\DateTime $dataFinal = null)
    {
        $this->dataFinal = $dataFinal;
    }

    /**
     * 
     * @param \DateTime $periodoInicial
     */
    public function setPeriodoInicial($periodoInicial)
    {
        $this->periodoInicial = $periodoInicial;
    }

    /**
     * 
     * @param \DateTime $periodoFinal
     */
    public function setPeriodoFinal($periodoFinal)
    {
        $this->periodoFinal = $periodoFinal;
    }

    /**
     * 
     * @param int $numero
     */
    public function setNumero($numero)
    {
        $this->numero = $numero;
    }

    /**
     * 
     * @param int $ano
     */
    public function setAno($ano)
    {
        $this->ano = $ano;
    }

    /**
     * 
     * @param String $arquivo
     */
    public function setArquivo($arquivo)
    {
        $this->arquivo = $arquivo;
    }

    /**
     * 
     * @param int $publicado
     */
    public function setPublicado($publicado)
    {
        if ($publicado !== 1 && $publicado !== 0) {
            throw new \InvalidArgumentException("O período final deve ser 0 ou 1");
        }
        $this->publicado = $publicado;
    }

    /**
     * 
     * @return String
     */
    public function getLabel()
    {
        if ($this->getNumero() >= 10)
            return $this->getNumero() . "/" . $this->getAno();
        else
            return "0" . $this->getNumero() . "/" . $this->getAno();
    }

    /**
     * Converte  o Objeto para Array
     * 
     * @return array
     */
    public function toArray()
    {
        return array(
            'id' => $this->getId(),
            'data_cadastro' => $this->getDataCadastro(),
            'periodo_inicial' => $this->getPeriodoInicial(),
            'periodo_final' => $this->getPeriodoFinal(),
            'ano' => $this->getAno(),
            'numero' => $this->getNumero(),
            'arquivo' => $this->getArquivo()
        );
    }

}
