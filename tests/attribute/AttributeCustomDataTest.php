<?php

/**
 * @author: Doug Wilbourne (dougwilbourne@gmail.com)
 */

declare (strict_types=1);

namespace pvcTests\html\abstract\attribute;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use pvc\html\abstract\attribute\AttributeCustomData;
use pvc\html\abstract\err\InvalidCustomDataNameException;
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
     * @covers \pvc\html\abstract\attribute\AttributeCustomData::__construct
     * @throws InvalidCustomDataNameException
     * @covers \pvc\html\abstract\attribute\AttributeCustomData::setName
     * @covers \pvc\html\abstract\attribute\AttributeCustomData::getName
     */
    public function testConstruct(): void
    {
        $customDataName = 'foo';
        $attribute = new AttributeCustomData($customDataName, $this->valTester);
        self::assertInstanceOf(AttributeCustomData::class, $attribute);
        $expectedResult = 'data-' . $customDataName;
        self::assertEquals($expectedResult, $attribute->getName());
    }

    /**
     * testSetNameFailsWithInvalidCustomDataName
     * @throws InvalidCustomDataNameException
     * @covers \pvc\html\abstract\attribute\AttributeCustomData::setName
     */
    public function testSetNameFailsWithInvalidCustomDataName(): void
    {
        /**
         * must be lower case and/or numbers
         */
        $customDataName = 'HOB!@';
        self::expectException(InvalidCustomDataNameException::class);
        $attribute = new AttributeCustomData($customDataName, $this->valTester);
    }
}
