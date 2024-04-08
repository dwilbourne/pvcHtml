<?php

/**
 * @author: Doug Wilbourne (dougwilbourne@gmail.com)
 */
declare(strict_types=1);

namespace pvc\html\attribute\abstract;

use pvc\html\err\InvalidAttributeValueException;
use pvc\interfaces\html\attribute\AttributeSingleValueInterface;

/**
 * Class AttributeSingleValue
 */
class AttributeSingleValue extends Attribute implements AttributeSingleValueInterface
{
    /**
     * @var string
     */
    protected string $value;

    /**
     * setValue
     * @param string $value
     * @throws InvalidAttributeValueException
     */
    public function setValue($value): void
    {
        if (is_string($value) && $this->testValue($value)) {
            $this->value = $value;
        } else {
            throw new InvalidAttributeValueException($this->getName(), $value);
        }
    }

    /**
     * getValue
     * @return string|null
     */
    public function getValue(): mixed
    {
        return $this->value ?? null;
    }

    /**
     * render
     * @return string
     */
    public function render(): string
    {
        if (!empty($this->value)) {
            return $this->name . "='" . $this->value . "'";
        } else {
            return '';
        }
    }
}