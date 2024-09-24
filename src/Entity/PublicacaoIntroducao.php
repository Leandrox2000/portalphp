<?php

namespace Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * PublicacaoIntroducao
 *
 * @ORM\Table(name="tb_publicacao_introducao", uniqueConstraints={@ORM\UniqueConstraint(name="uk_publicacao_introducao_publicacao_introducao", columns={"id_publicacao_introducao"})})
 * @ORM\Entity
 */
class PublicacaoIntroducao extends AbstractEntity implements EntityInterface
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id_publicacao_introducao", type="bigint", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\SequenceGenerator(sequenceName="tb_publicacao_introducao_id_publicacao_introducao_seq", allocationSize=1, initialValue=1)
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
     * @ORM\Column(name="ds_conteudo", type="text", nullable=false)
     */
    private $conteudo;

    public function __construct()
    {
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
    public function getConteudo()
    {
        return $this->conteudo;
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
     * @param string $conteudo
     */
    public function setConteudo($conteudo)
    {
        $this->conteudo = $conteudo;
    }

    /**
     *
     * @return string
     */
    public function getLabel()
    {
        return $this->getConteudo();
    }

    public function toArray()
    {
        return array(
            'id' => $this->getId(),
            'conteudo' => $this->getConteudo(),
            'dataCadastro' => $this->getDataCadastro(),
        );
    }

}
