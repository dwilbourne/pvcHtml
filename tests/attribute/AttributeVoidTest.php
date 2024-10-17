<?php

/**
 * @author: Doug Wilbourne (dougwilbourne@gmail.com)
 */

declare (strict_types=1);

namespace pvcTests\html\abstract\attribute;

use PHPUnit\Framework\TestCase;
use pvc\html\abstract\attribute\AttributeVoid;
use pvc\html\abstract\err\InvalidAttributeNameException;

class AttributeVoidTest extends TestCase
{
    protected string $name;

    protected AttributeVoid $attribute;

    /**
     * @throws InvalidAttributeNameException
     */
    public function setUp(): void
    {
        $this->name = 'hidden';
        $this->attribute = new AttributeVoid($this->name);
    }

    /**
     * testConstruct
     * @covers \pvc\html\abstract\attribute\AttributeVoid::__construct
     */
    public function testConstruct(): void
    {
        self::assertInstanceOf(AttributeVoid::class, $this->attribute);
    }

    /**
     * testSetNameThrowsExceptionWithInvalidName
     * @covers \pvc\html\abstract\attribute\AttributeVoid::setName
     * @covers \pvc\html\abstract\attribute\AttributeVoid::isValidAttributeName
     */
    public function testSetNameThrowsExceptionWithInvalidName(): void
    {
        $testName = '%7g(';
        self::expectException(InvalidAttributeNameException::class);
        $attribute = new AttributeVoid($testName);
        unset($attribute);
    }

    /**
     * testGetName
     * @throws InvalidAttributeNameException
     * @covers \pvc\html\abstract\attribute\AttributeVoid::setName
     * @covers \pvc\html\abstract\attribute\AttributeVoid::getName
     */
    public function testSetGetName(): void
    {
        self::assertEquals($this->name, $this->attribute->getName());
    }


    /**
     * testRenderReturnsAttributeName
     * @covers \pvc\html\abstract\attribute\AttributeVoid::render
     */
    public function testRenderReturnsAttributeNameWhenUsageValueToTrue(): void
    {
        $expectedOutput = $this->name;
        self::assertEquals($expectedOutput, $this->attribute->render());
    }
}
