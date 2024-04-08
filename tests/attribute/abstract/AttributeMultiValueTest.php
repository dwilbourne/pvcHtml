<?php

/**
 * @author: Doug Wilbourne (dougwilbourne@gmail.com)
 */

declare (strict_types=1);

namespace pvcTests\html\attribute\abstract;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use pvc\html\attribute\abstract\AttributeMultiValue;
use pvc\html\err\InvalidAttributeValueException;
use pvc\interfaces\validator\ValTesterInterface;

class AttributeMultiValueTest extends TestCase
{
    protected ValTesterInterface|MockObject $tester;

    protected AttributeMultiValue $attribute;

    protected string $attributeName;

    public function setUp(): void
    {
        $this->attributeName = 'class';
        $this->tester = $this->createMock(ValTesterInterface::class);
        $this->attribute = new AttributeMultiValue($this->tester);
        $this->attribute->setName($this->attributeName);
    }

    /**
     * testSetValues
     * @throws \pvc\html\err\InvalidAttributeValueException
     * @covers \pvc\html\attribute\abstract\AttributeMultiValue::setValue
     * @covers \pvc\html\attribute\abstract\AttributeMultiValue::getValue
     */
    public function testSetValuesWithTesterSet(): void
    {
        $this->tester->method('testValue')->willReturn(true);
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
        $this->tester->method('testValue')->willReturn(false);
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
        $this->tester->method('testValue')->willReturn(true);
        $this->attribute->setValue($testValues);
        $expectedOutput = $this->attributeName . '=\'bar\'';
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
        $this->tester->method('testValue')->willReturn(true);
        $this->attribute->setValue($testValues);
        $expectedOutput = $this->attributeName . '=\'bar baz quux\'';
        self::assertEquals($expectedOutput, $this->attribute->render());
    }
}
