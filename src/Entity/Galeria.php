<?php

namespace Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Galeria
 *
 * @ORM\Table(name="tb_galeria")
 * @ORM\Entity(repositoryClass="Entity\Repository\GaleriaRepository")  
 */ 
class Galeria extends AbstractEntity implements EntityInterface
{

    /**
     * @var integer
     *
     * @ORM\Column(name="id_galeria", type="bigint", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\SequenceGenerator(sequenceName="tb_galeria_id_galeria_seq", allocationSize=1, initialValue=1)
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
     * @ORM\Column(name="ds_descricao", type="text", nullable=true)
     */
    private $descricao;

    /**
     * @var integer
     *
     * @ORM\Column(name="st_publicado", type="integer", nullable=false)
     */
    private $publicado = 0;
    
 
    /**
     * @var integer
     *
     * @ORM\Column(name="st_propriedade_sede", type="integer", nullable=false)
     */
    private $propriedadeSede = 0;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\ManyToMany(targetEntity="Entity\Imagem", inversedBy="galerias")
     * @ORM\JoinTable(name="tb_galeria_imagem",
     *   joinColumns={
     *     @ORM\JoinColumn(name="id_galeria", referencedColumnName="id_galeria")
     *   },
     *   inverseJoinColumns={
     *     @ORM\JoinColumn(name="id_imagem", referencedColumnName="id_imagem")
     *   }
     * )
     */
    private $imagens;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\ManyToMany(targetEntity="Entity\Site", inversedBy="galerias")
     * @ORM\JoinTable(name="tb_galeria_site",
     *   joinColumns={
     *     @ORM\JoinColumn(name="id_galeria", referencedColumnName="id_galeria")
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
     * @ORM\ManyToMany(targetEntity="Entity\Site", inversedBy="paiGalerias", cascade={"persist"})
     * @ORM\JoinTable(name="tb_pai_galeria_site",
     *   joinColumns={
     *     @ORM\JoinColumn(name="id_galeria", referencedColumnName="id_galeria")
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
     * @ORM\ManyToMany(targetEntity="Entity\Noticia", inversedBy="galerias")
     */
    private $noticias;
    
    /**
     * @var \Doctrine\Common\Collections\Collection
     * 
     * @ORM\OneToMany(targetEntity="PaginaEstaticaGaleria", mappedBy="galeria", fetch="EXTRA_LAZY")
     */
    private $paginasEstaticas;
//
//    /**
//     * @var \Doctrine\Common\Collections\Collection
//     *
//     * @ORM\ManyToMany(targetEntity="DestaqueHome", mappedBy="sites", fetch="EXTRA_LAZY")
//     */
//    private $fototecas;
    
     /**
     * @var Collection
     * 
     * @ORM\OneToMany(targetEntity="GaleriaSite", mappedBy="galeria")
     */
     
    private $galeriasSite;

    function getGaleriasSite() {
        return $this->galeriasSite;
    }
    
    public function setGaleriasSite($galeriasSite)
    {
        $this->galeriasSite = $galeriasSite;
    }
    /**
     * 
     * @param \Doctrine\Common\Collections\Collection $galeria
     */
    /*public function setGaleriasSite(\Doctrine\Common\Collections\Collection $galeria)
    {
        $this->galeriasSite = $galeria;
    }*/
    
    
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->setPaginasEstaticas(new \Doctrine\Common\Collections\ArrayCollection());
        $this->setImagens(new \Doctrine\Common\Collections\ArrayCollection());
        $this->setSites(new \Doctrine\Common\Collections\ArrayCollection());
        $this->setNoticias(new \Doctrine\Common\Collections\ArrayCollection());
        //$this->setGaleriasSite(new \Doctrine\Common\Collections\ArrayCollection());
        $this->setDataCadastro(new \DateTime('now'));
//        $this->setFototecas(new \Doctrine\Common\Collections\ArrayCollection());
        
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
    
    function getPaiSites() {
        return $this->paiSites;
    }

    function setPaiSites(\Doctrine\Common\Collections\Collection $paiSites) {
        $this->paiSites = $paiSites;
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
    public function getDescricao()
    {
        return $this->descricao;
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
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getNoticias()
    {
        return $this->noticias;
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
     * @param string $titulo
     */
    public function setTitulo($titulo)
    {
        $this->titulo = $titulo;
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
     * @param \Doctrine\Common\Collections\Collection $imagem
     */
    public function setImagens(\Doctrine\Common\Collections\Collection $imagens)
    {
        $this->imagens = $imagens;
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
     * @param \Doctrine\Common\Collections\Collection $noticias
     */
    public function setNoticias(\Doctrine\Common\Collections\Collection $noticias)
    {
        $this->noticias = $noticias;
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
     * @return Imagens
     */
    public function getImagens()
    {
        return $this->imagens;
    }

    /**
     * 
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getPaginasEstaticas()
    {
        return $this->paginasEstaticas;
    }

    /**
     * 
     * @param \Doctrine\Common\Collections\Collection $paginasEstaticas
     */
    public function setPaginasEstaticas(\Doctrine\Common\Collections\Collection $paginasEstaticas)
    {
        $this->paginasEstaticas = $paginasEstaticas;
    }
    
    /**
     * 
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getFototecas()
    {
        return $this->fototecas;
    }

    /**
     * 
     * @param \Doctrine\Common\Collections\Collection $fototecas
     */
    public function setFototecas(\Doctrine\Common\Collections\Collection $fototecas)
    {
        $this->fototecas = $fototecas;
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
     * @return string
     */
    public function getLogin()
    {
        return $this->login;
    }

    /**
     * @param string $login
     */
    public function setLogin($login)
    {
        $this->login = $login;
    }

    /**
     * Ordena  m array de objetos imagem de acordo com a ordem especificada no parâmetro 
     * @param array $idsOrdem
     * @return type
     */
    public function getImagensOrdenadas(array $idsOrdem){
        
        //Cria o array que receberá as imagens
        $arrayImagens = array();
        
        //Popula o array com a chave de acordo com o id
        foreach($this->getImagens() as $imagem){
            $arrayImagens[$imagem->getId()] = $imagem;
        }
        
        //Cria o segundo array que receberá as imagens na ordem
        $arrayImagensOrdenadas = array();
        
        //popula o array
        foreach($idsOrdem as $id){
            if(isset($arrayImagens[$id])){
                $arrayImagensOrdenadas[] = $arrayImagens[$id];
            }
        }
        
        
        return $arrayImagensOrdenadas;
        
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
            "descricao" => $this->getDescricao(),
            "sites" => $this->getSites(),
            "noticias" => $this->getNoticias(),
            "imagens" => $this->getImagens(),
            "publicado" => $this->getPublicado(),
            "propriedadeSede" => $this->getPropriedadeSede(),
            "paginasEstaticas" => $this->getPaginasEstaticas(),
            "fototecas" => $this->getFototecas()
        );
    }

}
