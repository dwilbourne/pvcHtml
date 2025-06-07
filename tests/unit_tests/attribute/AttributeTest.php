<?php

/**
 * @author: Doug Wilbourne (dougwilbourne@gmail.com)
 */

declare (strict_types=1);

namespace pvcTests\html\unit_tests\attribute;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use pvc\html\attribute\Attribute;
use pvc\html\err\InvalidAttributeIdNameException;
use pvc\html\err\InvalidAttributeValueException;
use pvc\htmlbuilder\definitions\types\AttributeValueDataType;
use pvc\interfaces\validator\ValTesterInterface;

class AttributeTest extends TestCase
{
    protected Attribute $attribute;
    protected string $name;
    protected bool $caseInsensitive;
    /**
     * @var ValTesterInterface<string|int>&MockObject
     */
    protected ValTesterInterface&MockObject $valTester;

    public function setUp(): void
    {
        $this->name = 'foo';
        $this->valTester = $this->createMock(ValTesterInterface::class);
        $this->attribute = $this->getMockForAbstractClass(Attribute::class, [$this->name]);
    }

    /**
     * @return void
     * @covers \pvc\html\attribute\Attribute::__construct
     * @covers \pvc\html\attribute\Attribute::setName
     */
    public function testConstruction(): void
    {
        self::assertInstanceOf(Attribute::class, $this->attribute);
    }

    /**
     * testSetNameThrowsExceptionWithInvalidName
     * @covers \pvc\html\attribute\AttributeVoid::setName
     * @covers \pvc\html\attribute\AttributeVoid::isValidAttributeIdName
     */
    public function testSetNameThrowsExceptionWithInvalidName(): void
    {
        $invalidName = '%7g(';
        self::expectException(InvalidAttributeIdNameException::class);
        $attribute = $this->getMockForAbstractClass(Attribute::class, [$invalidName]);
        unset($attribute);
    }

    /**
     * @return void
     * @throws InvalidAttributeValueException
     * @covers \pvc\html\attribute\Attribute::setValue
     */
    public function testSetValueFailsWithEmptyValue(): void
    {
        $dataType = AttributeValueDataType::String;
        $caseSensitive = false;
        $value = '';
        $this->valTester->method('testValue')->willReturn(false);
        $args = [$this->name, $dataType, $caseSensitive, $this->valTester];
        $this->attribute = $this->getMockForAbstractClass(Attribute::class, $args);
        self::expectException(InvalidAttributeValueException::class);
        $this->attribute->setValue($value);
    }

    /**
     * @return void
     * @throws InvalidAttributeValueException
     * @covers \pvc\html\attribute\Attribute::setValue
     */
    public function testDefaultValueTypeStringThrowsExceptionWithIntValue(): void
    {
        self::expectException(InvalidAttributeValueException::class);
        $this->attribute->setValue(123);
    }

    /**
     * @return void
     * @throws InvalidAttributeValueException
     * @covers \pvc\html\attribute\Attribute::setValue
     */
    public function testSetValueIntegerThrowsExceptionWithInvalidValueType(): void
    {
        $this->attribute = $this->getMockForAbstractClass(Attribute::class, [$this->name, AttributeValueDataType::Integer]);
        self::expectException(InvalidAttributeValueException::class);
        $this->attribute->setValue('123');
    }

    /**
     * @return void
     * @throws InvalidAttributeValueException
     * @covers \pvc\html\attribute\Attribute::setValue
     */
    public function testSetValueBooleanThrowsExceptionWithInvalidValueType(): void
    {
        $this->attribute = $this->getMockForAbstractClass(Attribute::class, [$this->name, AttributeValueDataType::Bool]);
        self::expectException(InvalidAttributeValueException::class);
        $this->attribute->setValue('123');
    }

    /**
     * @return void
     * @covers \pvc\html\attribute\Attribute::setValue
     */
    public function testCaseSensitiveFalseConvertsValue(): void
    {
        $dataType = AttributeValueDataType::String;
        $caseSensitive = false;
        $this->attribute = $this->getMockForAbstractClass(Attribute::class,
            [$this->name,
             $dataType,
             $caseSensitive,
             $this->valTester,
            ]);
        $inputValue = 'AbCdE';
        $expectedValue = strtolower($inputValue);
        $this->valTester->expects($this->once())->method('testValue')->with($expectedValue)->willReturn(true);
        $this->attribute->method('confirmParameterCount')->willReturn($expectedValue);
        $this->attribute->setValue($inputValue);
        /**
         * confirm the set value is the case-converted string
         */
        self::assertEquals($expectedValue, $this->attribute->getValue());
    }

    /**
     * @return void
     * @throws InvalidAttributeValueException
     * @covers \pvc\html\attribute\Attribute::setValue
     */
    public function testCaseSensitiveTrueDoesNotConvertValue(): void
    {
        $dataType = AttributeValueDataType::String;
        $caseSensitive = true;
        $this->attribute = $this->getMockForAbstractClass(Attribute::class,
            [$this->name,
             $dataType,
             $caseSensitive,
             $this->valTester,
            ]);
        $inputValue = 'AbCdE';
        $expectedValue = $inputValue;
        $this->valTester->expects($this->once())->method('testValue')->with($expectedValue)->willReturn(true);
        $this->attribute->method('confirmParameterCount')->willReturn($expectedValue);
        $this->attribute->setValue($inputValue);
        /**
         * confirm the set value is the case-converted string
         */
        self::assertEquals($expectedValue, $this->attribute->getValue());
    }

    /**
     * testSetValueThrowsExceptionWhenTesterFails
     * @throws InvalidAttributeValueException
     * @covers \pvc\html\attribute\Attribute::setValue
     */
    public function testSetValueThrowsExceptionWhenTesterFails(): void
    {
        $dataType = AttributeValueDataType::String;
        $caseSensitive = false;
        $value = 'bar';
        $this->valTester->method('testValue')->willReturn(false);
        $args = [$this->name, $dataType, $caseSensitive, $this->valTester];
        $this->attribute = $this->getMockForAbstractClass(Attribute::class, $args);
        self::expectException(InvalidAttributeValueException::class);
        $this->attribute->setValue($value);
    }



    /**
     * @return void
     * @throws InvalidAttributeValueException
     * @covers \pvc\html\attribute\Attribute::setValue
     */
    public function testConfirmParameterCountIsCalled(): void
    {
        $inputValue = 'AbCdE';
        $returnValue = $inputValue;
        $this->attribute->expects($this->once())->method('confirmParameterCount')->willReturn($returnValue);
        $this->attribute->setValue($inputValue);
    }

    /**
     * @return void
     * @depends testConfirmParameterCountIsCalled
     * @covers \pvc\html\attribute\Attribute::unsetValue
     */
    public function testUnsetValue(): void
    {
        $inputValue = 'AbCdE';
        $returnValue = $inputValue;
        $this->attribute->expects($this->once())->method('confirmParameterCount')->willReturn($returnValue);
        $this->attribute->setValue($inputValue);
        self::assertEquals($returnValue, $this->attribute->getValue());
        $this->attribute->unsetValue();
        self::assertNull($this->attribute->getValue());
    }
}
