<?php

/**
 * @author: Doug Wilbourne (dougwilbourne@gmail.com)
 */

declare (strict_types=1);

namespace pvcTests\html\unit_tests\attribute;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use pvc\html\attribute\AttributeCustomData;
use pvc\html\err\InvalidCustomDataNameException;
use pvc\interfaces\validator\ValTesterInterface;

class AttributeCustomDataTest extends TestCase
{
    protected ValTesterInterface|MockObject $valTester;

    public function setUp(): void
    {
        $this->valTester = $this->createMock(ValTesterInterface::class);
    }

    /**
     * testConstruct
     * @throws InvalidCustomDataNameException
     * @covers \pvc\html\attribute\AttributeCustomData::setName
     * @covers \pvc\html\attribute\AttributeCustomData::getName
     */
    public function testSetGetName(): void
    {
        $name = 'data-foo';
        $attribute = new AttributeCustomData();
        $attribute->setName($name);
        $expectedResult = $name;
        self::assertEquals($expectedResult, $attribute->getName());
    }

    /**
     * testSetNameFailsWithInvalidCustomDataName
     * @throws InvalidCustomDataNameException
     * @covers \pvc\html\attribute\AttributeCustomData::setName
     */
    public function testSetNameFailsWithunprefixedCustomDataName(): void
    {
        /**
         * must be lower case and/or numbers prefixed by 'data-'
         */
        $name = 'foo';
        $attribute = new AttributeCustomData();
        self::expectException(InvalidCustomDataNameException::class);
        $attribute->setName($name);
        unset($attribute);
    }

    /**
     * testSetnameFailsWithCustomNameThatHasIllegalCharacters
     * @throws InvalidCustomDataNameException
     * @covers \pvc\html\attribute\AttributeCustomData::setName
     */
    public function testSetnameFailsWithCustomNameThatHasIllegalCharacters(): void
    {
        /**
         * must be lower case and/or numbers prefixed by 'data-'
         */
        $name = 'data-%*(HB9';
        $attribute = new AttributeCustomData();
        self::expectException(InvalidCustomDataNameException::class);
        $attribute->setName($name);
        unset($attribute);
    }
}
