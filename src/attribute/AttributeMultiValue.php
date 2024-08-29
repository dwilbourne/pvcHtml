<?php

/**
 * @author: Doug Wilbourne (dougwilbourne@gmail.com)
 */
declare(strict_types=1);

namespace pvc\html\attribute;

use pvc\html\err\InvalidAttributeValueException;

/**
 * Class AttributeMultiValue
 * @extends Attribute<array<string>, string>
 */
class AttributeMultiValue extends Attribute
{
    /**
     * @var array<string>
     */
    protected array $values = [];


    /**
     * setValues
     * @param array<string> $value
     * @throws InvalidAttributeValueException
     */
    public function setValue($value): void
    {
        $values = [];

        /**
         * make sure it is an array and is not empty
         */
        if (!is_array($value) || empty($value)) {
            throw new InvalidAttributeValueException($this->getName());
        }

        foreach ($value as $arrayItem) {
            /**
             * each element must be a string
             */
            if (!is_string($arrayItem)) {
                throw new InvalidAttributeValueException($this->getName());
            }
            /**
             * convert to lower case if not case-sensitive
             */
            if (!$this->valueIsCaseSensitive()) {
                $arrayItem = strtolower($arrayItem);
            }
            /**
             * test the array element
             */
            if (!$this->getTester()->testValue(($arrayItem))) {
                throw new InvalidAttributeValueException($this->getName());
            }
            $values[] = $arrayItem;
        }
        /**
         * set the values
         */
        $this->values = $values;
    }

    /**
     * getValues
     * @return array<string>
     */
    public function getValue(): array
    {
        return $this->values;
    }

    /**
     * render
     * @return string
     */
    public function render(): string
    {
        if (!empty($this->getValue())) {
            return $this->name . "='" . implode(' ', $this->values) . "'";
        } else {
            return '';
        }
    }
}
