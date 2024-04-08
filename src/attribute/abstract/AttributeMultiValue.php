<?php

/**
 * @author: Doug Wilbourne (dougwilbourne@gmail.com)
 */
declare(strict_types=1);

namespace pvc\html\attribute\abstract;

use pvc\html\err\InvalidAttributeValueException;
use pvc\interfaces\html\attribute\AttributeMultiValueInterface;

/**
 * Class AttributeMultiValue
 */
class AttributeMultiValue extends Attribute implements AttributeMultiValueInterface
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
        foreach ($value as $arrayItem) {
            if (is_string($arrayItem) && $this->testValue($arrayItem)) {
                $this->values[] = $arrayItem;
            } else {
                throw new InvalidAttributeValueException($this->getName(), $arrayItem);
            }
        }
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
    function render(): string
    {
        if (!empty($this->getValue())) {
            return $this->name . "='" . implode(' ', $this->values) . "'";
        } else {
            return '';
        }
    }

}
