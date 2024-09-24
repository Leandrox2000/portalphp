<?php

namespace Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * LicitacaoConvenioContrato
 *
 * @ORM\Table(name="tb_licitacao_convenio_contrato", uniqueConstraints={@ORM\UniqueConstraint(name="uk_licitacao_convenio_contrato_id_licitacao_convenio_contrato", columns={"id_licitacao_convenio_contrato"})}, indexes={@ORM\Index(name="IDX_86642BA85F0233A5", columns={"id_status_lcc"}), @ORM\Index(name="IDX_86642BA85B456DCB", columns={"id_tipo_lcc"}), @ORM\Index(name="IDX_86642BA89AF0C591", columns={"id_ambito_lcc"}), @ORM\Index(name="IDX_86642BA841F85574", columns={"id_categoria_lcc"})})
 * @ORM\Entity(repositoryClass="Entity\Repository\LicitacaoConvenioContratoRepository")
 */
class LicitacaoConvenioContrato extends AbstractEntity implements EntityInterface
{

    /**
     * @var integer
     *
     * @ORM\Column(name="id_licitacao_convenio_contrato", type="bigint", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\SequenceGenerator(sequenceName="tb_licitacao_convenio_contrat_id_licitacao_convenio_contrat_seq", allocationSize=1, initialValue=1)
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
     * @var \DateTime
     *
     * @ORM\Column(name="dt_publicacao_dou", type="datetime", nullable=true)
     */
    private $dataPublicacaoDou;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="dt_abertura_proposta", type="datetime", nullable=true)
     */
    private $dataAberturaProposta;

    /**
     * @var string
     *
     * @ORM\Column(name="ds_edital", type="string", length=100, nullable=true)
     */
    private $edital;

    /**
     * @var string
     *
     * @ORM\Column(name="vl_estimado", type="string", length=50, nullable=true)
     */
    private $valorEstimado;

    /**
     * @var string
     *
     * @ORM\Column(name="ds_uasg", type="string", length=100, nullable=true)
     */
    private $uasg;

    /**
     * @var integer
     *
     * @ORM\Column(name="nu_ano", type="integer", nullable=true)
     */
    private $ano;

    /**
     * @var string
     *
     * @ORM\Column(name="ds_processo", type="string", length=50, nullable=true)
     */
    private $processo;

    /**
     * @var string
     *
     * @ORM\Column(name="ds_objeto", type="string", length=100, nullable=true)
     */
    private $objeto;

    /**
     * @var string
     *
     * @ORM\Column(name="ds_observacoes", type="text", nullable=true)
     */
    private $observacoes;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="dt_vigencia_inicial", type="datetime", nullable=true)
     */
    private $dataVigenciaInicial;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="dt_vigencia_final", type="datetime", nullable=true)
     */
    private $dataVigenciaFinal;

    /**
     * @var string
     *
     * @ORM\Column(name="no_contratada", type="string", length=120, nullable=true)
     */
    private $contratada;

    /**
     * @var integer
     *
     * @ORM\Column(name="st_publicado", type="integer", nullable=false)
     */
    private $publicado = '0';

    /**
     * @var \Entity\StatusLcc
     *
     * @ORM\ManyToOne(targetEntity="Entity\StatusLcc")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_status_lcc", referencedColumnName="id_status_lcc")
     * })
     */
    private $status;

    /**
     * @var \Entity\TipoLcc
     *
     * @ORM\ManyToOne(targetEntity="Entity\TipoLcc")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_tipo_lcc", referencedColumnName="id_tipo_lcc")
     * })
     */
    private $tipo;

    /**
     * @var \Entity\AmbitoLcc
     *
     * @ORM\ManyToOne(targetEntity="Entity\AmbitoLcc")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_ambito_lcc", referencedColumnName="id_ambito_lcc")
     * })
     */
    private $ambito;

    /**
     * @var \Entity\CategoriaLcc
     *
     * @ORM\ManyToOne(targetEntity="Entity\CategoriaLcc")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_categoria_lcc", referencedColumnName="id_categoria_lcc")
     * })
     */
    private $categoria;

    /**
     * @var \Doctrine\Common\Collections\Collection
     * 
     * @ORM\OneToMany(targetEntity="ArquivoLcc", mappedBy="licitacaoConvenioContrato", fetch="EXTRA_LAZY")
     * 
     */
    private $arquivos;

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
    public function getDataPublicacaoDou()
    {
        return $this->dataPublicacaoDou;
    }

    /**
     * 
     * @return \DateTime
     */
    public function getDataAberturaProposta()
    {
        return $this->dataAberturaProposta;
    }

    /**
     * 
     * @return String
     */
    public function getEdital()
    {
        return $this->edital;
    }

    /**
     * 
     * @return float
     */
    public function getValorEstimado()
    {
        return $this->valorEstimado;
    }

    /**
     * 
     * @return String
     */
    public function getUasg()
    {
        return $this->uasg;
    }

    /**
     * 
     * @return integer
     */
    public function getAno()
    {
        return $this->ano;
    }

    /**
     * 
     * @return String
     */
    public function getProcesso()
    {
        return $this->processo;
    }

    /**
     * 
     * @return String
     */
    public function getObjeto()
    {
        return $this->objeto;
    }

    /**
     * 
     * @return String
     */
    public function getObservacoes()
    {
        return $this->observacoes;
    }

    /**
     * 
     * @return \DateTime
     */
    public function getDataVigenciaInicial()
    {
        return $this->dataVigenciaInicial;
    }

    /**
     * 
     * @return \DateTime
     */
    public function getDataVigenciaFinal()
    {
        return $this->dataVigenciaFinal;
    }

    /**
     * 
     * @return String
     */
    public function getContratada()
    {
        return $this->contratada;
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
     * @return \Entity\StatusLcc
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * 
     * @return \Entity\TipoLcc
     */
    public function getTipo()
    {
        return $this->tipo;
    }

    /**
     * 
     * @return \Entity\AmbitoLcc
     */
    public function getAmbito()
    {
        return $this->ambito;
    }

    /**
     * 
     * @return \Entity\CategoriaLcc
     */
    public function getCategoria()
    {
        return $this->categoria;
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
     * @param \DateTime $dataPublicacaoDou
     */
    public function setDataPublicacaoDou(\DateTime $dataPublicacaoDou)
    {
        $this->dataPublicacaoDou = $dataPublicacaoDou;
    }

    /**
     * 
     * @param \DateTime $dataAberturaProposta
     */
    public function setDataAberturaProposta(\DateTime $dataAberturaProposta)
    {
        $this->dataAberturaProposta = $dataAberturaProposta;
    }

    /**
     * 
     * @param String $edital
     */
    public function setEdital($edital)
    {
        $this->edital = $edital;
    }

    /**
     * 
     * @param float $valorEstimado
     */
    public function setValorEstimado($valorEstimado)
    {
        $this->valorEstimado = $valorEstimado;
    }

    /**
     * 
     * @param String $uasg
     */
    public function setUasg($uasg)
    {
        $this->uasg = $uasg;
    }

    /**
     * 
     * @param integer $ano
     */
    public function setAno($ano)
    {
        $this->ano = $ano;
    }

    /**
     * 
     * @param String $processo
     */
    public function setProcesso($processo)
    {
        $this->processo = $processo;
    }

    /**
     * 
     * @param String $objeto
     */
    public function setObjeto($objeto)
    {
        $this->objeto = $objeto;
    }

    /**
     * 
     * @param String $observacoes
     */
    public function setObservacoes($observacoes)
    {
        $this->observacoes = $observacoes;
    }

    /**
     * 
     * @param \DateTime $dataVigenciaInicial
     */
    public function setDataVigenciaInicial(\DateTime $dataVigenciaInicial)
    {
        $this->dataVigenciaInicial = $dataVigenciaInicial;
    }

    /**
     * 
     * @param \DateTime $dataVigenciaFinal
     */
    public function setDataVigenciaFinal(\DateTime $dataVigenciaFinal)
    {
        $this->dataVigenciaFinal = $dataVigenciaFinal;
    }

    /**
     * 
     * @param String $contratada
     */
    public function setContratada($contratada)
    {
        $this->contratada = $contratada;
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
     * @param \Entity\StatusLcc $status
     */
    public function setStatus(\Entity\StatusLcc $status)
    {
        $this->status = $status;
    }

    /**
     * 
     * @param \Entity\TipoLcc $tipo
     */
    public function setTipo(\Entity\TipoLcc $tipo)
    {
        $this->tipo = $tipo;
    }

    /**
     * 
     * @param \Entity\AmbitoLcc $ambito
     */
    public function setAmbito(\Entity\AmbitoLcc $ambito)
    {
        $this->ambito = $ambito;
    }

    /**
     * 
     * @param \Entity\CategoriaLcc $categoria
     */
    public function setCategoria(\Entity\CategoriaLcc $categoria)
    {
        $this->categoria = $categoria;
    }

    /**
     * 
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getArquivos()
    {
        return $this->arquivos;
    }

    /**
     * 
     * @param \Doctrine\Common\Collections\Collection $arquivos
     */
    public function setArquivos(\Doctrine\Common\Collections\Collection $arquivos)
    {
        $this->arquivos = $arquivos;
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
            "dataInicial" => $this->getDataInicial(),
            "dataFinal" => $this->getDataFinal(),
            "ambito" => $this->getAmbito(),
            "categoria" => $this->getCategoria(),
            "tipo" => $this->getTipo(),
            "status" => $this->getStatus(),
            "dataPublicacaoDou" => $this->getDataPublicacaoDou(),
            "dataAberturaProposta" => $this->getDataAberturaProposta(),
            "edital" => $this->getEdital(),
            "valorEstimado" => $this->getValorEstimado(),
            "uasg" => $this->getUasg(),
            "ano" => $this->getAno(),
            "processo" => $this->getProcesso(),
            "objeto" => $this->getObjeto(),
            "observacoes" => $this->getObservacoes(),
            "dataVigenciaInicial" => $this->getDataVigenciaInicial(),
            "dataVigenciaFinal" => $this->getDataVigenciaFinal(),
            "contratada" => $this->getContratada(),
            "publicado" => $this->getPublicado(),
            "arquivos" => $this->getArquivos()
        );
    }

    /**
     * 
     * @return String
     */
    public function getLabel()
    {
        return $this->getObjeto();
    }

}
