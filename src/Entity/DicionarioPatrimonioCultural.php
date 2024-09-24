<?php

namespace Entity;

use Doctrine\ORM\Mapping as ORM;


/**
 * DicionarioPatrimonioCultural
 *
 * @ORM\Table(name="tb_dicionario_patrimonio", uniqueConstraints={@ORM\UniqueConstraint(name="uk_dicionario_patrimonio_id_dicionario_patrimonio", columns={"id_dicionario_patrimonio"})}, indexes={@ORM\Index(name="IDX_A09FF842E04455E", columns={"id_categoria_dpc"})})
 * @ORM\Entity(repositoryClass="Entity\Repository\DicionarioPatrimonioCulturalRepository")
 */
class DicionarioPatrimonioCultural extends AbstractEntity implements EntityInterface
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id_dicionario_patrimonio", type="bigint", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\SequenceGenerator(sequenceName="tb_dicionario_patrimonio_id_dicionario_patrimonio_seq", allocationSize=1, initialValue=1)
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
     * @var \DateTime
     *
     * @ORM\Column(name="dt_cadastro", type="datetime", nullable=false)
     */
    private $dataCadastro;

    /**
     * @var string
     *
     * @ORM\Column(name="no_verbete_autor", type="string", length=120, nullable=false)
     */
    private $verbete;

    /**
     * @var string
     *
     * @ORM\Column(name="no_titulo", type="string", length=120, nullable=false)
     */
    private $titulo;

    /**
     * @var string
     *
     * @ORM\Column(name="ds_descricao", type="text", nullable=false)
     */
    private $descricao;

    /**
     * @var string
     *
     * @ORM\Column(name="no_colaborador", type="string", length=120, nullable=true)
     */
    private $colaborador;

    /**
     * @var string
     *
     * @ORM\Column(name="no_funcao", type="string", length=100, nullable=true)
     */
    private $funcao;

    /**
     * @var string
     *
     * @ORM\Column(name="ds_link", type="string", length=500, nullable=true)
     */
    private $link;

    /**
     * @var string
     *
     * @ORM\Column(name="ds_ficha_tecnica", type="text", nullable=true)
     */
    private $fichaTecnica;

    /**
     * @var integer
     *
     * @ORM\Column(name="st_publicado", type="integer", nullable=false)
     */
    private $publicado = 0;

    /**
     * @var \Entity\CategoriaDicionario
     *
     * @ORM\ManyToOne(targetEntity="Entity\CategoriaDicionario")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_categoria_dpc", referencedColumnName="id_categoria_dpc")
     * })
     */
    private $categoria;
    
    public function __construct()
    {
        $this->setDataCadastro(new \DateTime('now'));
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
     * @return string
     */
    public function getVerbete()
    {
        return $this->verbete;
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
    public function getDescricao()
    {
        return $this->descricao;
    }

    /**
     * 
     * @return string
     */
    public function getColaborador()
    {
        return $this->colaborador;
    }

    /**
     * 
     * @return string
     */
    public function getFuncao()
    {
        return $this->funcao;
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
     * @return string
     */
    public function getFichaTecnica()
    {
        return $this->fichaTecnica;
    }

    /**
     * 
     * @return \DateTime
     */
    public function getPublicado()
    {
        return $this->publicado;
    }

    /**
     * 
     * @return \Entity\CategoriaDicionario
     */
    public function getCategoria()
    {
        return $this->categoria;
    }

    /**
     * 
     * @param string $id
     */
    public function setId($id)
    {
        $this->id = $id;
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
     * @param string $verbete
     */
    public function setVerbete($verbete)
    {
        $this->verbete = $verbete;
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
     * @param string $descricao
     */
    public function setDescricao($descricao)
    {
        $this->descricao = $descricao;
    }

    /**
     * 
     * @param string $colaborador
     */
    public function setColaborador($colaborador)
    {
        $this->colaborador = $colaborador;
    }

    /**
     * 
     * @param string $funcao
     */
    public function setFuncao($funcao)
    {
        $this->funcao = $funcao;
    }

    /**
     * 
     * @param string $link
     */
    public function setLink($link)
    {
        $this->link = $link;
    }

    /**
     * 
     * @param string $fichaTecnica
     */
    public function setFichaTecnica($fichaTecnica)
    {
        $this->fichaTecnica = $fichaTecnica;
    }

    /**
     * 
     * @param integer $publicado
     */
    public function setPublicado($publicado)
    {
        $this->publicado = $publicado;
    }

    /**
     * 
     * @param \Entity\CategoriaDicionario $categoria
     */
    public function setCategoria(\Entity\CategoriaDicionario $categoria)
    {
        $this->categoria = $categoria;
    }
    
    public function getLabel()
    {
        return $this->getTitulo();
    }

    public function toArray()
    {
        return array(
            'id' => $this->getId(),
            'categoria' => $this->getCategoria(),
            'colaborador' => $this->getColaborador(),
            'dataCadastro' => $this->getDataCadastro(),
            'dataInicial' => $this->getDataInicial(),
            'dataFinal' => $this->getDataFinal(),
            'descricao' => $this->getDescricao(),
            'fichaTecnica' => $this->getFichaTecnica(),
            'funcao' => $this->getFuncao(),
            'link' => $this->getLink(),
            'publicado' => $this->getPublicado(),
            'titulo' => $this->getTitulo(),
            'verbete' => $this->getVerbete(),
        );
    }
}
