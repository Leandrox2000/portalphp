<?php

namespace Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Diretoria
 *
 * @ORM\Table(name="tb_diretoria", uniqueConstraints={@ORM\UniqueConstraint(name="uk_diretoria_diretoria", columns={"id_diretoria"}),
 * @ORM\UniqueConstraint(name="uk_diretoria_funcionario", columns={"id_funcionario"}),
 * @ORM\UniqueConstraint(name="uk_diretoria_ordem", columns={"nu_ordem"})})
 * @ORM\Entity(repositoryClass="Entity\Repository\DiretoriaRepository")
 */
class Diretoria extends AbstractEntity implements EntityInterface {

    /**
     * @var integer
     *
     * @ORM\Column(name="id_diretoria", type="bigint", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\SequenceGenerator(sequenceName="tb_diretoria_id_diretoria_seq", allocationSize=1, initialValue=1)
     */
    private $id;

    /**
     * @var integer
     *
     * @ORM\Column(name="nu_ordem", type="bigint", nullable=true)
     */
    private $ordem;

    /**
     * @var integer
     *
     * @ORM\Column(name="st_publicado", type="bigint", nullable=false)
     */
    private $publicado = '0';

    /**
     * @var \Entity\Funcionario
     *
     * @ORM\OneToOne(targetEntity="Entity\Funcionario", mappedBy="diretor")
     * @ORM\JoinColumn(name="id_funcionario", referencedColumnName="id_funcionario")
     */
    private $funcionario;

    public function getId()
    {
        return $this->id;
    }

    public function getOrdem()
    {
        return $this->ordem;
    }

    public function getPublicado()
    {
        return $this->publicado;
    }

    public function getFuncionario()
    {
        return $this->funcionario;
    }

    public function setId($id)
    {
        $this->id = $id;
    }

    public function setOrdem($ordem)
    {
        $this->ordem = $ordem;
    }

    public function setPublicado($publicado)
    {
        $this->publicado = $publicado;
    }

    public function setFuncionario(\Entity\Funcionario $funcionario)
    {
        $this->funcionario = $funcionario;
    }

    public function getLabel() {
        return $this->getFuncionario()->getLabel();
    }

    public function toArray() {
        return array(
            'id' => $this->getId(),
            'ordem' => $this->getOrdem(),
            'funcionario' => $this->getFuncionario(),
            'publicado' => $this->getPublicado(),
        );
    }

}
