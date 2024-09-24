<?php

namespace Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * TbEnderecoRodape
 *
 * @ORM\Table(name="tb_endereco_rodape", uniqueConstraints={@ORM\UniqueConstraint(name="uk_endereco_rodape_id_endereco_rodape", columns={"id_endereco_rodape"})})
 * @ORM\Entity(repositoryClass="Entity\Repository\EnderecoRodapeRepository")
 */
class EnderecoRodape extends AbstractEntity implements EntityInterface
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id_endereco_rodape", type="bigint", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\SequenceGenerator(sequenceName="tb_endereco_rodape_id_endereco_rodape_seq", allocationSize=1, initialValue=1)
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="ds_endereco", type="text", nullable=false)
     */
    private $endereco;

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
     * Constructor
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
    public function getEndereco()
    {
        return $this->endereco;
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
     * @param string $endereco
     */
    public function setEndereco($endereco)
    {
        $this->endereco = $endereco;
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
     * @param int $publicado
     */
    public function setPublicado($publicado)
    {
        if ($publicado !== 1 && $publicado !== 0) {
            throw new \InvalidArgumentException("A publicação deve ser 0 ou 1");
        }
        $this->publicado = $publicado;
    }



    public function getLabel()
    {
        return $this->getEndereco();
    }

    /**
     *
     * @return array
     */
    public function toArray()
    {
        return array(
            'id'            => $this->getId(),
            'endereco'      => $this->getEndereco(),
            'dataCadastro'   => $this->getDataCadastro(),
            'dataInicial'   => $this->getDataInicial(),
            'dataFinal'     => $this->getDataFinal(),
            'publicado'     => $this->getPublicado(),
        );
    }



}
