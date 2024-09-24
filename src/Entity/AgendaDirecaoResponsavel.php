<?php
namespace Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Responsáveis pela Agenda da Direção
 *
 * @ORM\Table(name="tb_agenda_direcao_responsavel")
 * @ORM\Entity(repositoryClass="Entity\Repository\AgendaDirecaoResponsavelRepository")
 */
class AgendaDirecaoResponsavel extends AbstractEntity implements EntityInterface
{

    /**
     * @var integer
     *
     * @ORM\Column(name="id_agenda_direcao_responsavel", type="bigint", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\SequenceGenerator(sequenceName="tb_agenda_direcao_responsavel_id_agenda_direcao_responsavel_seq", allocationSize=1, initialValue=1)
     */
    private $id;
    
    /**
     * @var string
     *
     * @ORM\Column(name="ds_responsavel", type="string", length=130, nullable=false)
     */
    private $responsavel;

    /**
     * @var \Entity\Imagem
     *
     * @ORM\ManyToOne(targetEntity="Entity\AgendaDirecao", inversedBy="responsaveis")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_agenda_direcao", referencedColumnName="id_agenda_direcao")
     * })
     */
    private $agendaDirecao;

    /**
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }
    
    /**
     * @return string
     */
    public function getResponsavel()
    {
        return $this->responsavel;
    }

    /**
     * @return \Entity\AgendaDirecao
     */
    public function getAgendaDirecao()
    {
        return $this->agendaDirecao;
    }

    /**
     * @param string $responsavel
     */
    public function setResponsavel($responsavel)
    {
        $this->responsavel = $responsavel;
    }

    /**
     * @param \Entity\AgendaDirecao $agendaDirecao
     * @return \Entity\AgendaDirecaoResponsavel
     */
    function setAgendaDirecao(\Entity\AgendaDirecao $agendaDirecao) {
        $this->agendaDirecao = $agendaDirecao;
        return $this;
    }
    
    /**
     * @return string
     */
    public function getLabel() {
        return $this->getResponsavel();
    }
    
    /**
     * @return array
     */
    public function toArray()
    {
        return array(
            "id" => $this->getId(),
            "responsavel" => $this->getResponsavel(),
            "agendaDirecao" => $this->getAgendaDirecao(),
        );
    }
}
    
