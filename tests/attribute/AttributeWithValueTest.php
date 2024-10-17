<?php

/**
 * @author: Doug Wilbourne (dougwilbourne@gmail.com)
 */
declare (strict_types=1);

namespace pvcTests\html\abstract\attribute;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use pvc\html\abstract\attribute\AttributeWithValue;
use pvc\interfaces\validator\ValTesterInterface;

class AttributeWithValueTest extends TestCase
{
    protected string $testName;
    protected ValTesterInterface|MockObject $tester;

    protected AttributeWithValue $attribute;

    public function setUp(): void
    {
        $this->testName = 'target';
        $this->tester = $this->createMock(ValTesterInterface::class);

        $this->attribute = $this->getMockBuilder(AttributeWithValue::class)
            ->setConstructorArgs([$this->testName, $this->tester])
            ->getMockForAbstractClass();
    }

    /**
     * testSetGetTester
     * @covers \pvc\html\abstract\attribute\AttributeWithValue::setTester
     * @covers \pvc\html\abstract\attribute\AttributeWithValue::getTester
     */
    public function testSetGetTester(): void
    {
        self::assertEquals($this->tester, $this->attribute->getTester());
    }

    /**
     * testSetValueIsCaseSensitive
     * @covers \pvc\html\abstract\attribute\AttributeWithValue::setCaseSensitive
     * @covers \pvc\html\abstract\attribute\AttributeWithValue::isCaseSensitive
     */
    public function testSetValueIsCaseSensitive(): void
    {
        self::assertFalse($this->attribute->isCaseSensitive());
        $this->attribute->setCaseSensitive(true);
        self::assertTrue($this->attribute->isCaseSensitive());
        $this->attribute->setCaseSensitive(false);
        self::assertFalse($this->attribute->isCaseSensitive());
    }

    /**
     * testSetGlobalIsGlobal
     * @covers \pvc\html\abstract\attribute\AttributeWithValue::setGlobalYn
     * @covers \pvc\html\abstract\attribute\AttributeWithValue::isGlobalYn
     */
    public function testSetGlobalIsGlobal(): void
    {
        /**
         * default should be faLse
         */
        self::assertFalse($this->attribute->isGlobalYn());

        /**
         * test setting to true
         */
        $newValue = true;
        $this->attribute->setGlobalYn($newValue);
        self::assertEquals($newValue, $this->attribute->isGlobalYn());

        /**
         * test setting back to false
         */
        $newValue = false;
        $this->attribute->setGlobalYn($newValue);
        self::assertEquals($newValue, $this->attribute->isGlobalYn());
    }

}
