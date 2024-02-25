<?php

/**
 * @author: Doug Wilbourne (dougwilbourne@gmail.com)
 */
declare (strict_types=1);

namespace pvcTests\html\attribute\abstract;

use PHPUnit\Framework\TestCase;
use pvc\html\attribute\abstract\AttributeMultiValue;
use pvc\html\err\InvalidAttributeValueException;
use pvc\interfaces\validator\ValTesterInterface;

class AttributeMultiValueTest extends TestCase
{
    protected AttributeMultiValue $attribute;

    public function setUp(): void
    {
        $testName = 'foo';
        $this->attribute = new AttributeMultiValue($testName);
    }

    /**
     * testSetValuesWithNoTesterSet
     * @throws \pvc\html\err\InvalidAttributeValueException
     * @covers \pvc\html\attribute\abstract\AttributeMultiValue::setValue
     * @covers \pvc\html\attribute\abstract\AttributeMultiValue::getValue
     */
    public function testSetValuesWithNoTesterSet(): void
    {
        $testValues = ['bar', 'baz', 'quux'];
        $this->attribute->setValue($testValues);
        self::assertEquals($testValues, $this->attribute->getValue());
    }

    /**
     * testSetValuesWithTesterSet
     * @throws \pvc\html\err\InvalidAttributeValueException
     * @covers \pvc\html\attribute\abstract\AttributeMultiValue::setValue
     * @covers \pvc\html\attribute\abstract\AttributeMultiValue::getValue
     */
    public function testSetValuesWithTesterSet(): void
    {
        $tester = $this->createMock(ValTesterInterface::class);
        $tester->method('testValue')->willReturn(true);
        $this->attribute->setTester($tester);
        $testValues = ['bar', 'baz', 'quux'];
        $this->attribute->setValue($testValues);
        self::assertEquals($testValues, $this->attribute->getValue());
    }

    /**
     * testSetValuesThrowsExceptionWhenTesterFails
     * @throws InvalidAttributeValueException
     * @covers \pvc\html\attribute\abstract\AttributeMultiValue::setValue
     * @covers \pvc\html\attribute\abstract\AttributeMultiValue::getValue
     */
    public function testSetValuesThrowsExceptionWhenTesterFails(): void
    {
        $tester = $this->createMock(ValTesterInterface::class);
        $tester->method('testValue')->willReturn(false);
        $this->attribute->setTester($tester);
        $testValues = ['bar', 'baz', 'quux'];
        self::expectException(InvalidAttributeValueException::class);
        $this->attribute->setValue($testValues);
    }

    /**
     * testRenderWithNoValueSet
     * @covers \pvc\html\attribute\abstract\AttributeMultiValue::render
     */
    public function testRenderWithNoValueSet(): void
    {
        self::assertEquals('', $this->attribute->render());
    }

    /**
     * testRenderWithValuesSet
     * @throws InvalidAttributeValueException
     * @covers \pvc\html\attribute\abstract\AttributeMultiValue::render
     */
    public function testRenderWithOneValueSet(): void
    {
        $testValues = ['bar'];
        $this->attribute->setValue($testValues);
        $expectedOutput = 'foo=\'bar\'';
        self::assertEquals($expectedOutput, $this->attribute->render());
    }

    /**
     * testRenderWithValuesSet
     * @throws InvalidAttributeValueException
     * @covers \pvc\html\attribute\abstract\AttributeMultiValue::render
     */
    public function testRenderWithMultipleValuesSet(): void
    {
        $testValues = ['bar', 'baz', 'quux'];
        $this->attribute->setValue($testValues);
        $expectedOutput = 'foo=\'bar baz quux\'';
        self::assertEquals($expectedOutput, $this->attribute->render());
    }
}
