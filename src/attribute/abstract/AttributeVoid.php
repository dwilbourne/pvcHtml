<?php

/**
 * @author: Doug Wilbourne (dougwilbourne@gmail.com)
 */
declare(strict_types=1);

namespace pvc\html\attribute\abstract;

/**
 * Class AttributeVoid
 * @extends Attribute<bool>
 */
class AttributeVoid extends Attribute
{

    /**
     * @var true
     */
    protected bool $usage = true;

    /**
     * setValue
     * @param true $value
     */
    public function setValue(mixed $value): void
    {
        $this->usage = $value;
    }

    /**
     * getValue
     * @return true
     */
    public function getValue(): bool
    {
        return $this->usage;
    }

    /**
     * render
     * @return string
     */
    function render(): string
    {
        return $this->getName();
    }

}