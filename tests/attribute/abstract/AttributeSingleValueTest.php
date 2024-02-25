<?php

/**
 * @author: Doug Wilbourne (dougwilbourne@gmail.com)
 */
declare (strict_types=1);

namespace pvcTests\html\attribute\abstract;

use PHPUnit\Framework\TestCase;
use pvc\html\attribute\abstract\AttributeSingleValue;
use pvc\html\err\InvalidAttributeValueException;
use pvc\interfaces\validator\ValTesterInterface;

class AttributeSingleValueTest extends TestCase
{
    protected AttributeSingleValue $attribute;

    public function setUp(): void
    {
        $testName = 'foo';
        $this->attribute = new AttributeSingleValue($testName);
    }

    /**
     * testSetGetValueWithNoValTesterSet
     * @throws \pvc\html\err\InvalidAttributeValueException
     * @covers \pvc\html\attribute\abstract\AttributeSingleValue::setValue
     * @covers \pvc\html\attribute\abstract\AttributeSingleValue::getValue
     * @covers \pvc\html\attribute\abstract\Attribute::testValue
     */
    public function testSetGetValueWithNoValTesterSet(): void
    {
        self::assertNull($this->attribute->getValue());

        $value = 'bar';
        $this->attribute->setValue($value);
        self::assertEquals($value, $this->attribute->getValue());
    }

    /**
     * testSetGetValueWithValTesterSet
     * @throws \pvc\html\err\InvalidAttributeValueException
     * @covers \pvc\html\attribute\abstract\AttributeSingleValue::setValue
     * @covers \pvc\html\attribute\abstract\AttributeSingleValue::getValue
     * @covers \pvc\html\attribute\abstract\Attribute::testValue
     */
    public function testSetGetValueWithValTesterSet(): void
    {
        $value = 'bar';
        $valTester = $this->createMock(ValTesterInterface::class);
        $valTester->method('testValue')->willReturn(true);
        $this->attribute->setTester($valTester);
        $this->attribute->setValue($value);
        self::assertEquals($value, $this->attribute->getValue());
    }

    /**
     * testSetValueThrowsExceptionWhenTesterFails
     * @throws InvalidAttributeValueException
     * @covers \pvc\html\attribute\abstract\AttributeSingleValue::setValue
     */
    public function testSetValueThrowsExceptionWhenTesterFails(): void
    {
        $value = 'bar';
        $valTester = $this->createMock(ValTesterInterface::class);
        $valTester->method('testValue')->willReturn(false);
        $this->attribute->setTester($valTester);
        self::expectException(InvalidAttributeValueException::class);
        $this->attribute->setValue($value);
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
        $this->attribute->setValue($value);
        $expectedRendering = 'foo=\'bar\'s\'';
        self::assertEquals($expectedRendering, $this->attribute->render());
    }

}
