<?php

/**
 * @author: Doug Wilbourne (dougwilbourne@gmail.com)
 */
declare(strict_types=1);

namespace pvc\html\attribute;

use pvc\html\err\InvalidNumberOfParametersException;

/**
 * Class AttributeSingleValue
 */
class AttributeSingleValue extends Attribute
{
    /**
     * @param array<string|int|bool> $values
     *
     * @return string|int|bool
     * @throws InvalidNumberOfParametersException
     */
    protected function confirmParameterCount(array $values): string|int|bool
    {
        if (count($values) != 1) {
            throw new InvalidNumberOfParametersException('1');
        }
        return $values[0];
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
            assert(is_string($value) || is_integer($value));
            return $this->getName() . "=" . $value;
        }
    }
}
