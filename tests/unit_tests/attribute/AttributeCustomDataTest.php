<?php

/**
 * @author: Doug Wilbourne (dougwilbourne@gmail.com)
 */

declare (strict_types=1);

namespace pvcTests\html\unit_tests\attribute;

use PHPUnit\Framework\TestCase;
use pvc\html\attribute\AttributeCustomData;
use pvc\html\err\InvalidCustomDataNameException;

class AttributeCustomDataTest extends TestCase
{
    /**
     * testConstruct
     * @throws InvalidCustomDataNameException
     * @covers \pvc\html\attribute\AttributeCustomData::setName
     */
    public function testSetGetName(): void
    {
        $name = 'data-foo';
        $attribute = new AttributeCustomData($name);
        self::assertInstanceOf(AttributeCustomData::class, $attribute);
    }

    /**
     * testSetNameFailsWithInvalidCustomDataName
     * @throws InvalidCustomDataNameException
     * @covers \pvc\html\attribute\AttributeCustomData::setName
     */
    public function testSetNameFailsWithUnprefixedCustomDataName(): void
    {
        /**
         * must be lower case and/or numbers prefixed by 'data-'
         */
        $name = 'foo';
        self::expectException(InvalidCustomDataNameException::class);
        $attribute = new AttributeCustomData($name);
        unset($attribute);
    }

    /**
     * testSetNameFailsWithCustomNameThatHasIllegalCharacters
     * @throws InvalidCustomDataNameException
     * @covers \pvc\html\attribute\AttributeCustomData::setName
     */
    public function testSetNameFailsWithCustomNameThatHasIllegalCharacters(): void
    {
        /**
         * must be lower case and/or numbers prefixed by 'data-'
         */
        self::expectException(InvalidCustomDataNameException::class);
        $name = 'data-%*(HB9';
        $attribute = new AttributeCustomData($name);
        unset($attribute);
    }
}
