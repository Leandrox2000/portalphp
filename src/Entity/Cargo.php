<?php
namespace Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Cargo
 *
 * @ORM\Table(name="tb_cargo", uniqueConstraints={@ORM\UniqueConstraint(name="UK_cargo_no_cargo", columns={"no_cargo"}), @ORM\UniqueConstraint(name="UK_cargo_id_cargo", columns={"id_cargo"})})
 * @ORM\Entity(repositoryClass="Entity\Repository\CargoRepository")
 */
class Cargo extends AbstractEntity implements EntityInterface
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id_cargo", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="no_cargo", type="string", length=255, nullable=false)
     */
    private $cargo;

    /**
     * 
     * @return integer
     */
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
     * @return string
     */
    public function getCargo()
    {
        return $this->cargo;
    }

    /**
     * 
     * @param string $cargo
     */
    public function setCargo($cargo)
    {
        $this->cargo = $cargo;
    }

    /**
     * 
     * @return string
     */
    public function getLabel()
    {
        return $this->getCargo();
    }

    public function toArray()
    {
        return array(
            'id' => $this->getId(),
            'cargo' => $this->getCargo(),
        );
    }

}
