<?php

/**
 * @author: Doug Wilbourne (dougwilbourne@gmail.com)
 */
declare(strict_types=1);

namespace pvc\html\attribute\abstract;

use pvc\html\err\InvalidAttributeValueException;
use pvc\interfaces\html\attribute\AttributeVoidInterface;

/**
 * Class AttributeVoid
 */
class AttributeVoid extends Attribute implements AttributeVoidInterface
{
    /**
     * this class inherits a setTester method.  The tester is never actually used in this class because the
     * value can only be boolean and both true and false are valid values in terms of managing the state of the
     * state of the object.  So using setTester on this class will not result in any change in behavior.
     */

    /**
     * @var bool
     */
    protected bool $usage = true;

    /**
     * setValue
     * @param bool $value
     */
    public function setValue($value): void
    {
        if (!is_bool($value)) {
            throw new InvalidAttributeValueException($this->getName(), $value);
        }
        $this->usage = $value;
    }

    /**
     * getValue
     * @return bool
     */
    public function getValue(): mixed
    {
        return $this->usage;
    }

    /**
     * render
     * @return string
     */
    function render(): string
    {
        return ($this->usage ? $this->getName() : '');
    }
}