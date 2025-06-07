<?php

/**
 * @author: Doug Wilbourne (dougwilbourne@gmail.com)
 */
declare(strict_types=1);

namespace pvc\html\attribute;

use pvc\html\err\InvalidAttributeValueException;
use pvc\html\err\InvalidNumberOfParametersException;

/**
 * Class AttributeMultiValue
 */
class AttributeMultiValue extends Attribute
{
    /**
     * @param  array<string|int>  $values
     *
     * @return array<string|int>
     * @throws InvalidNumberOfParametersException
     */
    protected function confirmParameterCount(array $values): array
    {
        if (count($values) < 1) {
            throw new InvalidNumberOfParametersException('>=1');
        }
        return $values;
    }

    /**
     * render
     * @return string
     */
    public function render(): string
    {
        if (is_null($value = $this->getValue())) {
            return '';
        } else {
            assert(is_array($value));
            return $this->getName() . "=" . implode(' ', $value);
        }
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
     * @return array<string|int>
     */
    public function getValues(): array
    {
        $result = $this->getValue();
        assert(is_array($result));
        return $result;
    }
}
