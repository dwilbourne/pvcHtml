<?php

/**
 * @author: Doug Wilbourne (dougwilbourne@gmail.com)
 */
declare(strict_types=1);

namespace pvc\html\attribute\abstract;

use pvc\html\err\InvalidAttributeValueException;
use pvc\html\err\UnsetAttributeNameException;
use pvc\interfaces\html\attribute\AttributeSingleValueInterface;

/**
 * Class AttributeSingleValue
 * @extends Attribute<string>
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
        if (!$this->valueIsCaseSensitive()) {
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
     * @return string|null
     */
    public function getValue(): string|null
    {
        return $this->value;
    }

    /**
     * render
     * @return string
     */
    public function render(): string
    {
        if (empty($this->getName())) {
            throw new UnsetAttributeNameException();
        }

        if (empty($this->value)) {
            return '';
        }

        return $this->name . "='" . $this->value . "'";
    }
}