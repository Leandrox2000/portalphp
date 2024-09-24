<?php
namespace Entity;

use Doctrine\ORM\Mapping as ORM;


/**
 * Funcionario
 *
 * @ORM\Table(name="tb_funcionario", indexes={@ORM\Index(name="IDX_1CAFFDD29A6508A3", columns={"id_unidade"}), @ORM\Index(name="IDX_1CAFFDD2AA5CAE95", columns={"id_vinculo"}), @ORM\Index(name="IDX_1CAFFDD2D56B1641", columns={"id_cargo"})})
 * @ORM\Entity(repositoryClass="Entity\Repository\FuncionarioRepository")
 */
class Funcionario extends AbstractEntity implements EntityInterface
{

    /**
     * @var integer
     *
     * @ORM\Column(name="id_funcionario", type="bigint", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\SequenceGenerator(sequenceName="tb_funcionario_id_funcionario_seq", allocationSize=1, initialValue=1)
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
     * @var string
     *
     * @ORM\Column(name="no_funcionario", type="string", length=50, nullable=false)
     */
    private $nome;

    /**
     * @var int
     *
     * @ORM\Column(name="st_diretoria", type="smallint", nullable=false)
     */
    private $diretoria = 0;


    /**
     * @var string
     *
     * @ORM\Column(name="no_imagem", type="string", length=50, nullable=true)
     */
    private $imagem;

    /**
     * @var string
     *
     * @ORM\Column(name="ds_email", type="string", length=100, nullable=false)
     */
    private $email;

    /**
     * @var string
     *
     * @ORM\Column(name="ds_curriculo", type="text", nullable=true)
     */
    private $curriculo;

    /**
     * @var integer
     *
     * @ORM\Column(name="st_exibir_portal", type="integer", nullable=false)
     */
    public $exibirPortal = '0';

    /**
     * @var integer
     *
     * @ORM\Column(name="st_exibir_intranet", type="integer", nullable=false)
     */
    private $exibirIntranet = '0';

    /**
     * @var integer
     *
     * @ORM\Column(name="st_publicado", type="integer", nullable=false)
     */
    private $publicado = '0';

    /**
     * @var Unidade
     *
     * @ORM\ManyToOne(targetEntity="Entity\Unidade")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_unidade", referencedColumnName="id_unidade")
     * })
     */
    private $unidade;

    /**
     * @var Vinculo
     *
     * @ORM\ManyToOne(targetEntity="Entity\Vinculo")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_vinculo", referencedColumnName="id_vinculo")
     * })
     */
    private $vinculo;

    /**
     * @var \Entity\Cargo
     *
     * @ORM\ManyToOne(targetEntity="Entity\Cargo")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_cargo", referencedColumnName="id_cargo")
     * })
     */
    private $cargo;

    /**
     * @var \Entity\Diretoria
     *
     * @ORM\OneToOne(targetEntity="Entity\Diretoria", mappedBy="funcionario", cascade={"remove"})
     */
    private $diretor;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->setDataCadastro(new \DateTime('now'));
    }

    public function getDiretor() {
        return $this->diretor;
    }

    public function setDiretor(\Entity\Diretoria $diretores) {
        $this->diretor = $diretores;
    }

    /**
     *
     * @return integer
     */
    public function getExibirPortal()
    {
        return $this->exibirPortal;
    }

    /**
     *
     * @return integer
     */
    public function getExibirIntranet()
    {
        return $this->exibirIntranet;
    }

    /**
     *
     * @param integer $exibirPortal
     */
    public function setExibirPortal($exibirPortal)
    {
        $this->exibirPortal = $exibirPortal;
    }

    /**
     *
     * @param integer $exibirIntranet
     */
    public function setExibirIntranet($exibirIntranet)
    {
        $this->exibirIntranet = $exibirIntranet;
    }

    /**
     *
     * @param int $diretoria
     */
    public function setDiretoria($diretoria)
    {
        $this->diretoria = $diretoria;
    }

    /**
     *
     * @return int
     */
    public function getDiretoria()
    {
        return $this->diretoria;
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
    public function getImagem()
    {
        return $this->imagem;
    }

    /**
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     *
     * @return string
     */
    public function getCurriculo()
    {
        return $this->curriculo;
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
     * @return Unidade
     */
    public function getUnidade()
    {
        return $this->unidade;
    }

    /**
     *
     * @return Vinculo
     */
    public function getVinculo()
    {
        return $this->vinculo;
    }

    /**
     *
     * @return Cargo
     */
    public function getCargo()
    {
        return $this->cargo;
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
     * @param string $nome
     */
    public function setNome($nome)
    {
        $this->nome = $nome;
    }

    /**
     *
     * @param string $imagem
     */
    public function setImagem($imagem)
    {
        $this->imagem = $imagem;
    }

    /**
     *
     * @param string $email
     */
    public function setEmail($email)
    {
        $this->email = $email;
    }

    /**
     *
     * @param string $curriculo
     */
    public function setCurriculo($curriculo)
    {
        $this->curriculo = $curriculo;
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
     * @param Unidade $unidade
     */
    public function setUnidade(Unidade $unidade)
    {
        $this->unidade = $unidade;
    }

    /**
     *
     * @param Vinculo $vinculo
     */
    public function setVinculo(Vinculo $vinculo)
    {
        $this->vinculo = $vinculo;
    }

    /**
     *
     * @param Cargo $cargo
     */
    public function setCargo(Cargo $cargo = null)
    {
        $this->cargo = $cargo;
    }

    /**
     *
     * @return string
     */
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
            "id"                => $this->getId(),
            "dataCadastro"      => $this->getDataCadastro(),
            "dataInicial"       => $this->getDataInicial(),
            "dataFinal"         => $this->getDataFinal(),
            "nome"              => $this->getNome(),
            "imagem"            => $this->getImagem(),
            "curriculo"         => $this->getCurriculo(),
            "vinculo"           => $this->getVinculo(),
            "unidade"           => $this->getUnidade(),
            "cargo"             => $this->getCargo(),
            "publicado"         => $this->getPublicado(),
            "email"             => $this->getEmail(),
            'exibirPortal'      => $this->getExibirPortal(),
            'exibirIntranet'    => $this->getExibirIntranet(),
            'diretoria'         => $this->getDiretoria(),
        );
    }

}
