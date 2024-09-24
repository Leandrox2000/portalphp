<?php

namespace Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Hash
 *
 * @ORM\Table(name="tb_hash", uniqueConstraints={@ORM\UniqueConstraint(name="uk_hash_hash", columns={"id_hash"}), @ORM\UniqueConstraint(name="uk_hash_value", columns={"ds_value"})})
 * @ORM\Entity(repositoryClass="Entity\Repository\HashRepository")
 */
class Hash extends AbstractEntity implements EntityInterface
{

    /**
     * @var integer
     *
     * @ORM\Column(name="id_hash", type="bigint", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\SequenceGenerator(sequenceName="tb_hash_id_hash_seq", allocationSize=1, initialValue=1)
     */
    private $id;

    /**
     * @var integer
     *
     * @ORM\Column(name="ds_value", type="string", nullable=true)
     */
    private $value;

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
    public function getValue()
    {
        return $this->value;
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
     * @param string $value
     */
    public function setValue($value)
    {
        $this->value = $value;
    }

    /**
     * 
     * @return array
     */
    public function toArray()
    {
        return array(
            "id" => $this->getId(),
            "value" => $this->getValue()
        );
    }

    /**
     * 
     * @return string
     */
    public function getLabel()
    {
        return $this->getValue();
    }

}
