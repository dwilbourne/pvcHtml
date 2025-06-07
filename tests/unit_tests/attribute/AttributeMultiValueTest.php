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
use pvc\htmlbuilder\definitions\types\AttributeValueDataType;
use pvc\interfaces\validator\ValTesterInterface;

class AttributeMultiValueTest extends TestCase
{
    protected string $name = 'foo';
    protected AttributeValueDataType $dataType = AttributeValueDataType::String;
    protected bool $caseSensitive = false;
    protected ValTesterInterface&MockObject $tester;

    protected AttributeMultiValue $attribute;

    public function setUp(): void
    {
        $this->tester = $this->createMock(ValTesterInterface::class);
        $this->tester->method('testValue')->willReturn(true);
        $this->attribute = new AttributeMultiValue(
            $this->name,
            $this->dataType,
            $this->caseSensitive,
            $this->tester,
        );
    }

    /**
     * testSetGetValue
     * @throws InvalidAttributeValueException
     * @covers \pvc\html\attribute\AttributeMultiValue::setValue
     * @covers \pvc\html\attribute\AttributeMultiValue::setValues
     * @covers \pvc\html\attribute\AttributeMultiValue::getValue
     * @covers \pvc\html\attribute\AttributeMultiValue::getValues
     */
    public function testSetGetValueSucceedsWithMultipleArguments(): void
    {
        $testValue = ['bar', 'baz', 'quux'];
        $this->attribute->setValues(...$testValue);
        self::assertEquals($testValue, $this->attribute->getValue());
        self::assertEquals($testValue, $this->attribute->getValues());
    }

    /**
     * @return void
     * @throws InvalidAttributeValueException
     * @covers \pvc\html\attribute\AttributeMultiValue::setValue
     * @covers \pvc\html\attribute\AttributeMultiValue::setValues
     * @covers \pvc\html\attribute\AttributeMultiValue::getValue
     * @covers \pvc\html\attribute\AttributeMultiValue::getValues
     */
    public function testSetGetValueSucceedsWithSingleArgument(): void
    {
        $testValue = 'bar';
        $expectedResult = [$testValue];
        $this->attribute->setValues($testValue);
        self::assertEquals($expectedResult, $this->attribute->getValue());
        self::assertEquals($expectedResult, $this->attribute->getValues());
    }

    /**
     * testRenderWithNoValueSet
     * @throws InvalidAttributeValueException
     * @throws \pvc\html\err\InvalidAttributeIdNameException
     * @covers \pvc\html\attribute\AttributeMultiValue::render
     */
    public function testRenderWithNoValueSet(): void
    {
        $expectedResult = '';
        self::assertEquals($expectedResult, $this->attribute->render());
    }

    /**
     * testRenderWithOneValueSet
     * @throws InvalidAttributeValueException
     * @covers \pvc\html\attribute\AttributeMultiValue::render
     */
    public function testRenderWithOneValueSet(): void
    {
        $testValue = ['bar'];
        $this->attribute->setValue(...$testValue);
        $expectedOutput = $this->name . '=bar';
        self::assertEquals($expectedOutput, $this->attribute->render());
    }

    /**
     * testRenderWithValueSet
     * @throws InvalidAttributeValueException
     * @covers \pvc\html\attribute\AttributeMultiValue::render
     */
    public function testRenderWithMultipleValueSet(): void
    {
        $testValue = ['bar', 'baz', 'quux'];
        $this->attribute->setValue(...$testValue);
        $expectedOutput = $this->name . '=bar baz quux';
        self::assertEquals($expectedOutput, $this->attribute->render());
    }
}
