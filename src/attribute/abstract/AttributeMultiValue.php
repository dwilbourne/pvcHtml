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
 * @extends Attribute<array<string>>
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
    public function setValue(mixed $value): void
    {
        foreach ($value as $arrayItem) {
            if (!$this->testValue(($arrayItem))) {
                throw new InvalidAttributeValueException($this->getName(), $arrayItem);
            }
            $this->values[] = $arrayItem;
        }
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
            return "";
        }
    }

    /**
     * getValues
     * @return array<string>
     */
    public function getValue(): mixed
    {
        return $this->values;
    }
}
