<?php

/**
 * @author: Doug Wilbourne (dougwilbourne@gmail.com)
 */
declare(strict_types=1);

namespace pvc\html\abstract\attribute;

use pvc\html\abstract\err\InvalidCustomDataNameException;

/**
 * Class AttributeCustomData
 */
class AttributeCustomData extends AttributeSingleValue
{
    /**
     * setName
     * @param string $name
     * @throws InvalidCustomDataNameException
     * Custom attributes are stored in the attributes array of a tag with the prefix so
     * that we can allow a tag to have an 'href' attribute and a 'data-href' attribute.
     */
    protected function setName(string $name): void
    {
        /**
         * according to various online sources, the data attribute name must be at least one character long and must
         * be prefixed with 'data-'. It should not contain any uppercase letters.  This regex restricts it to lower
         * case letters and numbers
         */
        if (!$this->isValidAttributeName($name)) {
            throw new InvalidCustomDataNameException();
        }
        $this->name = 'data-' . $name;
    }
}
