<?php

namespace Entity;

use Doctrine\ORM\Mapping as ORM;


/**
 * Biblioteca
 *
 * @ORM\Table(name="tb_biblioteca", uniqueConstraints={@ORM\UniqueConstraint(name="uk_biblioteca_id_biblioteca", columns={"id_biblioteca"})}, indexes={@ORM\Index(name="IDX_4BA100A43C2736E4", columns={"id_imagem"})})
 * @ORM\Entity(repositoryClass="Entity\Repository\BibliotecaRepository")
 */
class Biblioteca extends AbstractEntity implements EntityInterface
{

    /**
     * @var integer
     *
     * @ORM\Column(name="id_biblioteca", type="bigint", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\SequenceGenerator(sequenceName="tb_biblioteca_id_biblioteca_seq", allocationSize=1, initialValue=1)
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
     * @ORM\Column(name="no_biblioteca", type="string", length=100, nullable=false)
     */
    private $nome;

    /**
     * @var string
     *
     * @ORM\Column(name="no_responsavel", type="string", length=120, nullable=false)
     */
    private $responsavel;

    /**
     * @var string
     *
     * @ORM\Column(name="no_cidade", type="string", length=100, nullable=false)
     */
    private $cidade;

    /**
     * @var string
     *
     * @ORM\Column(name="sg_uf", type="string", length=2, nullable=false)
     */
    private $uf;

    /**
     * @var string
     *
     * @ORM\Column(name="ds_cep", type="string", nullable=false)
     */
    private $cep;

    /**
     * @var string
     *
     * @ORM\Column(name="ds_endereco", type="text", nullable=false)
     */
    private $endereco;

    /**
     * @var string
     *
     * @ORM\Column(name="ds_numero", type="string", length=10, nullable=false)
     */
    private $numero;

    /**
     * @var string
     *
     * @ORM\Column(name="no_complemento", type="string", length=50, nullable=true)
     */
    private $complemento;

    /**
     * @var string
     *
     * @ORM\Column(name="no_bairro", type="string", length=50, nullable=false)
     */
    private $bairro;

    /**
     * @var string
     *
     * @ORM\Column(name="ds_telefone", type="string", length=20, nullable=false)
     */
    private $telefone;

    /**
     * @var string
     *
     * @ORM\Column(name="ds_celular", type="string", length=20, nullable=false)
     */
    private $celular;

    /**
     * @var string
     *
     * @ORM\Column(name="ds_email", type="string", length=100, nullable=true)
     */
    private $email;

    /**
     * @var string
     *
     * @ORM\Column(name="ds_horario_funcionamento", type="text", nullable=false)
     */
    private $horarioFuncionamento;

    /**
     * @var string
     *
     * @ORM\Column(name="ds_descricao", type="text", nullable=true)
     */
    private $descricao;

    /**
     * @var integer
     *
     * @ORM\Column(name="st_publicado", type="integer", nullable=false)
     */
    private $publicado = '0';
    
     /**
     * @var integer
     *
     * @ORM\Column(name="nu_ordem", type="integer", nullable=false)
     */
    private $ordem;

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
     * @var \Entity\RedeSocialBiblioteca
     *
     * @ORM\OneToMany(targetEntity="Entity\RedeSocialBiblioteca", mappedBy="biblioteca", fetch="EXTRA_LAZY")
     */
    private $redesSociais;

    public function __construct()
    {
        $this->setDataCadastro(new \DateTime('now'));
    }

    /**
     * 
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getRedesSociais()
    {
        return $this->redesSociais;
    }

    /**
     * 
     * @param \Doctrine\Common\Collections\Collection $redesSociais
     */
    public function setRedesSociais(\Doctrine\Common\Collections\Collection $redesSociais)
    {
        $this->redesSociais = $redesSociais;
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
     * @param \DateTime $dataCadastro
     */
    public function setDataCadastro(\DateTime $dataCadastro)
    {
        $this->dataCadastro = $dataCadastro;
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

    public function getResponsavel()
    {
        return $this->responsavel;
    }

    public function getCidade()
    {
        return $this->cidade;
    }

    public function getUf()
    {
        return $this->uf;
    }

    public function getCep()
    {
        return $this->cep;
    }

    public function getEndereco()
    {
        return $this->endereco;
    }

    public function getNumero()
    {
        return $this->numero;
    }

    public function getComplemento()
    {
        return $this->complemento;
    }

    public function getBairro()
    {
        return $this->bairro;
    }

    public function getTelefone()
    {
        return $this->telefone;
    }

    public function getCelular()
    {
        return $this->celular;
    }

    public function getEmail()
    {
        return $this->email;
    }

    public function getHorarioFuncionamento()
    {
        return $this->horarioFuncionamento;
    }

    public function getDescricao()
    {
        return $this->descricao;
    }

    public function getPublicado()
    {
        return $this->publicado;
    }
    
    public function getOrdem()
    {
        return $this->ordem;
    }

    public function getImagem()
    {
        return $this->imagem;
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
     * @param string $responsavel
     */
    public function setResponsavel($responsavel)
    {
        $this->responsavel = $responsavel;
    }

    /**
     * 
     * @param string $cidade
     */
    public function setCidade($cidade)
    {
        $this->cidade = $cidade;
    }

    /**
     * 
     * @param string $uf
     */
    public function setUf($uf)
    {
        $this->uf = $uf;
    }

    /**
     * 
     * @param string $cep
     */
    public function setCep($cep)
    {
        $this->cep = $cep;
    }

    /**
     * 
     * @param string $endereco
     */
    public function setEndereco($endereco)
    {
        $this->endereco = $endereco;
    }

    /**
     * 
     * @param string $numero
     */
    public function setNumero($numero)
    {
        $this->numero = $numero;
    }

    /**
     * 
     * @param string $complemento
     */
    public function setComplemento($complemento)
    {
        $this->complemento = $complemento;
    }

    /**
     * 
     * @param string $bairro
     */
    public function setBairro($bairro)
    {
        $this->bairro = $bairro;
    }

    /**
     * 
     * @param string $telefone
     */
    public function setTelefone($telefone)
    {
        $this->telefone = $telefone;
    }

    /**
     * 
     * @param string $celular
     */
    public function setCelular($celular)
    {
        $this->celular = $celular;
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
     * @param string $horarioFuncionamento
     */
    public function setHorarioFuncionamento($horarioFuncionamento)
    {
        $this->horarioFuncionamento = $horarioFuncionamento;
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
     * @param integer $publicado
     */
    public function setPublicado($publicado)
    {
        $this->publicado = $publicado;
    }
    
    
    /**
     * 
     * @param integer $publicado
     */
    public function setOrdem($ordem)
    {
        $this->ordem = $ordem;
    }

    /**
     * 
     * @param \Entity\Imagem $imagem
     */
    public function setImagem(\Entity\Imagem $imagem)
    {
        $this->imagem = $imagem;
    }

    public function getLabel()
    {
        return $this->getNome();
    }

    public function toArray()
    {
        return array(
            "id" => $this->getId(),
            "bairro" => $this->getBairro(),
            "celular" => $this->getCelular(),
            "cep" => $this->getCep(),
            "cidade" => $this->getCidade(),
            "complemento" => $this->getComplemento(),
            "dataCadastro" => $this->getDataCadastro(),
            "dataFinal" => $this->getDataFinal(),
            "dataInicial" => $this->getDataInicial(),
            "descricao" => $this->getDescricao(),
            "email" => $this->getEmail(),
            "endereco" => $this->getEndereco(),
            "horarioFuncionamento" => $this->getHorarioFuncionamento(),
            "imagem" => $this->getImagem(),
            "nome" => $this->getNome(),
            "numero" => $this->getNumero(),
            "publicado" => $this->getPublicado(),
            "ordem" => $this->getOrdem(),
            "responsavel" => $this->getResponsavel(),
            "telefone" => $this->getTelefone(),
            "uf" => $this->getUf(),
            "redesSociais" => $this->getRedesSociais()
        );
    }

}
