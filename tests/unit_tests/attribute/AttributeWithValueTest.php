<?php

/**
 * @author: Doug Wilbourne (dougwilbourne@gmail.com)
 */
declare (strict_types=1);

namespace pvcTests\html\unit_tests\attribute;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use pvc\html\attribute\AttributeWithValue;
use pvc\interfaces\validator\ValTesterInterface;

class AttributeWithValueTest extends TestCase
{
    protected ValTesterInterface|MockObject $tester;

    protected AttributeWithValue $attribute;

    public function setUp(): void
    {
        $this->tester = $this->createMock(ValTesterInterface::class);

        $this->attribute = $this->getMockBuilder(AttributeWithValue::class)
            ->getMockForAbstractClass();
    }

    /**
     * testSetGetTester
     * @covers \pvc\html\attribute\AttributeWithValue::setTester
     * @covers \pvc\html\attribute\AttributeWithValue::getTester
     */
    public function testSetGetTester(): void
    {
        $this->attribute->setTester($this->tester);
        self::assertEquals($this->tester, $this->attribute->getTester());
    }

    /**
     * testSetValueIsCaseSensitive
     * @covers \pvc\html\attribute\AttributeWithValue::setCaseSensitive
     * @covers \pvc\html\attribute\AttributeWithValue::isCaseSensitive
     */
    public function testSetValueIsCaseSensitive(): void
    {
        self::assertFalse($this->attribute->isCaseSensitive());
        $this->attribute->setCaseSensitive(true);
        self::assertTrue($this->attribute->isCaseSensitive());
        $this->attribute->setCaseSensitive(false);
        self::assertFalse($this->attribute->isCaseSensitive());
    }
}
