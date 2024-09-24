<?php
namespace Entity;

/**
 * Description of Type
 */
class Type implements EntityInterface
{

    /**
     *
     * @var int 
     */
    private $id;

    /**
     *
     * @var String 
     */
    private $nome;

    /**
     * 
     * @param int $id
     * @param string $nome
     */
    public function __construct($id, $nome)
    {
        $this->setId($id);
        $this->setNome($nome);
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
     * @param int $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * 
     * @return String
     */
    public function getNome()
    {
        return $this->nome;
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
     * @return String
     */
    public function getLabel()
    {
        return $this->nome;
    }

    public function toArray()
    {
        return array(
            'id' => $this->getId(),
            'nome' => $this->getNome(),
        );
    }

}
