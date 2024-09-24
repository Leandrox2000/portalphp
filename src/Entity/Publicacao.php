<?php

namespace Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Publicacao
 *
 * @ORM\Table(name="tb_publicacao", indexes={@ORM\Index(name="IDX_513A998FB0E6CC2", columns={"id_publicacao_categoria"})})
 * @ORM\Entity(repositoryClass="Entity\Repository\PublicacaoRepository")
 */
class Publicacao extends AbstractEntity implements EntityInterface {

    /**
     * @var integer
     *
     * @ORM\Column(name="id_publicacao", type="bigint", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\SequenceGenerator(sequenceName="tb_publicacao_id_publicacao_seq", allocationSize=1, initialValue=1)
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="no_titulo", type="string", length=255, nullable=false)
     */
    private $titulo;

    /**
     * @var string
     *
     * @ORM\Column(name="no_autor", type="string", length=255, nullable=false)
     */
    private $autor;

    /**
     * @var string
     *
     * @ORM\Column(name="ds_edicao", type="string", length=50, nullable=false)
     */
    private $edicao;

    /**
     * @var integer
     *
     * @ORM\Column(name="nu_pagina", type="integer", nullable=true)
     */
    private $paginas;
    
    
    /**
     * @var integer
     *
     * @ORM\Column(name="nu_ordem", type="integer", nullable=false)
     */
    private $ordem;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="dt_inicial", type="datetime", nullable=true)
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
    private $publicado = 0;

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
     * @ORM\Column(name="dt_cadastro", type="datetime", nullable=false)
     */
    private $dataCadastro;

    /**
     * @var PublicacaoCategoria
     *
     * @ORM\ManyToOne(targetEntity="PublicacaoCategoria", inversedBy="experiencias")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_publicacao_categoria", referencedColumnName="id_publicacao_categoria")
     * })
     */
    private $categoria;

    /**
     * @var Imagem
     *
     * @ORM\ManyToOne(targetEntity="Imagem", inversedBy="imagem")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_imagem", referencedColumnName="id_imagem")
     * })
     */
    private $imagem;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="dt_publicacao", type="datetime", nullable=true)
     */
    private $dataPublicacao;

    /**
     * @var string
     *
     * @ORM\Column(name="vl_preco", type="string", nullable=true)
     */
    private $preco;

    /**
     * @var integer
     *
     * @ORM\Column(name="st_tipo_publicacao", type="integer", nullable=false)
     */
    private $tipoPublicacao;

    /**
     * @var integer
     *
     * @ORM\Column(name="st_tipo_livraria", type="integer", nullable=false)
     */
    private $tipoLivraria;

    /**
     *  Construtor
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
    public function getArquivo()
    {
        return $this->arquivo;
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
     * @return string
     */
    public function getPreco()
    {
        return $this->preco;
    }

    /**
     *
     * @return integer
     */
    public function getTipoPublicacao()
    {
        return $this->tipoPublicacao;
    }

    /**
     *
     * @return integer
     */
    public function getTipoLivraria()
    {
        return $this->tipoLivraria;
    }

    /**
     *
     * @param string $preco
     */
    public function setPreco($preco)
    {
        $this->preco = $preco;
    }

    /**
     *
     * @param integer $tipoPublicacao
     */
    public function setTipoPublicacao($tipoPublicacao)
    {
        $this->tipoPublicacao = $tipoPublicacao;
    }

    /**
     *
     * @param integer $tipoLivraria
     */
    public function setTipoLivraria($tipoLivraria)
    {
        $this->tipoLivraria = $tipoLivraria;
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
    public function getAutor()
    {
        return $this->autor;
    }

    /**
     *
     * @return int
     */
    public function getEdicao()
    {
        return $this->edicao;
    }

    /**
     *
     * @return int
     */
    public function getPaginas()
    {
        return $this->paginas;
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
    public function getDataCadastro()
    {
        return $this->dataCadastro;
    }

    /**
     *
     * @return PublicacaoCategoria
     */
    public function getCategoria()
    {
        return $this->categoria;
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
     * @param string $autor
     */
    public function setAutor($autor)
    {
        $this->autor = $autor;
    }

    /**
     *
     * @param string $edicao
     */
    public function setEdicao($edicao)
    {
        $this->edicao = $edicao;
    }

    /**
     *
     * @param int $paginas
     * @throws \InvalidArgumentException
     */
    public function setPaginas($paginas)
    {
        if (!is_numeric($paginas)) {
            throw new \InvalidArgumentException("Número da páginas só aceita números");
        }
        $this->paginas = $paginas;
    }
    
    /**
     *
     * @param int $ordem
     */
    public function setOrdem($ordem){
        $this->ordem = $ordem;
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
     * @throws \InvalidArgumentException
     */
    public function setPublicado($publicado)
    {
        if ($publicado !== 1 && $publicado !== 0) {
            throw new \InvalidArgumentException("O Status de Publicado deve ser 0 ou 1");
        }
        $this->publicado = $publicado;
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
     * @param \DateTime $dataCadastro
     */
    public function setDataCadastro(\DateTime $dataCadastro)
    {
        $this->dataCadastro = $dataCadastro;
    }

    /**
     *
     * @param \Entity\PublicacaoCategoria $categoria
     */
    public function setCategoria(PublicacaoCategoria $categoria)
    {
        $this->categoria = $categoria;
    }

    /**
     *
     * @return Imagem
     */
    public function getImagem()
    {
        return $this->imagem;
    }

    /**
     *
     * @return \DateTime
     */
    public function getDataPublicacao()
    {
        return $this->dataPublicacao;
    }

    /**
     *
     * @param Imagem $imagem
     */
    public function setImagem(Imagem $imagem)
    {
        $this->imagem = $imagem;
    }

    /**
     *
     * @param \DateTime $dataPublicacao
     */
    public function setDataPublicacao(\DateTime $dataPublicacao)
    {
        $this->dataPublicacao = $dataPublicacao;
    }

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
            'id' => $this->getId(),
            'titulo' => $this->getTitulo(),
            'autor' => $this->getAutor(),
            'conteudo' => $this->getConteudo(),
            'categoria' => $this->getCategoria(),
            'edicao' => $this->getEdicao(),
            'paginas' => $this->getPaginas(),
            'ordem' => $this->getOrdem(),
            'dataInicial' => $this->getDataInicial(),
            'dataFinal' => $this->getDataFinal(),
            'dataCadastro' => $this->getDataCadastro(),
            'dataPublicacao' => $this->getDataPublicacao(),
            'imagem' => $this->getImagem(),
        );
    }

}
