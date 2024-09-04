<?php

/**
 * @author: Doug Wilbourne (dougwilbourne@gmail.com)
 */
declare(strict_types=1);

namespace pvc\html\abstract\attribute;

use pvc\html\abstract\err\InvalidAttributeValueException;

/**
 * Class AttributeSingleValue
 * @extends Attribute<string, string>
 */
class AttributeSingleValue extends Attribute
{
    /**
     * @var string
     */
    protected mixed $value = '';

    /**
     * setValue
     * @param string $value
     * @throws InvalidAttributeValueException
     */
    public function setValue($value): void
    {
        /**
         * make sure it is a string and is not empty
         */
        if (!is_string($value) || empty($value)) {
            throw new InvalidAttributeValueException($this->getName());
        }

        /**
         * adjust to lower case if it is not case-sensitive
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
        return empty($this->value) ? '' : $this->getName() . "='" . $this->getValue() . "'";
    }
}