<?php

namespace Entity;


use Doctrine\ORM\Mapping as ORM;

/**
 * ComentarioNoticia
 *
 * @ORM\Table(name="tb_comentario_noticia", uniqueConstraints={@ORM\UniqueConstraint(name="uk_comentario_noticia_comentario_noticia", columns={"id_comentario_noticia"})}, indexes={@ORM\Index(name="IDX_DC673E74EFBDF6E6", columns={"id_noticia"})})
 * @ORM\Entity(repositoryClass="Entity\Repository\ComentarioNoticiaRepository")
 */
class ComentarioNoticia extends AbstractEntity implements EntityInterface
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id_comentario_noticia", type="bigint", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\SequenceGenerator(sequenceName="tb_comentario_noticia_id_comentario_noticia_seq", allocationSize=1, initialValue=1)
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
     * @ORM\Column(name="dt_final", type="datetime", nullable=true)
     */
    private $dataFinal;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="dt_cadastro", type="datetime", nullable=false)
     */
    private $dataCadastro;

    /**
     * @var string
     *
     * @ORM\Column(name="no_autor", type="string", length=100, nullable=false)
     */
    private $autor;

    /**
     * @var string
     *
     * @ORM\Column(name="ds_email", type="string", length=200, nullable=true)
     */
    private $email;

    /**
     * @var string
     *
     * @ORM\Column(name="ds_comentario", type="text", nullable=false)
     */
    private $comentario;

    /**
     * @var integer
     *
     * @ORM\Column(name="st_publicado", type="integer", nullable=false)
     */
    private $publicado = '0';

    /**
     * @var \Entity\Noticia
     *
     * @ORM\ManyToOne(targetEntity="Entity\Noticia")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_noticia", referencedColumnName="id_noticia")
     * })
     */
    private $noticia;
    
    function __construct()
    {
        $this->setDataCadastro(new \DateTime("now"));
    }

    /**
     * 
     * @return string
     */
    public function getLabel()
    {
        return $this->getComentario()
                . ' em <a style="color: blue;" href="noticia/form/'
                . $this->getNoticia()->getId()
                . '">&laquo;&nbsp;'
                . mb_strtoupper($this->getNoticia()->getTitulo(), 'UTF-8')
                . '&nbsp;&raquo;</a>';
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
            'dataInicial' => $this->getDataInicial(),
            'dataFinal' => $this->getDataFinal(),
            'autor' => $this->getAutor(),
            'email' => $this->getEmail(),
            'publicado' => $this->getPublicado(),
            'noticia' => $this->getNoticia()
        );
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
     * @return string
     */
    public function getAutor()
    {
        return $this->autor;
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
    public function getComentario()
    {
        return $this->comentario;
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
     * @return \Entity\Noticia
     */
    public function getNoticia()
    {
        return $this->noticia;
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
     * @param \DateTime $dataFinal
     */
    public function setDataFinal(\DateTime $dataFinal)
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
     * @param string $autor
     */
    public function setAutor($autor)
    {
        $this->autor = $autor;
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
     * @param string $comentario
     */
    public function setComentario($comentario)
    {
        $this->comentario = $comentario;
    }

    /**
     * 
     * @param string $publicado
     */
    public function setPublicado($publicado)
    {
        $this->publicado = $publicado;
    }

    /**
     * 
     * @param \Entity\Noticia $noticia
     */
    public function setNoticia(\Entity\Noticia $noticia)
    {
        $this->noticia = $noticia;
    }

}
