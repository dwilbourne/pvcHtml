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
use pvc\interfaces\validator\ValTesterInterface;

class AttributeSingleValueTest extends TestCase
{
    protected ValTesterInterface|MockObject $tester;

    protected AttributeSingleValue $attribute;

    public function setUp(): void
    {
        $this->tester = $this->createMock(ValTesterInterface::class);
        $this->attribute = new AttributeSingleValue();
        $this->attribute->setTester($this->tester);
    }


    /**
     * testSetValueConvertsCaseIfNotCaseSensitive
     * @throws InvalidAttributeValueException
     * @covers \pvc\html\attribute\AttributeSingleValue::setValue
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
     * testSetValueFailsWithZeroArguments
     * @throws InvalidAttributeValueException
     * @covers \pvc\html\attribute\AttributeSingleValue::setValue
     */
    public function testSetValueFailsWithZeroArguments(): void
    {
        self::expectException(InvalidNumberOfParametersException::class);
        $this->attribute->setValue();
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
     * testSetValueThrowsExceptionWhenTesterFails
     * @throws InvalidAttributeValueException
     * @covers \pvc\html\attribute\AttributeSingleValue::setValue
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
     * @covers \pvc\html\attribute\AttributeSingleValue::setValue
     * @covers \pvc\html\attribute\AttributeSingleValue::getValue
     */
    public function testSetGetValue(): void
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
        $name = 'foo';
        $value = 'bar\'s';
        $this->tester->method('testValue')->willReturn(true);
        $this->attribute->setName($name);
        $this->attribute->setValue($value);
        $expectedRendering = $name . '=\'bar\'s\'';
        self::assertEquals($expectedRendering, $this->attribute->render());
    }
}
