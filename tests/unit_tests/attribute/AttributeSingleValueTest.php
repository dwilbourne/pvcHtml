<?php

/**
 * @author: Doug Wilbourne (dougwilbourne@gmail.com)
 */

declare (strict_types=1);

namespace pvcTests\html\unit_tests\attribute;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use pvc\html\attribute\AttributeSingleValue;
use pvc\html\err\InvalidAttributeValueException;
use pvc\html\err\InvalidNumberOfParametersException;
use pvc\htmlbuilder\definitions\types\AttributeValueDataType;
use pvc\interfaces\validator\ValTesterInterface;

class AttributeSingleValueTest extends TestCase
{
    protected string $name = 'foo';
    protected AttributeValueDataType $dataType = AttributeValueDataType::String;
    protected bool $caseSensitive = false;
    protected ValTesterInterface&MockObject $tester;

    protected AttributeSingleValue $attribute;

    public function setUp(): void
    {
        $this->tester = $this->createMock(ValTesterInterface::class);
        $this->tester->method('testValue')->willReturn(true);
        $this->attribute = new AttributeSingleValue(
            $this->name,
            $this->dataType,
            $this->caseSensitive,
            $this->tester,
        );
    }

    /**
     * testSetValueFailsWithMoreThanOneArgument
     * @throws InvalidAttributeValueException
     * @covers \pvc\html\attribute\AttributeSingleValue::setValue
     */
    public function testSetValueFailsWithMoreThanOneArgument(): void
    {
        self::expectException(InvalidNumberOfParametersException::class);
        $this->attribute->setValue('foo', 'bar');
    }

    /**
     * testSetGetValue
     * @throws InvalidAttributeValueException
     * @covers \pvc\html\attribute\AttributeSingleValue::setValue
     * @covers \pvc\html\attribute\AttributeSingleValue::getValue
     */
    public function testSetValueSucceedsWithOneArgument(): void
    {
        $value = 'bar';
        $this->tester->method('testValue')->willReturn(true);
        $this->attribute->setValue($value);
        self::assertEquals($value, $this->attribute->getValue());
    }

    /**
     * testRenderWithValueSet
     * @throws InvalidAttributeValueException
     * @covers \pvc\html\attribute\AttributeSingleValue::render
     */
    public function testRenderWithValueSet(): void
    {
        $value = 'bar';
        $this->attribute->setValue($value);
        $expectedRendering = $this->name . '=' . $value;
        self::assertEquals($expectedRendering, $this->attribute->render());
    }

    /**
     * @return void
     * @covers \pvc\html\attribute\AttributeSingleValue::render
     */
    public function testRenderWithoutValueSet(): void
    {
        $expectedRendering  = '';
        self::assertEquals($expectedRendering, $this->attribute->render());
    }
}
