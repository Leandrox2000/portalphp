<?php

namespace Entity;

abstract class AbstractEntity
{

    public function __toString()
    {
        return $this->getLabel();
    }

}
