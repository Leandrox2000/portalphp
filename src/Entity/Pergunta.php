<?php

namespace Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Pergunta
 *
 * @ORM\Table(name="tb_pergunta", uniqueConstraints={@ORM\UniqueConstraint(name="UK_pergunta_id_pergunta", columns={"id_pergunta"})}, indexes={@ORM\Index(name="IDX_2AED648A1659562F", columns={"id_pergunta_categoria"})})
 * @ORM\Entity(repositoryClass="Entity\Repository\PerguntaRepository")
 */
class Pergunta extends AbstractEntity implements EntityInterface
{

    /**
     * @var integer
     *
     * @ORM\Column(name="id_pergunta", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="ds_pergunta", type="string", length=150, nullable=false)
     */
    private $pergunta;

    /**
     * @var string
     *
     * @ORM\Column(name="ds_resposta", type="text", length=16, nullable=false)
     */
    private $resposta;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="dt_cadastro", type="datetime", nullable=false)
     */
    private $dataCadastro;

    /**
     * @var integer
     *
     * @ORM\Column(name="st_publicado", type="smallint", nullable=false)
     */
    private $publicado = 0;
    
    
    /**
     * @var integer
     *
     * @ORM\Column(name="nu_ordem", type="integer", nullable=false)
     */
    private $ordem;
    

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="dt_inicial", type="datetime", nullable=false)
     */
    private $dataInicial;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="dt_final", type="datetime")
     */
    private $dataFinal;

    /**
     * @var PerguntaCategoria
     *
     * @ORM\ManyToOne(targetEntity="PerguntaCategoria")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_pergunta_categoria", referencedColumnName="id_pergunta_categoria")
     * })
     */
    private $categoria;

    /**
     * 
     * Seta a Data de Cadastro
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
    public function getPergunta()
    {
        return $this->pergunta;
    }

    /**
     * 
     * @return string
     */
    public function getResposta()
    {
        return $this->resposta;
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
     * @return int
     */
    public function getOrdem()
    {
        return $this->ordem ;
    }

    /**
     * 
     * @return \Entity\PerguntaCategoria
     */
    public function getCategoria()
    {
        return $this->categoria;
    }

    /**
     * 
     * @param string $pergunta
     */
    public function setPergunta($pergunta)
    {
        $this->pergunta = $pergunta;
    }

    /**
     * 
     * @param string $resposta
     */
    public function setResposta($resposta)
    {
        $this->resposta = $resposta;
    }

    /**
     * 
     * @param \DateTime $dataCadastro
     */
    public function setDataCadastro($dataCadastro)
    {
        if (!$dataCadastro instanceof \DateTime) {
            throw new \InvalidArgumentException("Data Cadastro só aceita dados do Tipo DateTime");
        }
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
     * @param int $ordem
     */
    public function setOrdem($ordem)
    {
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
     * @param \Entity\PerguntaCategoria $categoria
     */
    public function setCategoria($categoria)
    {
        if (!$categoria instanceof PerguntaCategoria) {
            throw new \InvalidArgumentException("Categoria deve ser do Tipo Objeto PerguntaCategoria");
        }
        $this->categoria = $categoria;
    }

    /**
     * Similiar à um __toString
     * Retorna o campo que for mais importante do objeto 
     *  
     * @return type
     */
    public function getLabel()
    {
        return $this->pergunta;
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
            'pergunta' => $this->getPergunta(),
            'resposta' => $this->getResposta(),
            'categoria' => $this->getCategoria(),
            'publicado' => $this->getPublicado(),
            'ordem' => $this->getOrdem(),
            'dataCadastro' => $this->getDataCadastro(),
            'dataInicial' => $this->getDataInicial(),
            'dataFinal' => $this->getDataFinal()
        );
    }

}
