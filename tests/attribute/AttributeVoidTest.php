<?php

/**
 * @author: Doug Wilbourne (dougwilbourne@gmail.com)
 */

declare (strict_types=1);

namespace pvcTests\html\attribute;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use pvc\html\attribute\AttributeVoid;
use pvc\html\err\InvalidAttributeNameException;
use pvc\html\err\InvalidAttributeValueException;
use pvc\html\err\UnsetAttributeNameException;
use pvc\interfaces\html\config\HtmlConfigInterface;
use pvc\interfaces\validator\ValTesterInterface;

class AttributeVoidTest extends TestCase
{
    protected string $name;

    protected HtmlConfigInterface $htmlConfig;
    protected ValTesterInterface|MockObject $tester;

    protected AttributeVoid $attribute;

    public function setUp(): void
    {
        $this->name = 'hidden';
        $this->htmlConfig = $this->createMock(HtmlConfigInterface::class);
        $this->htmlConfig->method('isValidAttributeName')->with($this->name)->willReturn(true);
        $this->tester = $this->createMock(ValTesterInterface::class);
        $this->attribute = new AttributeVoid($this->tester, $this->htmlConfig);
    }

    /**
     * testSetAttributeThrowsExceptionWithVoidAttributeNameAndNonBooleanValue
     * @throws InvalidAttributeValueException
     * @covers AttributeVoid::setValue
     */
    public function testSetAttributeThrowsExceptionWithVoidAttributeNameAndNonBooleanValue(): void
    {
        self::expectException(InvalidAttributeValueException::class);
        $value = 'foobar';
        $this->attribute->setValue($value);
    }


    /**
     * testSetGetValue
     * @covers AttributeVoid::setValue
     * @covers AttributeVoid::getValue
     */
    public function testSetGetValue(): void
    {
        $this->tester->method('testValue')->willReturn(true);
        self::assertTrue($this->attribute->getValue());
        $this->attribute->setValue(false);
        self::assertFalse($this->attribute->getValue());
    }

    /**
     * testRenderReturnsAttributeNameWhenUsageValueToTrue
     * @covers AttributeVoid::render
     */
    public function testRenderReturnsAttributeNameWhenUsageValueToTrue(): void
    {
        $expectedOutput = $this->name;
        $this->attribute->setName($this->name);
        self::assertEquals($expectedOutput, $this->attribute->render());
    }

    /**
     * testRenderReturnsEmptyStringWhenValueSetToFalse
     * @throws UnsetAttributeNameException
     * @throws InvalidAttributeNameException
     * @covers AttributeVoid::render
     */
    public function testRenderReturnsEmptyStringWhenValueSetToFalse(): void
    {
        $expectedOutput = '';
        $this->attribute->setName($this->name);
        $this->tester->method('testValue')->willReturn(true);
        $this->attribute->setValue(false);
        self::assertEquals($expectedOutput, $this->attribute->render());
    }

    /**
     * setRenderThrowsExceptionWhenNameNotSet
     * @throws UnsetAttributeNameException
     * @covers AttributeVoid::render
     */
    public function testSetRenderThrowsExceptionWhenNameNotSet(): void
    {
        self::expectException(UnsetAttributeNameException::class);
        $this->attribute->render();
    }
}
