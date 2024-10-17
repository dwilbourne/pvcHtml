<?php

/**
 * @author: Doug Wilbourne (dougwilbourne@gmail.com)
 */
declare(strict_types=1);

namespace pvc\html\abstract\attribute;

use pvc\html\abstract\err\InvalidAttributeValueException;
use pvc\interfaces\html\attribute\AttributeMultiValueInterface;

/**
 * Class AttributeMultiValue
 */
class AttributeMultiValue extends AttributeWithValue implements AttributeMultiValueInterface
{
    /**
     * @var array<string>
     */
    protected array $values = [];


    /**
     * setValues
     * @param array<string> $values
     * @throws InvalidAttributeValueException
     */
    public function setValues(array $values): void
    {
        /**
         * test to make sure $values is not empty
         */
        if (empty($values)) {
            throw new InvalidAttributeValueException($this->getName());
        }

        $newValues = [];
        foreach ($values as $value) {
            /**
             * set to lower case if not case-sensitive
             */
            if (!$this->isCaseSensitive()) {
                $value = strtolower($value);
            }

            /**
             * test the array element
             */
            if (!$this->getTester()->testValue(($value))) {
                throw new InvalidAttributeValueException($this->getName());
            }
            $newValues[] = $value;
        }

        /**
         * set the values
         */
        $this->values = $newValues;
    }

    /**
     * getValues
     * @return array<string>
     */
    public function getValues(): array
    {
        return $this->values;
    }

    /**
     * render
     * @return string
     */
    public function render(): string
    {
        if (empty($this->getValues())) {
            throw new InvalidAttributeValueException($this->getName());
        }
        return $this->name . "='" . implode(' ', $this->values) . "'";
    }
}
