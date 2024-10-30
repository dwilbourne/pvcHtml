<?php

/**
 * @author: Doug Wilbourne (dougwilbourne@gmail.com)
 */
declare(strict_types=1);

namespace pvc\html\attribute;

use pvc\html\err\InvalidCustomDataNameException;

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
    public function setName(string $name): void
    {
        /**
         * according to various online sources, the data attribute id must be at least one character long and must
         * be prefixed with 'data-'. It should not contain any uppercase letters.  This regex restricts it to lower
         * case letters and numbers
         */
        if (!$this->isValidAttributeIdName($name)) {
            throw new InvalidCustomDataNameException();
        }
        $this->name = 'data-' . $name;
    }
}
