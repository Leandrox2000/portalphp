<?php

namespace Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Bibliografia
 *
 * @ORM\Table(name="tb_bibliografia", indexes={@ORM\Index(name="IDX_FDFC443A3C2736E4", columns={"id_imagem"})})
 * @ORM\Entity(repositoryClass="Entity\Repository\BibliografiaRepository")
 */
class Bibliografia extends AbstractEntity implements EntityInterface
{

    /**
     * @var integer
     *
     * @ORM\Column(name="id_bibliografia", type="bigint", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\SequenceGenerator(sequenceName="tb_bibliografia_id_bibliografia_seq", allocationSize=1, initialValue=1)
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="no_titulo", type="string", length=50, nullable=false)
     */
    private $titulo;

    /**
     * @var string
     *
     * @ORM\Column(name="ds_conteudo", type="text", nullable=false)
     */
    private $conteudo;

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
     * @var \Entity\Imagem
     *
     * @ORM\ManyToOne(targetEntity="Entity\Imagem")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_imagem", referencedColumnName="id_imagem")
     * })
     */
    private $imagem;

    /**
     * Constructor
     * 
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
     * @return \Entity\Imagem
     */
    public function getImagem()
    {
        return $this->imagem;
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
     * @param type $publicado
     */
    public function setPublicado($publicado)
    {
        $this->publicado = $publicado;
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
     * @return string
     */
    public function getLabel()
    {
        return $this->getTitulo();
    }

    public function toArray()
    {
        return array(
          "id" => $this->getId(),
          "titulo" => $this->getTitulo(),
          "conteudo" => $this->getConteudo(),
          "dataInicial" => $this->getDataInicial(),
          "dataFinal" => $this->getDataFinal(),
          "dataCadastro" => $this->getDataCadastro(),
          "publicado" => $this->getPublicado(),
          "imagem" => $this->getImagem()
        );
    }

}
