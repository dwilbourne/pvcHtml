<?php

/**
 * @author: Doug Wilbourne (dougwilbourne@gmail.com)
 */
declare(strict_types=1);

namespace pvc\html\attribute;

use pvc\html\err\InvalidAttributeException;
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
         * value cannot be empty and must be validated by the tester.
         */
        if (empty($value) || !$this->getTester()->testValue($value)) {
            throw new InvalidAttributeValueException($this->getName(), $value);
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
     * __get
     * add a little fluency so you can write ->value instead of ->getValue()
     * @param string $id
     * @return string
     * @throws InvalidAttributeException
     */
    public function __get(string $id): string
    {
        if ($id == 'value') {
            return $this->getValue();
        } else {
            throw new InvalidAttributeException($id);
        }
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