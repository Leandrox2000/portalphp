<?php
namespace Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * BannerGeral
 *
 * @ORM\HasLifecycleCallbacks()
 * @ORM\Table(name="tb_banner_geral", uniqueConstraints={@ORM\UniqueConstraint(name="uk_banner_geral_banner", columns={"id_banner_geral"})}, indexes={@ORM\Index(name="IDX_405F724A3C2736E4", columns={"id_imagem"}), @ORM\Index(name="IDX_405F724A65B6765", columns={"id_banner_geral_categoria"})})
 * @ORM\Entity(repositoryClass="Entity\Repository\BannerGeralRepository")
 */
class BannerGeral extends AbstractEntity implements EntityInterface
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id_banner_geral", type="bigint", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\SequenceGenerator(sequenceName="tb_banner_geral_id_banner_geral_seq", allocationSize=1, initialValue=1)
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
     * @ORM\Column(name="dt_cadastro", type="datetime", nullable=false)
     */
    private $dataCadastro;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="dt_final", type="datetime", nullable=true)
     */
    private $dataFinal;

    /**
     * @var integer
     *
     * @ORM\Column(name="st_publicado", type="bigint", nullable=false)
     */
    private $publicado = '0';

    /**
     * @var integer
     *
     * @ORM\Column(name="nu_ordem", type="bigint", nullable=true)
     */
    private $ordem;

    /**
     * @var string
     *
     * @ORM\Column(name="no_nome", type="string", length=100, nullable=false)
     */
    private $nome;

    /**
     * @var string
     *
     * @ORM\Column(name="ds_descricao", type="text", nullable=true)
     */
    private $descricao;

    /**
     * @var string
     *
     * @ORM\Column(name="st_abrir_em", type="string", nullable=true)
     */
    private $abrirEm = 'm';

    /**
     * @var integer
     *
     * @ORM\Column(name="st_tem_link", type="bigint", nullable=true)
     */
    private $temLink = '0';

    /**
     * @var string
     *
     * @ORM\Column(name="ds_url", type="string", length=300, nullable=true)
     */
    private $url;

    /**
     * @var \Entity\Imagem
     *
     * @ORM\ManyToOne(targetEntity="Imagem", fetch="EAGER")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_imagem", referencedColumnName="id_imagem")
     * })
     */
    private $imagem;

    /**
     * @var \Entity\BannerGeralCategoria
     *
     * @ORM\ManyToOne(targetEntity="BannerGeralCategoria")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_banner_geral_categoria", referencedColumnName="id_banner_geral_categoria")
     * })
     */
    private $categoria;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\ManyToMany(targetEntity="Entity\Site", inversedBy="idBannerGeral", cascade={"persist"})
     * @ORM\JoinTable(name="tb_banner_geral_site",
     *   joinColumns={
     *     @ORM\JoinColumn(name="id_banner_geral", referencedColumnName="id_banner_geral")
     *   },
     *   inverseJoinColumns={
     *     @ORM\JoinColumn(name="id_site", referencedColumnName="id_site")
     *   }
     * )
     */
    private $sites;

    /**
     * @var \Entity\FuncionalidadeMenu
     *
     * @ORM\ManyToOne(targetEntity="Entity\FuncionalidadeMenu", fetch="EAGER")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_funcionalidade_menu", referencedColumnName="id_funcionalidade_menu", nullable=true)
     * })
     */
    private $funcionalidadeMenu;

    /**
     * @var integer
     *
     * @ORM\Column(name="id_entidade", type="bigint", nullable=true)
     */
    private $idEntidade;

    /**
     *
     * @var string
     */
    private $urlCompleta;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->sites = new \Doctrine\Common\Collections\ArrayCollection();
        $this->dataCadastro = new \DateTime('now');
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
     * URL computada com base nos campos url, funcionalidade e idEntidade.
     * 
     * @return string
     */
    public function getUrlCompleta()
    {
        return $this->urlCompleta;
    }

    /**
     *
     * @param string $urlCompleta
     */
    public function setUrlCompleta($urlCompleta)
    {
        $this->urlCompleta = $urlCompleta;
    }

    /**
     *
     * @return \Entity\FuncionalidadeMenu
     */
    public function getFuncionalidadeMenu()
    {
        return $this->funcionalidadeMenu;
    }

    /**
     *
     * @return integer
     */
    public function getIdEntidade()
    {
        return $this->idEntidade;
    }

    /**
     *
     * @param \Entity\FuncionalidadeMenu $funcionalidadeMenu
     */
    public function setFuncionalidadeMenu(\Entity\FuncionalidadeMenu $funcionalidadeMenu = null)
    {
        $this->funcionalidadeMenu = $funcionalidadeMenu;
    }

    /**
     *
     * @param integer $idEntidade
     */
    public function setIdEntidade($idEntidade)
    {
        $this->idEntidade = $idEntidade;
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
    public function getDataCadastro()
    {
        return $this->dataCadastro;
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
    public function getNome()
    {
        return $this->nome;
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
    public function getAbrirEm()
    {
        return $this->abrirEm;
    }

    /**
     *
     * @return integer
     */
    public function getTemLink()
    {
        return $this->temLink;
    }

    /**
     *
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
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
     * @return \Entity\BannerGeralCategoria
     */
    public function getCategoria()
    {
        return $this->categoria;
    }

    /**
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getSites()
    {
        return $this->sites;
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
     * @param \DateTime $dataCadastro
     */
    public function setDataCadastro(\DateTime $dataCadastro)
    {
        $this->dataCadastro = $dataCadastro;
    }

    /**
     *
     * @param \DateTime $dataFinal
     */
    // Removida a obrigatoriedade do tipo da variável ser DateTime devido à necessidade de setar datas NULAS
    // Variável deve poder receber DATAS no padrão DateTime ou NULL
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
     * @param integer $ordem
     */
    public function setOrdem($ordem)
    {
        $this->ordem = $ordem;
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
     * @param string $descricao
     */
    public function setDescricao($descricao)
    {
        $this->descricao = $descricao;
    }

    /**
     *
     * @param string $abrirEm
     */
    public function setAbrirEm($abrirEm)
    {
        $this->abrirEm = $abrirEm;
    }

    /**
     *
     * @param integer $temLink
     */
    public function setTemLink($temLink)
    {
        $this->temLink = $temLink;
    }

    /**
     *
     * @param string $url
     */
    public function setUrl($url)
    {
        $this->url = $url;
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
     * @param \Entity\BannerGeralCategoria $categoria
     */
    public function setCategoria(\Entity\BannerGeralCategoria $categoria)
    {
        $this->categoria = $categoria;
    }

    /**
     *
     * @param \Doctrine\Common\Collections\Collection $sites
     */
    public function setSites(\Doctrine\Common\Collections\Collection $sites)
    {
        $this->sites = $sites;
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
            'id' => $this->getId(),
            'dataInicial' => $this->getDataInicial(),
            'dataCadastro' => $this->getDataCadastro(),
            'dataFinal' => $this->getDataFinal(),
            'publicado' => $this->getPublicado(),
            'descricao' => $this->getDescricao(),
            'categoria' => $this->getCategoria(),
            'abrirEm' => $this->getAbrirEm(),
            'temLink' => $this->getTemLink(),
            'url' => $this->getUrl(),
            'sites' => $this->getSites(),
            'ordem' => $this->getOrdem(),
            'imagem' => $this->getImagem(),
            'nome' => $this->getNome(),
            'urlCompleta' => $this->getUrlCompleta(),
            'funcionalidadeMenu' => $this->getFuncionalidadeMenu(),
            'idEntidade' => $this->getIdEntidade()
        );
    }

    /**
     * Altera a URL do banner, caso os campos "Funcionalidade" e/ou "URL"
     * tenham sido preenchidos.
     *
     * @ORM\PostLoad
     */
    public function postLoad()
    {
        $this->urlCompleta = $this->url;
       
        // Se foi selecionada uma Funcionalidade
        if (!empty($this->funcionalidadeMenu)) {
            // Troca a URL pela URL da funcionalidade
            $this->urlCompleta = $this->funcionalidadeMenu->getUrl();
        }

        // Se foi selecionado um conteúdo da Funcionalidade
        if (!empty($this->idEntidade)) {
            // Se uma entidade foi definida incrementa a URL, apontando
            // para a página de detalhes da funcionalidade
            $this->urlCompleta .= '/' . $this->idEntidade;
        }
    }
    
    public function setUrlCompletaSite($site, $func){
        $this->postLoad();
        if($func){
            if(in_array($this->urlCompleta, $func))
                $this->urlCompleta = strtolower($site).'/'.$this->urlCompleta;
        }
    }
    
    public function getUrlCompletaSite()
    {
        return $this->urlCompleta;
    }
}
