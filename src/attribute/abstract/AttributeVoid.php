<?php

/**
 * @author: Doug Wilbourne (dougwilbourne@gmail.com)
 */
declare(strict_types=1);

namespace pvc\html\attribute\abstract;

use pvc\html\err\InvalidAttributeValueException;
use pvc\html\err\UnsetAttributeNameException;
use pvc\interfaces\html\attribute\AttributeInterface;

/**
 * Class AttributeVoid
 * @implements AttributeInterface<bool>
 */
class AttributeVoid extends Attribute implements AttributeInterface
{
    /**
     * set default value to true so that if name is set, attribute is rendered by default
     */
    protected mixed $value = true;

    /**
     * render
     * @return string
     */
    function render(): string
    {
        if (empty($this->getName())) {
            throw new UnsetAttributeNameException();
        }

        return $this->getValue() ? $this->getName() : '';
    }

    /**
     * setValue
     * @param bool $value
     */
    function setValue(mixed $value): void
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
    function getValue(): mixed
    {
        return $this->value;
    }
}