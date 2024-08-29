<?php

/**
 * @author: Doug Wilbourne (dougwilbourne@gmail.com)
 */

declare (strict_types=1);

namespace pvcTests\html\attribute;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use pvc\html\attribute\AttributeSingleValue;
use pvc\html\err\InvalidAttributeValueException;
use pvc\html\err\UnsetAttributeNameException;
use pvc\interfaces\html\config\HtmlConfigInterface;
use pvc\interfaces\validator\ValTesterInterface;

class AttributeSingleValueTest extends TestCase
{
    protected string $name;

    protected HtmlConfigInterface $htmlConfig;

    protected ValTesterInterface|MockObject $tester;

    protected AttributeSingleValue $attribute;

    public function setUp(): void
    {
        $this->name = 'target';
        $this->htmlConfig = $this->createMock(HtmlConfigInterface::class);
        $this->htmlConfig->method('isValidAttributeName')->with($this->name)->willReturn(true);
        $this->tester = $this->createMock(ValTesterInterface::class);
        $this->attribute = new AttributeSingleValue($this->tester, $this->htmlConfig);
    }

    /**
     * testSetValueFailsIfNotAString
     * @throws InvalidAttributeValueException
     * @covers \pvc\html\attribute\AttributeSingleValue::setValue
     */
    public function testSetValueFailsIfNotAString(): void
    {
        $value = 5;
        self::expectException(InvalidAttributeValueException::class);
        $this->attribute->setValue($value);
    }

    /**
     * testSetValueFailsIfValueIsEmpty
     * @throws InvalidAttributeValueException
     * @covers \pvc\html\attribute\AttributeSingleValue::setValue
     */
    public function testSetValueFailsIfValueIsEmpty(): void
    {
        $value = '';
        self::expectException(InvalidAttributeValueException::class);
        $this->attribute->setValue($value);
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
     * testRenderWithNoNameSet
     * @throws UnsetAttributeNameException
     * @covers \pvc\html\attribute\AttributeSingleValue::render
     */
    public function testRenderWithNoNameSet(): void
    {
        self::expectException(UnsetAttributeNameException::class);
        $this->attribute->render();
    }

    /**
     * testRenderWithNoValueSet
     * @covers \pvc\html\attribute\AttributeSingleValue::render
     */
    public function testRenderWithNoValueSet(): void
    {
        $this->attribute->setName($this->name);
        self::assertEquals('', $this->attribute->render());
    }

    /**
     * testRenderWithValueSet
     * @throws InvalidAttributeValueException
     * @covers \pvc\html\attribute\AttributeSingleValue::render
     */
    public function testRenderWithValueSet(): void
    {
        $this->attribute->setName($this->name);
        $value = 'bar\'s';
        $this->tester->method('testValue')->willReturn(true);
        $this->attribute->setValue($value);
        $expectedRendering = $this->name . '=\'bar\'s\'';
        self::assertEquals($expectedRendering, $this->attribute->render());
    }

}
