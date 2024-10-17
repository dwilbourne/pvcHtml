<?php

/**
 * @author: Doug Wilbourne (dougwilbourne@gmail.com)
 */
declare(strict_types=1);

namespace pvc\html\abstract\attribute;

use pvc\html\abstract\err\InvalidAttributeValueException;
use pvc\interfaces\html\attribute\AttributeSingleValueInterface;

/**
 * Class AttributeSingleValue
 */
class AttributeSingleValue extends AttributeWithValue implements AttributeSingleValueInterface
{
    /**
     * @var string
     */
    protected string $value = '';

    /**
     * setValue
     * @param string $value
     * @throws InvalidAttributeValueException
     */
    public function setValue(string $value): void
    {
        /**
         * if the value is not case-sensitive, set it to lower case
         */
        if (!$this->isCaseSensitive()) {
            $value = strtolower($value);
        }

        /**
         * test the value
         */
        if (!$this->getTester()->testValue($value)) {
            throw new InvalidAttributeValueException($this->getName());
        }

        $this->value = $value;
    }

    /**
     * getValue
     * @return string
     */
    public function getValue(): string
    {
        return $this->value;
    }

    /**
     * render
     * @return string
     */
    public function render(): string
    {
        if (empty($this->getValue())) {
            throw new InvalidAttributeValueException($this->getName());
        }
        return $this->getName() . "='" . $this->getValue() . "'";
    }
}