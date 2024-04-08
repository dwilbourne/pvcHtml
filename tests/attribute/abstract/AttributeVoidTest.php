<?php

/**
 * @author: Doug Wilbourne (dougwilbourne@gmail.com)
 */

declare (strict_types=1);

namespace pvcTests\html\attribute\abstract;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use pvc\html\attribute\abstract\AttributeVoid;
use pvc\html\err\InvalidAttributeValueException;
use pvc\interfaces\validator\ValTesterInterface;

class AttributeVoidTest extends TestCase
{
    protected string $name;

    protected ValTesterInterface|MockObject $tester;

    protected AttributeVoid $attribute;

    public function setUp(): void
    {
        $this->name = 'hidden';
        $this->tester = $this->createMock(ValTesterInterface::class);
        $this->tester->method('testValue')->willReturn(true);
        $this->attribute = new AttributeVoid($this->tester);
        $this->attribute->setName($this->name);
    }

    /**
     * testGetValueReturnsTrueByDefault
     * @covers \pvc\html\attribute\abstract\AttributeVoid::getValue
     */
    public function testGetValueReturnsTrueByDefault(): void
    {
        self::assertTrue($this->attribute->getValue());
    }

    /**
     * testSetValueThrowsExceptionWithBadValue
     * @throws InvalidAttributeValueException
     * @covers \pvc\html\attribute\abstract\AttributeVoid::setValue
     */
    public function testSetValueThrowsExceptionWithBadValue(): void
    {
        self::expectException(InvalidAttributeValueException::class);
        /**
         * supposed to be boolean
         */
        $badValue = 'string';
        $this->attribute->setValue($badValue);
    }

    /**
     * testSetGetValue
     * @covers \pvc\html\attribute\abstract\AttributeVoid::setValue
     * @covers \pvc\html\attribute\abstract\AttributeVoid::getValue
     */
    public function testSetGetValue(): void
    {
        $usage = false;
        $this->attribute->setValue($usage);
        self::assertEquals($usage, $this->attribute->getValue());
    }

    /**
     * testRenderReturnsEmptyStringWhenUsageSetToFalse
     * @throws \pvc\html\err\InvalidAttributeValueException
     * @covers \pvc\html\attribute\abstract\AttributeVoid::render
     */
    public function testRenderReturnsEmptyStringWhenUsageSetToFalse(): void
    {
        $usage = false;
        $this->attribute->setValue($usage);
        self::assertEmpty($this->attribute->render());
    }

    /**
     * testRenderReturnsAttributeNameWhenUsageSetToTrue
     * @covers \pvc\html\attribute\abstract\AttributeVoid::render
     */
    public function testRenderReturnsAttributeNameWhenUsageSetToTrue(): void
    {
        $expectedOutput = $this->name;
        $this->attribute->setValue(true);
        self::assertEquals($expectedOutput, $this->attribute->render());
    }
}
