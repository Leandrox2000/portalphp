<?php
namespace Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * BackgroundHome
 *
 * @ORM\Table(name="tb_background_home")
 * @ORM\Entity(repositoryClass="Entity\Repository\BackgroundHomeRepository")
 */
class BackgroundHome extends AbstractEntity implements EntityInterface
{

    /**
     * @var integer
     *
     * @ORM\Column(name="id_background_home", type="bigint", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\SequenceGenerator(sequenceName="tb_background_home_id_background_home_seq", allocationSize=1, initialValue=1)
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
     * @ORM\Column(name="dt_cadastro", type="datetime", nullable=true)
     */
    private $dataCadastro;

    /**
     * @var integer
     *
     * @ORM\Column(name="st_publicado", type="integer", nullable=false)
     */
    private $publicado = '0';

    /**
     * @var string
     *
     * @ORM\Column(name="no_nome", type="string", length=100, nullable=false)
     */
    private $nome;

    /**
     * @var \Entity\Imagem
     *
     * @ORM\ManyToOne(targetEntity="Entity\Imagem", fetch="EAGER")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_imagem", referencedColumnName="id_imagem")
     * })
     */
    private $imagem;

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
     * @return integer
     */
    public function getPublicado()
    {
        return $this->publicado;
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
     * @return \Entity\Imagem
     */
    public function getImagem()
    {
        return $this->imagem;
    }

    /**
     * 
     * @param integer $id
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
    // Alterada a necessidade do tipo de dado ser DateTime 
    // A variável $dataFinal também deve poder ser setada como NULL
    // public function setDataFinal(\DateTime $dataFinal)
    public function setDataFinal($dataFinal)
    {
        $this->dataFinal = $dataFinal;
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
     * @param string $nome
     */
    public function setNome($nome)
    {
        $this->nome = $nome;
    }

    /**
     * 
     * @param \Entity\Imagem $imagem
     */
    public function setImagem(\Entity\Imagem $imagem)
    {
        $this->imagem = $imagem;
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
     * @param \DateTime $dataCadastro
     */
    public function setDataCadastro(\DateTime $dataCadastro)
    {
        $this->dataCadastro = $dataCadastro;
    }
    
    /**
     * 
     * @return string
     */
    public function getLabel()
    {
        return $this->getNome();
    }
    
    public function toArray()
    {
        return array(
            'id' => $this->getId(),
            'nome' => $this->getNome(),
            'imagem' => $this->getImagem(),
            'dataCadastro' => $this->getDataCadastro(),
            'dataInicial' => $this->getDataInicial(),
            'dataFinal' => $this->getDataFinal(),
            'publicado' => $this->getPublicado(),
        );
    }

}
