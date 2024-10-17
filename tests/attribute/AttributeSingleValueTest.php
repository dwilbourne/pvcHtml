<?php

/**
 * @author: Doug Wilbourne (dougwilbourne@gmail.com)
 */

declare (strict_types=1);

namespace pvcTests\html\abstract\attribute;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use pvc\html\abstract\attribute\AttributeSingleValue;
use pvc\html\abstract\err\InvalidAttributeValueException;
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
        $this->attribute = new AttributeSingleValue($this->name, $this->tester);
    }


    /**
     * testSetValueConvertsCaseIfNotCaseSensitive
     * @throws InvalidAttributeValueException
     * @covers \pvc\html\abstract\attribute\AttributeSingleValue::setValue
     */
    public function testSetValueConvertsCaseIfNotCaseSensitive(): void
    {
        $value = 'FOO';
        $this->attribute->setCaseSensitive(true);
        $this->tester->method('testValue')->willReturn(true);
        $this->attribute->setValue($value);
        self::assertEquals($value, $this->attribute->getValue());

        $value = 'FOO';
        $this->attribute->setCaseSensitive(false);
        $this->tester->method('testValue')->willReturn(true);
        $this->attribute->setValue($value);
        self::assertEquals(strtolower($value), $this->attribute->getValue());
    }

    /**
     * testSetValueThrowsExceptionWhenTesterFails
     * @throws InvalidAttributeValueException
     * @covers \pvc\html\abstract\attribute\AttributeSingleValue::setValue
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
     * @throws InvalidAttributeValueException
     * @covers \pvc\html\abstract\attribute\AttributeSingleValue::setValue
     * @covers \pvc\html\abstract\attribute\AttributeSingleValue::getValue
     */
    public function testSetGetValue(): void
    {
        $value = 'bar';
        $this->tester->method('testValue')->willReturn(true);
        $this->attribute->setValue($value);
        self::assertEquals($value, $this->attribute->getValue());
    }

    /**
     * testRenderWithNoValueSet
     * @covers \pvc\html\abstract\attribute\AttributeSingleValue::render
     */
    public function testRenderWithNoValueSet(): void
    {
        self::expectException(InvalidAttributeValueException::class);
        $this->attribute->render();
    }

    /**
     * testRenderWithValueSet
     * @throws InvalidAttributeValueException
     * @covers \pvc\html\abstract\attribute\AttributeSingleValue::render
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
