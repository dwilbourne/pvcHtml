<?php

/**
 * @author: Doug Wilbourne (dougwilbourne@gmail.com)
 */
declare(strict_types=1);

namespace pvc\html\abstract\attribute;

use pvc\html\abstract\err\InvalidAttributeValueException;
use pvc\html\abstract\err\UnsetAttributeNameException;

/**
 * Class AttributeVoid
 * @extends Attribute<bool, bool>
 */
class AttributeVoid extends Attribute
{
    /**
     * set default value to true so that if name is set, attribute is rendered by default
     */
    protected mixed $value = true;

    /**
     * render
     * @return string
     */
    public function render(): string
    {
        return $this->getValue() ? $this->getName() : '';
    }

    /**
     * setValue
     * @param bool $value
     */
    public function setValue(mixed $value): void
    {
        if (!is_bool($value)) {
            throw new InvalidAttributeValueException($this->getName());
        }
        $this->value = $value;
    }

    /**
     * getValue
     * @return bool
     */
    public function getValue(): mixed
    {
        return $this->value;
    }
}