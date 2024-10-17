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
     * testSetValuesFailsWhenValueIsEmpty
     * @throws InvalidAttributeValueException
     * @covers \pvc\html\abstract\attribute\AttributeMultiValue::setValues
     */
    public function testSetValuesFailsWhenValueIsEmpty(): void
    {
        $values = [];
        self::expectException(InvalidAttributeValueException::class);
        $this->attribute->setValues($values);
    }

    /**
     * testSetValuesConvertsValuesToLowerCaseIfCaseSensitive
     * @throws InvalidAttributeValueException
     * @covers \pvc\html\abstract\attribute\AttributeMultiValue::setValues
     */
    public function testSetValuesConvertsValuesToLowerCaseIfCaseSensitive(): void
    {
        $values = ['FOO', 'BAR'];
        $this->attribute->setCaseSensitive(true);
        $this->tester->method('testValue')->willReturn(true);
        $this->attribute->setValues($values);
        self::assertEquals($values, $this->attribute->getValues());

        $values = ['FOO', 'BAR'];
        $this->attribute->setCaseSensitive(false);
        $this->tester->method('testValue')->willReturn(true);
        $this->attribute->setValues($values);
        $expectedResult = array_map('strtolower', $values);
        self::assertEquals($expectedResult, $this->attribute->getValues());
    }

    /**
     * testSetGetValues
     * @throws InvalidAttributeValueException
     * @covers \pvc\html\abstract\attribute\AttributeMultiValue::setValues
     * @covers \pvc\html\abstract\attribute\AttributeMultiValue::getValues
     */
    public function testSetGetValues(): void
    {
        $this->tester->method('testValue')->willReturn(true);
        $testValues = ['bar', 'baz', 'quux'];
        $this->attribute->setValues($testValues);
        self::assertEquals($testValues, $this->attribute->getValues());
    }

    /**
     * testSetValuesThrowsExceptionWhenTesterFails
     * @throws InvalidAttributeValueException
     * @covers \pvc\html\abstract\attribute\AttributeMultiValue::setValues
     * @covers \pvc\html\abstract\attribute\AttributeMultiValue::getValues
     */
    public function testSetValuesThrowsExceptionWhenTesterFails(): void
    {
        $this->tester->method('testValue')->willReturn(false);
        $testValues = ['bar', 'baz', 'quux'];
        self::expectException(InvalidAttributeValueException::class);
        $this->attribute->setValues($testValues);
    }

    /**
     * testRenderWithnoValuesSet
     * @throws InvalidAttributeValueException
     * @covers \pvc\html\abstract\attribute\AttributeMultiValue::render
     */
    public function testRenderWithnoValuesSet(): void
    {
        self::expectException(InvalidAttributeValueException::class);
        $this->attribute->render();
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
        $this->attribute->setValues($testValues);
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
        $this->attribute->setValues($testValues);
        $expectedOutput = $this->attributeName . '=\'bar baz quux\'';
        self::assertEquals($expectedOutput, $this->attribute->render());
    }
}
