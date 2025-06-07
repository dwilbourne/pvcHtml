<?php

/**
 * @author: Doug Wilbourne (dougwilbourne@gmail.com)
 */
declare(strict_types=1);

namespace pvc\html\attribute;

use pvc\html\err\InvalidCustomDataNameException;
use pvc\interfaces\html\attribute\AttributeCustomDataInterface;

/**
 * Class AttributeCustomData
 */
class AttributeCustomData extends AttributeSingleValue implements AttributeCustomDataInterface
{
    /**
     * setName
     *
     * @param  string  $name
     *
     * @throws InvalidCustomDataNameException
     * Custom attributes are stored in the element with the prefix so
     * that we can allow an element to have an 'href' attribute and a 'data-href' attribute.
     */
    public function setName(string $name): void
    {
        /**
         * according to various online sources, the data attribute id must be at least one character long and must
         * be prefixed with 'data-'. It should not contain any uppercase letters.  The isValidAttributeIdName method
         * regex restricts it to lower case letters and numbers
         */
        if (!str_starts_with($name, 'data-')) {
            throw new InvalidCustomDataNameException($name);
        }

        $suffix = substr($name, 5);
        if (!$this->isValidAttributeIdName($suffix)) {
            throw new InvalidCustomDataNameException($name);
        }
        $this->name = $name;
    }
}
