<?php

/**
 * @author: Doug Wilbourne (dougwilbourne@gmail.com)
 */

declare (strict_types=1);

namespace pvcTests\html\attribute\abstract;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use pvc\html\attribute\abstract\AttributeCustomData;
use pvc\html\err\InvalidCustomDataNameException;
use pvc\interfaces\validator\ValTesterInterface;

class AttributeCustomDataTest extends TestCase
{
    protected AttributeCustomData $attribute;

    protected ValTesterInterface|MockObject $customDataNameTester;

    protected ValTesterInterface $valTester;

    public function setUp(): void
    {
        $this->valTester = $this->createMock(ValTesterInterface::class);
        $this->customDataNameTester = $this->createMock(ValTesterInterface::class);
        $this->attribute = new AttributeCustomData($this->customDataNameTester, $this->valTester);
    }

    /**
     * testConstruct
     * @covers \pvc\html\attribute\abstract\AttributeCustomData::__construct
     */
    public function testConstruct(): void
    {
        self::assertInstanceOf(AttributeCustomData::class, $this->attribute);
    }

    /**
     * testSetNameFailsWithInvalidCustomDataName
     * @throws InvalidCustomDataNameException
     * @covers \pvc\html\attribute\abstract\AttributeCustomData::setName
     */
    public function testSetNameFailsWithInvalidCustomDataName(): void
    {
        $customDataName = 'foo';
        $this->customDataNameTester->expects($this->once())->method('testValue')->with($customDataName)->willReturn(false);
        self::expectException(InvalidCustomDataNameException::class);
        $this->attribute->setName($customDataName);
    }

    /**
     * testSetNameSucceeds
     * @throws InvalidCustomDataNameException
     * @covers \pvc\html\attribute\abstract\AttributeCustomData::setName
     * @covers \pvc\html\attribute\abstract\AttributeCustomData::getName
     */
    public function testSetNameSucceeds(): void
    {
        $customDataName = 'foo';
        $this->customDataNameTester->expects($this->once())->method('testValue')->with($customDataName)->willReturn(true);
        $this->attribute->setName($customDataName);
        $expectedResult = 'data-' . $customDataName;
        self::assertEquals($expectedResult, $this->attribute->getName());
    }


}
