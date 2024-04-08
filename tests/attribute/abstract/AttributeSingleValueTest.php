<?php

/**
 * @author: Doug Wilbourne (dougwilbourne@gmail.com)
 */

declare (strict_types=1);

namespace pvcTests\html\attribute\abstract;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use pvc\html\attribute\abstract\AttributeSingleValue;
use pvc\html\err\InvalidAttributeValueException;
use pvc\interfaces\validator\ValTesterInterface;

class AttributeSingleValueTest extends TestCase
{
    protected string $name;

    protected ValTesterInterface|MockObject $tester;

    protected AttributeSingleValue $attribute;

    public function setUp(): void
    {
        $this->name = 'target';
        $this->tester = $this->createMock(ValTesterInterface::class);
        $this->attribute = new AttributeSingleValue($this->tester);
        $this->attribute->setName($this->name);
    }

    /**
     * testSetValueThrowsExceptionWhenTesterFails
     * @throws InvalidAttributeValueException
     * @covers \pvc\html\attribute\abstract\AttributeSingleValue::setValue
     */
    public function testSetValueThrowsExceptionWhenTesterFails(): void
    {
        $value = 'bar';
        $this->tester->method('testValue')->willReturn(false);
        self::expectException(InvalidAttributeValueException::class);
        $this->attribute->setValue($value);
    }

    /**
     * testSetGetValue
     * @throws \pvc\html\err\InvalidAttributeValueException
     * @covers \pvc\html\attribute\abstract\AttributeSingleValue::setValue
     * @covers \pvc\html\attribute\abstract\AttributeSingleValue::getValue
     * @covers \pvc\html\attribute\abstract\Attribute::testValue
     */
    public function testSetGetValueWithValTesterSet(): void
    {
        $value = 'bar';
        $this->tester->method('testValue')->willReturn(true);
        $this->attribute->setValue($value);
        self::assertEquals($value, $this->attribute->getValue());
    }

    /**
     * testRenderWithNoValueSet
     * @covers \pvc\html\attribute\abstract\AttributeSingleValue::render
     */
    public function testRenderWithNoValueSet(): void
    {
        self::assertEquals('', $this->attribute->render());
    }

    /**
     * testRenderWithValueSet
     * @throws InvalidAttributeValueException
     * @covers \pvc\html\attribute\abstract\AttributeSingleValue::render
     */
    public function testRenderWithValueSet(): void
    {
        $value = 'bar\'s';
        $this->tester->method('testValue')->willReturn(true);
        $this->attribute->setValue($value);
        $expectedRendering = $this->name . '=\'bar\'s\'';
        self::assertEquals($expectedRendering, $this->attribute->render());
    }
}
