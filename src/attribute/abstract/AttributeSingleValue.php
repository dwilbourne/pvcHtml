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
 * @extends Attribute<string>
 */
class AttributeSingleValue extends Attribute implements AttributeSingleValueInterface
{
    /**
     * @var string
     */
    protected string $value;

    /**
     * getValue
     * @return string|null
     */
    public function getValue(): mixed
    {
        return $this->value ?? null;
    }

    /**
     * setValue
     * @param string $value
     * @throws InvalidAttributeValueException
     */
    public function setValue(mixed $value): void
    {
        if (!$this->testValue($value)) {
            throw new InvalidAttributeValueException($this->getName(), $value);
        }
        $this->value = $value;
    }

    public function render(): string
    {
        if (!empty($this->value)) {
            return $this->name . "='" . $this->value . "'";
        } else {
            return '';
        }
    }
}