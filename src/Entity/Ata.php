<?php
namespace Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Ata
 *
 * @ORM\Table(name="tb_ata", uniqueConstraints={@ORM\UniqueConstraint(name="uk_ata_id_ata", columns={"id_ata"})})
 * @ORM\Entity(repositoryClass="Entity\Repository\AtaRepository")
 */
class Ata extends AbstractEntity implements EntityInterface
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id_ata", type="bigint", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\SequenceGenerator(sequenceName="tb_ata_id_ata_seq", allocationSize=1, initialValue=1)
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="no_ata", type="string", length=50, nullable=false)
     */
    private $nome;

    /**
     * @var string
     *
     * @ORM\Column(name="no_arquivo", type="string", length=255, nullable=false)
     */
    private $arquivo;

    /**
     * @var string
     *
     * @ORM\Column(name="ds_ata", type="text", nullable=false)
     */
    private $descricao;
    
    /**
     * @var \DateTime
     *
     * @ORM\Column(name="dt_reuniao", type="datetime", nullable=true)
     */
    private $dataReuniao;

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
     * Constructor
     */
    public function __construct()
    {
        $this->setDataCadastro(new \DateTime("now"));
        $this->setDataInicial(new \DateTime("now"));
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
    public function getArquivo()
    {
        return $this->arquivo;
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
     * @return int
     */
    public function getPublicado()
    {
        return $this->publicado;
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
     * @param string $arquivo
     */
    public function setArquivo($arquivo)
    {
        $this->arquivo = $arquivo;
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
     * @param int $publicado
     */
    public function setPublicado($publicado)
    {
        $this->publicado = $publicado;
    }
    
    /**
     * 
     * @return string
     */
    public function getLabel()
    {
        return $this->nome;
    }

    public function getDataReuniao()
    {
        return $this->dataReuniao;
    }

    /**
     * 
     * @param \DateTime $dataReuniao
     */
    public function setDataReuniao(\DateTime $dataReuniao)
    {
        $this->dataReuniao = $dataReuniao;
    }
    
    /**
     * 
     * @return array
     */
    public function toArray(){
        return array(
            "id" => $this->getId(),
            "dataCadastro" => $this->getDataCadastro(),
            "dataInicial" => $this->getDataInicial(),
            "dataFinal" => $this->getDataFinal(),
            "dataReuniao" => $this->getDataReuniao(),
            "nome" => $this->getNome(),
            "descricao" => $this->getDescricao(),
            "arquivo" => $this->getArquivo(),
            "publicado" => $this->getPublicado()
        );
    }
    
}
