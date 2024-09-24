<?php
namespace Entity;

class Usuario extends AbstractEntity implements EntityInterface
{
    /**
     *
     * @var integer
     */
    protected $id = 0;

    /**
     *
     * @var String
     */
    private $nome;

    /**
     *
     * @var \Entity\Site
     */
    private $site;

    public function getId()
    {
        return $this->id;
    }

    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * 
     * @return type
     */
    public function getNome()
    {
        return $this->nome;
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
     * @param string $nome
     */
    public function setNome($nome)
    {
        $this->nome = $nome;
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
     * @return string
     */
    public function getLabel()
    {
        return $this->getNome();
    }
    
    public function toArray()
    {
        return array(
            'id' => $this->getId(),
            'nome' => $this->getNome(),
            'site' => $this->getSite(),
        );
    }

}
