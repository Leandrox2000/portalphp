<?php
namespace Entity;

/**
 * Classe base de todas as entidades
 *
 * @author Luciano
 */
interface EntityInterface
{
    public function getId();
    public function getLabel();
    public function toArray();
}
