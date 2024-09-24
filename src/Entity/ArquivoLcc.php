<?php

namespace Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ArquivoLcc
 *
 * @ORM\Table(name="tb_arquivo_lcc", uniqueConstraints={@ORM\UniqueConstraint(name="uk_arquivo_lcc_id_arquivo_lcc", columns={"id_arquivo_lcc"})}, indexes={@ORM\Index(name="IDX_F249998E927CB61A", columns={"id_licitacao_convenio_contrato"})})
 * @ORM\Entity(repositoryClass="Entity\Repository\ArquivoLccRepository")
 */
class ArquivoLcc extends AbstractEntity implements EntityInterface
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id_arquivo_lcc", type="bigint", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\SequenceGenerator(sequenceName="tb_arquivo_lcc_id_arquivo_lcc_seq", allocationSize=1, initialValue=1)
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="no_arquivo", type="string", length=255, nullable=false)
     */
    private $nome;
    
    /**
     * @var string
     *
     * @ORM\Column(name="no_original", type="string", length=255, nullable=false)
     */
    private $nomeOriginal;

    /**
     * @var \Entity\LicitacaoConvenioContrato
     *
     * @ORM\ManyToOne(targetEntity="Entity\LicitacaoConvenioContrato")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_licitacao_convenio_contrato", referencedColumnName="id_licitacao_convenio_contrato")
     * })
     */
    private $licitacaoConvenioContrato;
    
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
     * @return \Entity\LicitacaoConvenioContrato
     */
    public function getNome()
    {
        return $this->nome;
    }

    public function getLicitacaoConvenioContrato()
    {
        return $this->licitacaoConvenioContrato;
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
     * @param String $nome
     */
    public function setNome($nome)
    {
        $this->nome = $nome;
    }

    /**
     * 
     * @param \Entity\LicitacaoConvenioContrato $licitacaoConvenioContrato
     */
    public function setLicitacaoConvenioContrato(\Entity\LicitacaoConvenioContrato $licitacaoConvenioContrato)
    {
        $this->licitacaoConvenioContrato = $licitacaoConvenioContrato;
    }

    /**
     * 
     * @return String
     */
    public function getNomeOriginal()
    {
        return $this->nomeOriginal;
    }

    /**
     * 
     * @param String $nomeOriginal
     */
    public function setNomeOriginal($nomeOriginal)
    {
        $this->nomeOriginal = $nomeOriginal;
    }

        
    /**
     * 
     * @return String
     */
    public function getLabel(){
        return $this->getNome();
    }
    
    /**
     * 
     * @return array
     */
    public function toArray(){
        return array(
          "id" => $this->getId(),
          "nome" => $this->getNome(),
          "nomeOriginal" => $this->getNomeOriginal(),
          "licitacaoConvenioContrato" => $this->getLicitacaoConvenioContrato()
        );
    }

    

}
