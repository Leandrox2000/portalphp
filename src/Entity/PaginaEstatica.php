<?php

namespace Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * PaginaEstatica
 *
 * @ORM\Table(name="tb_pagina_estatica")
 * @ORM\Entity(repositoryClass="Entity\Repository\PaginaEstaticaRepository")
 */
class PaginaEstatica extends AbstractEntity implements EntityInterface
{

    /**
     * @var integer
     *
     * @ORM\Column(name="id_pagina_estatica", type="bigint", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\SequenceGenerator(sequenceName="tb_pagina_estatica_id_pagina_estatica_seq", allocationSize=1, initialValue=1)
     */
    private $id;
    
    /**
     * @var string
     *
     * @ORM\Column(name="at_username", type="string", length=150, nullable=true)
     */
    private $login;

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
     * @ORM\Column(name="no_titulo", type="string", length=150, nullable=false)
     */
    private $titulo;

    /**
     * @var string
     *
     * @ORM\Column(name="ds_conteudo", type="text", nullable=true)
     */
    private $conteudo;

    /**
     * @var string
     *
     * @ORM\Column(name="ds_palavras_chave", type="text", length=200, nullable=true)
     */
    private $palavrasChave;

    /**
     * @var integer
     *
     * @ORM\Column(name="st_propriedade_sede", type="integer", nullable=false)
     */
    private $propriedadeSede = 0;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\ManyToMany(targetEntity="Site", inversedBy="paginasEstaticos", cascade={"persist"})
     * @ORM\JoinTable(name="tb_pagina_estatica_site",
     *   joinColumns={
     *     @ORM\JoinColumn(name="id_pagina_estatica", referencedColumnName="id_pagina_estatica")
     *   },
     *   inverseJoinColumns={
     *     @ORM\JoinColumn(name="id_site", referencedColumnName="id_site")
     *   }
     * )
     */
    private $sites;
    
    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\ManyToMany(targetEntity="Entity\Site", inversedBy="paiPaginas", cascade={"persist"})
     * @ORM\JoinTable(name="tb_pai_pagina_estatica_site",
     *   joinColumns={
     *     @ORM\JoinColumn(name="id_pagina_estatica", referencedColumnName="id_pagina_estatica")
     *   },
     *   inverseJoinColumns={
     *     @ORM\JoinColumn(name="id_site", referencedColumnName="id_site")
     *   }
     * )
     */
    private $paiSites;

    /**
     * @var \Doctrine\Common\Collections\Collection
     * 
     * @ORM\OneToMany(targetEntity="PaginaEstaticaGaleria", mappedBy="paginaEstatica", fetch="EXTRA_LAZY", cascade={"remove"})
     */
    private $galerias;
    
    /**
     * @var integer
     *
     * @ORM\Column(name="st_publicado", type="integer", nullable=false)
     */
    private $publicado = '0';

    
    
  /**
     * @var \Entity\Menu
     *
     * @ORM\ManyToOne(targetEntity="Entity\Menu")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_menu", referencedColumnName="id_menu")
     * })
     */
    private $menu;
    
    
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->setSites(new \Doctrine\Common\Collections\ArrayCollection());
        $this->setGalerias(new \Doctrine\Common\Collections\ArrayCollection());
        $this->setDataCadastro(new \DateTime('now'));
    }
    
    function getLogin() {
        return $this->login;
    }

    function setLogin($login) {
        $this->login = $login;
    }
    
    function getPaiSites() {
        return $this->paiSites;
    }

    function setPaiSites(\Doctrine\Common\Collections\Collection $paiSites) {
        $this->paiSites = $paiSites;
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
     * @return string
     */
    public function getPalavrasChave()
    {
        return $this->palavrasChave;
    }

    /**
     * 
     * @param string $palavrasChave
     */
    public function setPalavrasChave($palavrasChave)
    {
        $this->palavrasChave = $palavrasChave;
    }
    
      /**
     * 
     * @param \Entity\Menu $menu
     */
    public function setMenu(\Entity\Menu $menu = null)
    {
        $this->menu = $menu;
    }
    
     /**
     * 
     * @return \Entity\Menu
     */
    public function getMenu()
    {
        return $this->menu;
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
    public function getTitulo()
    {
        return $this->titulo;
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
     * @return integer
     */
    public function getPublicado()
    {
        return $this->publicado;
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
     * @param $dataFinal
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
     * @param string $titulo
     */
    public function setTitulo($titulo)
    {
        $this->titulo = $titulo;
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
     * @param integer $publicado
     */
    public function setPublicado($publicado)
    {
        $this->publicado = $publicado;
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
     * @return integer
     */
    public function getPropriedadeSede()
    {
        return $this->propriedadeSede;
    }

    /**
     * 
     * @param integer $propriedadeSede
     */
    public function setPropriedadeSede($propriedadeSede)
    {
        $this->propriedadeSede = $propriedadeSede;
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
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getGalerias()
    {
        return $this->galerias;
    }

    /**
     * 
     * @param \Doctrine\Common\Collections\Collection $galerias
     */
    public function setGalerias(\Doctrine\Common\Collections\Collection $galerias)
    {
        $this->galerias = $galerias;
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
            "titulo" => $this->getTitulo(),
            'palavrasChave' => $this->getPalavrasChave(),
            "conteudo" => $this->getConteudo(),
            "publicado" => $this->getPublicado(),
            "sites" => $this->getSites(),
            "propriedadeSede" => $this->getPropriedadeSede(),
            "galerias" => $this->getGalerias()
        );
    }

}
