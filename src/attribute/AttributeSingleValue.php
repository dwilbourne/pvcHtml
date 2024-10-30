<?php

/**
 * @author: Doug Wilbourne (dougwilbourne@gmail.com)
 */
declare(strict_types=1);

namespace pvc\html\attribute;

use pvc\html\err\InvalidAttributeValueException;
use pvc\html\err\InvalidNumberOfParametersException;
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
     * @param string ...$values
     * @throws InvalidAttributeValueException
     */
    public function setValue(...$values): void
    {
        /**
         * should be a single value
         */
        if(count($values) != 1) {
            throw new InvalidNumberOfParametersException('1');
        } else {
            $value = $values[0];
        }

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
            throw new InvalidAttributeValueException();
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
        return $this->getName() . "='" . $this->getValue() . "'";
    }
}