<?php

namespace Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Unidade
 *
 * @ORM\Table(name="tb_unidade")
 * @ORM\Entity(repositoryClass="Entity\Repository\UnidadeRepository")
 */
class Unidade extends AbstractEntity implements EntityInterface
{

    /**
     * @var integer
     *
     * @ORM\Column(name="id_unidade", type="bigint", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\SequenceGenerator(sequenceName="tb_unidade_id_unidade_seq", allocationSize=1, initialValue=1)
     */
    private $id;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="dt_cadastro", type="datetime", nullable=false)
     */
    private $dataCadastro;

    /**
     * @var string
     *
     * @ORM\Column(name="no_unidade", type="string", length=100, nullable=false)
     */
    private $nome;

    /**
     * @var string
     *
     * @ORM\Column(name="no_cidade", type="string", length=100, nullable=false)
     */
    private $cidade;

    /**
     * @var string
     *
     * @ORM\Column(name="ds_cep", type="string", nullable=false)
     */
    private $cep;
    
    /**
     * @var integer
     *
     * @ORM\Column(name="nu_ordem", type="integer", nullable=false)
     */
    private $ordem;
    
    

    /**
     * @var string
     *
     * @ORM\Column(name="sg_uf", type="string", nullable=false)
     */
    private $uf;

    /**
     * @var string
     *
     * @ORM\Column(name="ds_endereco", type="text", nullable=false)
     */
    private $endereco;

    /**
     * @var string
     *
     * @ORM\Column(name="no_bairro", type="string", length=50, nullable=false)
     */
    private $bairro;

    /**
     * @var string
     *
     * @ORM\Column(name="no_complemento", type="string", length=50, nullable=false)
     */
    private $complemento;

    /**
     * @var string
     *
     * @ORM\Column(name="ds_numero", type="string", length=10, nullable=false)
     */
    private $numero;

    /**
     * @var string
     *
     * @ORM\Column(name="ds_telefone", type="string", length=20, nullable=false)
     */
    private $telefone;

    /**
     * @var string
     *
     * @ORM\Column(name="ds_celular", type="string", length=50, nullable=true)
     */
    private $celular;


    /**
     * @var string
     *
     * @ORM\Column(name="ds_email", type="string", length=100, nullable=false)
     */
    private $email;

    /**
     * @var string
     *
     * @ORM\Column(name="ds_site", type="string", length=100, nullable=false)
     */
    private $site;

    /**
     * @var string
     *
     * @ORM\Column(name="no_estado", type="string", length=100, nullable=true)
     */
    private $estado;



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
    public function getDataCadastro()
    {
        return $this->dataCadastro;
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
    public function getCidade()
    {
        return $this->cidade;
    }

    /**
     *
     * @return string
     */
    public function getCep()
    {
        return $this->cep;
    }
    
    /**
     *
     * @return integer
     */
    public function getOrdem()
    {
        return $this->ordem;
    }
    

    /**
     *
     * @return string
     */
    public function getUf()
    {
        return $this->uf;
    }

    /**
     *
     * @return string
     */
    public function getEndereco()
    {
        return $this->endereco;
    }

    /**
     *
     * @return string
     */
    public function getBairro()
    {
        return $this->bairro;
    }

    /**
     *
     * @return string
     */
    public function getComplemento()
    {
        return $this->complemento;
    }

    /**
     *
     * @return string
     */
    public function getNumero()
    {
        return $this->numero;
    }

    /**
     *
     * @return string
     */
    public function getTelefone()
    {
        return $this->telefone;
    }

    /**
     *
     * @return string
     */
    public function getCelular()
    {
        return $this->celular;
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
    public function getSite()
    {
        return $this->site;
    }

    /**
     *
     * @return string
     */
    public function getEstado()
    {
        return $this->estado;
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
     * @param string $nome
     */
    public function setNome($nome)
    {
        $this->nome = $nome;
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
     * @param string $cep
     */
    public function setCep($cep)
    {
        $this->cep = $cep;
    }
    
    
    /**
     *
     * @param integer $ordem
     */
    public function setOrdem($ordem)
    {
        $this->ordem = $ordem;
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
     * @param string $endereco
     */
    public function setEndereco($endereco)
    {
        $this->endereco = $endereco;
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
     * @param string $complemento
     */
    public function setComplemento($complemento)
    {
        $this->complemento = $complemento;
    }

    /**
     *
     * @param integer $numero
     */
    public function setNumero($numero)
    {
        $this->numero = $numero;
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
     * @param string $site
     */
    public function setSite($site)
    {
        $this->site = $site;
    }

    /**
     *
     * @param integer $id
     */
    public function setEstado($estado)
    {
        $this->estado = $estado;
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
            "id" => $this->getId(),
            "dataCadastro" => $this->getDataCadastro(),
            "nome" => $this->getNome(),
            "cidade" => $this->getCidade(),
            "cep" => $this->getCep(),
            "ordem" => $this->getOrdem(),
            "uf" => $this->getUf(),
            "bairro" => $this->getBairro(),
            "complemento" => $this->getComplemento(),
            "numero" => $this->getNumero(),
            "telefone" => $this->getTelefone(),
            "celular" => $this->getCelular(),
            "email" => $this->getEmail(),
            "site" => $this->getSite(),
            "endereco" => $this->getEndereco(),
        );
    }

}
