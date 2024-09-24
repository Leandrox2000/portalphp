<?php
namespace Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Imagem
 *
 * @ORM\Table(name="tb_imagem", indexes={@ORM\Index(name="IDX_A7C79A45CE25AE0A", columns={"id_imagem_pasta"})})
 * @ORM\Entity(repositoryClass="Entity\Repository\ImagemRepository")  
 */
class Imagem extends AbstractEntity implements EntityInterface
{

    /**
     * @var integer
     *
     * @ORM\Column(name="id_imagem", type="bigint", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\SequenceGenerator(sequenceName="tb_imagem_id_imagem_seq", allocationSize=1, initialValue=1)
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
     * @ORM\Column(name="no_nome", type="string", length=150, nullable=false)
     */
    private $nome;
    
    /**
     * @var string
     *
     * @ORM\Column(name="ds_palavras_chave", type="text", length=200, nullable=true)
     */
    private $palavrasChave;

    /**
     * @var string
     *
     * @ORM\Column(name="ds_credito", type="string", length=100, nullable=true)
     */
    private $credito;
    
    /**
     * @var string
     *
     * @ORM\Column(name="ds_legenda", type="string", length=200, nullable=true)
     */
    private $legenda;

    /**
     * @var string
     *
     * @ORM\Column(name="no_imagem", type="string", length=100, nullable=false)
     */
    private $imagem;

    /**
     * @var \Entity\ImagemPasta
     *
     * @ORM\ManyToOne(targetEntity="Entity\ImagemPasta", fetch="EAGER")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_imagem_pasta", referencedColumnName="id_imagem_pasta")
     * })
     */
    private $pasta;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\ManyToMany(targetEntity="Entity\Galeria", inversedBy="imagens", cascade={"persist"})
     * @ORM\JoinTable(name="tb_galeria_imagem",
     *   joinColumns={
     *     @ORM\JoinColumn(name="id_imagem", referencedColumnName="id_imagem")
     *   },
     *   inverseJoinColumns={
     *     @ORM\JoinColumn(name="id_galeria", referencedColumnName="id_galeria")
     *   }
     * )
     */
    private $galerias;

    /**
     * @var \Doctrine\Common\Collections\Collection
     * 
     * @ORM\OneToMany(targetEntity="Publicacao", mappedBy="imagem", fetch="EXTRA_LAZY")
     */
    private $publicacoes;

    /**
     * @var \Doctrine\Common\Collections\Collection
     * 
     * @ORM\OneToMany(targetEntity="Bibliografia", mappedBy="imagem", fetch="EXTRA_LAZY")
     */
    private $bibliografias;

    /**
     * @var \Doctrine\Common\Collections\Collection
     * 
     * @ORM\OneToMany(targetEntity="BackgroundHome", mappedBy="imagem", fetch="EXTRA_LAZY")
     */
    private $backgroundsHome;

    public function __construct()
    {
        $this->setDataCadastro(new \DateTime('now'));
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
     * @return \Entity\ImagemPasta
     */
    public function getPasta()
    {
        return $this->pasta;
    }

    /**
     * 
     * @param \Entity\ImagemPasta $pasta
     */
    public function setPasta(\Entity\ImagemPasta $pasta)
    {
        $this->pasta = $pasta;
    }
    
    public function getLegenda()
    {
        return $this->legenda;
    }

    public function setLegenda($legenda)
    {
        $this->legenda = $legenda;
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
    public function getCredito()
    {
        return $this->credito;
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
     * @param int $id
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
     * @param string $credito
     */
    public function setCredito($credito)
    {
        $this->credito = $credito;
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
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getPublicacoes()
    {
        return $this->publicacoes;
    }

    /**
     * 
     * @param \Doctrine\Common\Collections\Collection $publicacoes
     */
    public function setPublicacoes(\Doctrine\Common\Collections\Collection $publicacoes)
    {
        $this->publicacoes = $publicacoes;
    }

    /**
     * 
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getBibliografias()
    {
        return $this->bibliografias;
    }

    /**
     * 
     * @param \Doctrine\Common\Collections\Collection $bibliografias
     */
    public function setBibliografia(\Doctrine\Common\Collections\Collection $bibliografias)
    {
        $this->bibliografias = $bibliografias;
    }

    /**
     * 
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getBannersHome()
    {
        return $this->bannersHome;
    }
    
    /**
     * 
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getBackgroundsHome()
    {
        return $this->backgroundsHome;
    }
    
    /**
     * 
     * @param \Doctrine\Common\Collections\Collection $backgroundsHome
     */
    public function setBackgroundsHome(\Doctrine\Common\Collections\Collection $backgroundsHome)
    {
        $this->backgroundsHome = $backgroundsHome;
    }

    /**
     * 
     * @param \Doctrine\Common\Collections\Collection $bannersHome
     */
    public function setBannersHome(\Doctrine\Common\Collections\Collection $bannersHome)
    {
        $this->bannersHome = $bannersHome;
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
            'dataCadastro' => $this->getDataCadastro(),
            'nome' => $this->getNome(),
            'palavrasChave' => $this->getPalavrasChave(),
            'credito' => $this->getCredito(),
            'legenda' => $this->getLegenda(),
            'imagem' => $this->getImagem(),
            'pasta' => $this->getPasta(),
            'galerias' => $this->getGalerias(),
            'publicacoes' => $this->getPublicacoes(),
            'bibliografias' => $this->getBibliografias(),
            'bannersHome' => $this->getBannersHome(),
            'backgroundsHome' => $this->getBackgroundsHome(),
        );
    }

}
