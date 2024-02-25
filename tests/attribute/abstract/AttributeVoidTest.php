<?php

/**
 * @author: Doug Wilbourne (dougwilbourne@gmail.com)
 */
declare (strict_types=1);

namespace pvcTests\html\attribute\abstract;

use PHPUnit\Framework\TestCase;
use pvc\html\attribute\abstract\AttributeVoid;

class AttributeVoidTest extends TestCase
{
    protected string $testName;

    protected AttributeVoid $attribute;

    public function setUp(): void
    {
        $this->testName = 'foo';
        $this->attribute = new AttributeVoid($this->testName);
    }

    /**
     * testGetValueReturnsFalseByDefault
     * @covers \pvc\html\attribute\abstract\AttributeVoid::getValue
     */
    public function testGetValueReturnsFalseByDefault(): void
    {
        self::assertTrue($this->attribute->getValue());
    }

    /**
     * testSetGetValue
     * @covers \pvc\html\attribute\abstract\AttributeVoid::setValue
     * @covers \pvc\html\attribute\abstract\AttributeVoid::getValue
     */
    public function testSetGetValue(): void
    {
        $usage = true;
        $this->attribute->setValue($usage);
        self::assertEquals($usage, $this->attribute->getValue());
    }

    /**
     * testRenderReturnsEmptyStringWhenValueSetToFalse
     * @covers \pvc\html\attribute\abstract\AttributeVoid::render
     */
    public function testRenderReturnsAttributeNameWhenValueSetToTrue(): void
    {
        $expectedOutput = $this->testName;
        $this->attribute->setValue(true);
        self::assertEquals($expectedOutput, $this->attribute->render());
    }
}
