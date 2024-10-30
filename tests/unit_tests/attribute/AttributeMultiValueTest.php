<?php

/**
 * @author: Doug Wilbourne (dougwilbourne@gmail.com)
 */

declare (strict_types=1);

namespace pvcTests\html\unit_tests\attribute;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use pvc\html\attribute\AttributeMultiValue;
use pvc\html\err\InvalidAttributeValueException;
use pvc\html\err\InvalidNumberOfParametersException;
use pvc\interfaces\validator\ValTesterInterface;

class AttributeMultiValueTest extends TestCase
{
    /**
     * @var ValTesterInterface<string>|MockObject
     */
    protected ValTesterInterface|MockObject $tester;

    protected AttributeMultiValue $attribute;

    public function setUp(): void
    {
        $this->tester = $this->createMock(ValTesterInterface::class);
        $this->attribute = new AttributeMultiValue();
        $this->attribute->setTester($this->tester);
    }

    /**
     * testSetValueFailsWhenValueIsEmpty
     * @throws InvalidNumberOfParametersException
     * @covers \pvc\html\attribute\AttributeMultiValue::setValue
     */
    public function testSetValueFailsWhenValueIsEmpty(): void
    {
        self::expectException(InvalidNumberOfParametersException::class);
        $this->attribute->setValue();
    }

    /**
     * testSetValueConvertsValueToLowerCaseIfCaseSensitive
     * @throws InvalidAttributeValueException
     * @covers \pvc\html\attribute\AttributeMultiValue::setValue
     * @covers \pvc\html\attribute\AttributeMultiValue::getValue
     */
    public function testSetValueConvertsValueToLowerCaseIfCaseSensitive(): void
    {
        $values = ['FOO', 'BAR'];
        $this->attribute->setCaseSensitive(true);
        $this->tester->method('testValue')->willReturn(true);
        $this->attribute->setValue(...$values);
        self::assertEquals($values, $this->attribute->getValue());

        $values = ['FOO', 'BAR'];
        $this->attribute->setCaseSensitive(false);
        $this->tester->method('testValue')->willReturn(true);
        $this->attribute->setValue(...$values);
        $expectedResult = array_map('strtolower', $values);
        self::assertEquals($expectedResult, $this->attribute->getValue());
    }

    /**
     * testSetGetValue
     * @throws InvalidAttributeValueException
     * @covers \pvc\html\attribute\AttributeMultiValue::setValue
     * @covers \pvc\html\attribute\AttributeMultiValue::getValue
     * @covers \pvc\html\attribute\AttributeMultiValue::setValues
     * @covers \pvc\html\attribute\AttributeMultiValue::getValues
     */
    public function testSetGetValue(): void
    {
        $this->tester->method('testValue')->willReturn(true);
        $testValue = ['bar', 'baz', 'quux'];
        $this->attribute->setValues(...$testValue);
        self::assertEquals($testValue, $this->attribute->getValues());
    }

    /**
     * testSetValueThrowsExceptionWhenTesterFails
     * @throws InvalidAttributeValueException
     * @covers \pvc\html\attribute\AttributeMultiValue::setValue
     * @covers \pvc\html\attribute\AttributeMultiValue::getValue
     */
    public function testSetValueThrowsExceptionWhenTesterFails(): void
    {
        $this->tester->method('testValue')->willReturn(false);
        $testValue = ['bar', 'baz', 'quux'];
        self::expectException(InvalidAttributeValueException::class);
        $this->attribute->setValue(...$testValue);
    }

    /**
     * testRenderWithNoValueSet
     * @throws InvalidAttributeValueException
     * @throws \pvc\html\err\InvalidAttributeIdNameException
     * @covers \pvc\html\attribute\AttributeMultiValue::render
     */
    public function testRenderWithNoValueSet(): void
    {
        $name = 'foo';
        $this->attribute->setName($name);
        self::expectException(InvalidAttributeValueException::class);
        $this->attribute->render();
    }

    /**
     * testRenderWithOneValueSet
     * @throws InvalidAttributeValueException
     * @covers \pvc\html\attribute\AttributeMultiValue::render
     */
    public function testRenderWithOneValueSet(): void
    {
        $name = 'foo';
        $testValue = ['bar'];
        $this->tester->method('testValue')->willReturn(true);
        $this->attribute->setName($name);
        $this->attribute->setValue(...$testValue);
        $expectedOutput = $name . '=\'bar\'';
        self::assertEquals($expectedOutput, $this->attribute->render());
    }

    /**
     * testRenderWithValueSet
     * @throws InvalidAttributeValueException
     * @covers \pvc\html\attribute\AttributeMultiValue::render
     */
    public function testRenderWithMultipleValueSet(): void
    {
        $name = 'foo';
        $testValue = ['bar', 'baz', 'quux'];
        $this->tester->method('testValue')->willReturn(true);
        $this->attribute->setName($name);
        $this->attribute->setValue(...$testValue);
        $expectedOutput = $name . '=\'bar baz quux\'';
        self::assertEquals($expectedOutput, $this->attribute->render());
    }
}
