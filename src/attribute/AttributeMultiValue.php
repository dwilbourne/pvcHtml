<?php

/**
 * @author: Doug Wilbourne (dougwilbourne@gmail.com)
 */
declare(strict_types=1);

namespace pvc\html\attribute;

use pvc\html\err\InvalidAttributeException;
use pvc\html\err\InvalidAttributeValueException;
use pvc\html\err\InvalidNumberOfParametersException;
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
     * @param string ...$values
     * @throws InvalidAttributeValueException
     */
    public function setValue(...$values): void
    {
        /**
         * test to make sure $values is not empty
         */
        if (empty($values)) {
            throw new InvalidNumberOfParametersException('>=1');
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
                throw new InvalidAttributeValueException($this->getName(), $value);
            }
            $newValues[] = $value;
        }

        /**
         * set the values
         */
        $this->values = $newValues;
    }

    /**
     * getValue
     * @return array<string>
     */
    public function getValue(): array
    {
        return $this->values;
    }

    /**
     * __get
     * add a little fluency so you can write ->value instead of ->getValue()
     * @param string $id
     * @return array<string>
     * @throws InvalidAttributeException
     */
    public function __get(string $id): array
    {
        if ($id == 'value' || $id == 'values') {
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
        if (empty($this->getValues())) {
            throw new InvalidAttributeValueException($this->getName(), '{null}');
        }
        return $this->name . "='" . implode(' ', $this->values) . "'";
    }

    /**
     * setValues
     * @param string ...$values
     * @throws InvalidAttributeValueException
     */
    public function setValues(...$values): void
    {
        $this->setValue(...$values);
    }

    /**
     * getValues
     * @return array<string>
     */
    public function getValues(): array
    {
        return $this->values;
    }
}
