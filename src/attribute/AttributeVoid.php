<?php

namespace pvc\html\attribute;

class AttributeVoid extends AttributeSingleValue
{

    /**
     * render
     * @return string
     */
    public function render(): string
    {
        return ($this->getValue()) ? $this->getName() : '';
    }
}