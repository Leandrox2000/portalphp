<?php

namespace Entity;

use DateTime;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Menu
 *
 * @ORM\Table(name="tb_menu", indexes={@ORM\Index(name="IDX_2675388267BF2851", columns={"id_funcionalidade_menu"})})
 * @ORM\Entity(repositoryClass="Entity\Repository\MenuRepository")
 */
class Menu extends AbstractEntity implements EntityInterface
{
    const ABRIR_MESMA_JANELA = 'm';
    const ABRIR_NOVA_JENELA = 'n';

    /**
     * @var integer
     *
     * @ORM\Column(name="id_menu", type="bigint", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\SequenceGenerator(sequenceName="tb_menu_id_menu_seq", allocationSize=1, initialValue=1)
     */
    private $id;

    /**
     * @var integer
     *
     * @ORM\Column(name="id_entidade", type="bigint", nullable=true)
     */
    private $idEntidade;

    /**
     * @var string
     *
     * @ORM\Column(name="no_tipo_menu", type="string", length=50, nullable=false)
     */
    private $tipoMenu;

    /**
     * @var string
     *
     * @ORM\Column(name="no_titulo", type="string", length=100, nullable=false)
     */
    private $titulo;

    /**
     * @var string

     * @ORM\Column(name="ds_url_externa", type="string", length=200, nullable=true)
     */
    private $urlExterna;

    /**
     * @var string
     *
     * @ORM\Column(name="st_abrir", type="string", length=1, nullable=true)
     */
    private $abrirEm = 'm';

    /**
     * @var DateTime
     *
     * @ORM\Column(name="dt_inicial", type="datetime", nullable=false)
     */
    private $dataInicial;

    /**
     * @var DateTime
     *
     * @ORM\Column(name="dt_final", type="datetime", nullable=true)
     */
    private $dataFinal;

    /**
     * @var DateTime
     *
     * @ORM\Column(name="dt_cadastro", type="datetime", nullable=false)
     */
    private $dataCadastro;

    /**
     * @var integer
     *
     * @ORM\Column(name="st_publicado", type="bigint", nullable=false)
     */
    private $publicado = '0';

    /**
     * @var \Entity\Menu
     *
     * @ORM\ManyToOne(targetEntity="Entity\Menu")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_menu_menu", referencedColumnName="id_menu", nullable=true)
     * })
     */
    private $vinculoPai;

    /**
     * @var Collection
     * @ORM\OneToMany(targetEntity="Entity\Menu", mappedBy="vinculoPai", fetch="EXTRA_LAZY")
     **/
    private $filhos;
    
    /**
     * @var \Entity\Site
     *
     * @ORM\ManyToOne(targetEntity="Entity\Site")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_site", referencedColumnName="id_site", nullable=false)
     * })
     */
    private $site;

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
     * @ORM\Column(name="nu_ordem", type="bigint", nullable=true)
     */
    private $ordem;
    
     
    /**
     * @var Collection
     * @ORM\OneToMany(targetEntity="Entity\PaginaEstatica", mappedBy="menu", fetch="EXTRA_LAZY")
     **/
    private $paginas;
    

    public function __construct()
    {
        $this->dataCadastro = new DateTime('now');
        $this->setFilhos(new \Doctrine\Common\Collections\ArrayCollection());
        $this->setPaginas = (new \Doctrine\Common\Collections\ArrayCollection());
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
     * @param string $abrirEm
     */
    public function setAbrirEm($abrirEm)
    {
        $this->abrirEm = $abrirEm;
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
     * @param integer $ordem
     */
    public function setOrdem($ordem)
    {
        $this->ordem = $ordem;
    }

    /**
     *
     * @return \Entity\Site
     */
    public function getSite()
    {
        return $this->site;
    }

    /**
     *
     * @param \Entity\Site $site
     */
    public function setSite(\Entity\Site $site)
    {
        $this->site = $site;
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
     * @return integer
     */
    public function getIdEntidade()
    {
        return $this->idEntidade;
    }

    /**
     *
     * @return string
     */
    public function getTipoMenu()
    {
        return $this->tipoMenu;
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
    public function getUrlExterna()
    {
        return $this->urlExterna;
    }

    /**
     *
     * @return DateTime
     */
    public function getDataInicial()
    {
        return $this->dataInicial;
    }

    /**
     *
     * @return DateTime
     */
    public function getDataFinal()
    {
        return $this->dataFinal;
    }

    /**
     *
     * @return DateTime
     */
    public function getDataCadastro()
    {
        return $this->dataCadastro;
    }

    /**
     *
     * @return string
     */
    public function getPublicado()
    {
        return $this->publicado;
    }

    /**
     *
     * @return \Entity\Menu
     */
    public function getVinculoPai()
    {
        return $this->vinculoPai;
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
     * @param integer $idEntidade
     */
    public function setIdEntidade($idEntidade)
    {
        $this->idEntidade = $idEntidade;
    }

    /**
     *
     * @param string $tipoMenu
     */
    public function setTipoMenu($tipoMenu)
    {
        $this->tipoMenu = $tipoMenu;
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
     * @param string $urlExterna
     */
    public function setUrlExterna($urlExterna)
    {
        $this->urlExterna = $urlExterna;
    }

    /**
     *
     * @param DateTime $dataInicial
     */
    public function setDataInicial(DateTime $dataInicial)
    {
        $this->dataInicial = $dataInicial;
    }

    /**
     *
     * @param DateTime $dataFinal
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
     * @param DateTime $dataCadastro
     */
    public function setDataCadastro(DateTime $dataCadastro)
    {
        $this->dataCadastro = $dataCadastro;
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
     * @param \Entity\Menu $vinculoPai
     */
    public function setVinculoPai(\Entity\Menu $vinculoPai = NULL)
    {
        $this->vinculoPai = $vinculoPai;
    }

    /**
     *
     * @param \Entity\FuncionalidadeMenu $funcionalidadeMenu
     */
    public function setFuncionalidadeMenu(\Entity\FuncionalidadeMenu $funcionalidadeMenu = NULL)
    {
        $this->funcionalidadeMenu = $funcionalidadeMenu;
    }

    /**
     *
     * @return string
     */
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
        $array = array(
            'id' => $this->getId(),
            'dataCadastro' => $this->getDataCadastro(),
            'dataFinal' => $this->getDataFinal(),
            'dataInicial' => $this->getDataInicial(),
            'idEntidade' => $this->getIdEntidade(),
            'abrirEm' => $this->getAbrirEm(),
            'publicado' => $this->getPublicado(),
            'tipoMenu' => $this->getTipoMenu(),
            'titulo' => $this->getTitulo(),
            'urlExterna' => $this->getUrlExterna(),
            'site' => $this->getSite(),
            'ordem' => $this->getOrdem(),
        );

        if (is_object($this->getFuncionalidadeMenu())) {
            $array['funcionalidadeMenu'] = $this->getFuncionalidadeMenu()->toArray();
        }

        if (is_object($this->getVinculoPai())) {
            $array['vinculoPai'] = $this->getVinculoPai()->toArray();
        }

        return $array;
    }

    public function getFilhos()
    {
        return $this->filhos;
    }

    public function setFilhos(Collection $filhos)
    {
        $this->filhos = $filhos;
    }
    
    
     public function getPaginas()
    {
        return $this->paginas;
    }

    public function setPaginas(Collection $paginas)
    {
        $this->paginas = $paginas;
    }


}
