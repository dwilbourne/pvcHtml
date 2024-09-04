<?php

/**
 * @author: Doug Wilbourne (dougwilbourne@gmail.com)
 */

declare (strict_types=1);

namespace pvcTests\html\abstract\attribute;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use pvc\html\abstract\attribute\AttributeMultiValue;
use pvc\html\abstract\err\InvalidAttributeValueException;
use pvc\interfaces\validator\ValTesterInterface;
use stdClass;

class AttributeMultiValueTest extends TestCase
{
    protected ValTesterInterface|MockObject $tester;

    protected AttributeMultiValue $attribute;

    protected string $attributeName;

    public function setUp(): void
    {
        $this->attributeName = 'class';
        $this->tester = $this->createMock(ValTesterInterface::class);
        $this->attribute = new AttributeMultiValue($this->attributeName, $this->tester);
    }

    /**
     * testSetValueFailsIfArgumentIsNotAnArray
     * @throws InvalidAttributeValueException
     * @covers \pvc\html\abstract\attribute\AttributeMultiValue::setValue
     */
    public function testSetValueFailsIfArgumentIsNotAnArray(): void
    {
        $values = new stdClass();
        self::expectException(InvalidAttributeValueException::class);
        $this->attribute->setValue($values);
    }

    /**
     * testSetValueFailsWhenValueIsEmpty
     * @throws InvalidAttributeValueException
     * @covers \pvc\html\abstract\attribute\AttributeMultiValue::setValue
     */
    public function testSetValueFailsWhenValueIsEmpty(): void
    {
        $values = [];
        self::expectException(InvalidAttributeValueException::class);
        $this->attribute->setValue($values);
    }

    /**
     * testSetValueFailsWhenNotAllArrayElementsAreStrings
     * @throws InvalidAttributeValueException
     * @covers \pvc\html\abstract\attribute\AttributeMultiValue::setValue
     */
    public function testSetValueFailsWhenNotAllArrayElementsAreStrings(): void
    {
        $values = ['foo', 5];
        $this->tester->method('testValue')->willReturn(true);
        self::expectException(InvalidAttributeValueException::class);
        $this->attribute->setValue($values);
    }

    /**
     * testSetValueConvertsValuesToLowerCaseIfCaseSensitive
     * @throws InvalidAttributeValueException
     * @covers \pvc\html\abstract\attribute\AttributeMultiValue::setValue
     */
    public function testSetValueConvertsValuesToLowerCaseIfCaseSensitive(): void
    {
        $values = ['FOO', 'BAR'];
        $this->attribute->setCaseSensitive(true);
        $this->tester->method('testValue')->willReturn(true);
        $this->attribute->setValue($values);
        self::assertEquals($values, $this->attribute->getValue());

        $values = ['FOO', 'BAR'];
        $this->attribute->setCaseSensitive(false);
        $this->tester->method('testValue')->willReturn(true);
        $this->attribute->setValue($values);
        $expectedResult = array_map('strtolower', $values);
        self::assertEquals($expectedResult, $this->attribute->getValue());
    }

    /**
     * testSetGetValue
     * @throws InvalidAttributeValueException
     * @covers \pvc\html\abstract\attribute\AttributeMultiValue::setValue
     * @covers \pvc\html\abstract\attribute\AttributeMultiValue::getValue
     */
    public function testSetGetValue(): void
    {
        $this->tester->method('testValue')->willReturn(true);
        $testValues = ['bar', 'baz', 'quux'];
        $this->attribute->setValue($testValues);
        self::assertEquals($testValues, $this->attribute->getValue());
    }

    /**
     * testSetValuesThrowsExceptionWhenTesterFails
     * @throws InvalidAttributeValueException
     * @covers \pvc\html\abstract\attribute\AttributeMultiValue::setValue
     * @covers \pvc\html\abstract\attribute\AttributeMultiValue::getValue
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
     * @covers \pvc\html\abstract\attribute\AttributeMultiValue::render
     */
    public function testRenderWithNoValueSet(): void
    {
        self::assertEquals('', $this->attribute->render());
    }

    /**
     * testRenderWithValuesSet
     * @throws InvalidAttributeValueException
     * @covers \pvc\html\abstract\attribute\AttributeMultiValue::render
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
     * @covers \pvc\html\abstract\attribute\AttributeMultiValue::render
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
