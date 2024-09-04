<?php

/**
 * @author: Doug Wilbourne (dougwilbourne@gmail.com)
 */
declare (strict_types=1);

namespace pvcTests\html\abstract\attribute;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use pvc\html\abstract\attribute\Attribute;
use pvc\html\abstract\err\InvalidAttributeNameException;
use pvc\interfaces\validator\ValTesterInterface;

class AttributeTest extends TestCase
{
    protected string $testName;
    protected ValTesterInterface|MockObject $tester;

    protected Attribute $attribute;

    public function setUp(): void
    {
        $this->testName = 'target';
        $this->tester = $this->createMock(ValTesterInterface::class);
        $this->attribute = $this->getMockBuilder(Attribute::class)
            ->setConstructorArgs([$this->testName, $this->tester])
            ->getMockForAbstractClass();
    }

    /**
     * testSetNameThrowsExceptionWithInvalidName
     * @covers \pvc\html\abstract\attribute\Attribute::setName
     * @covers \pvc\html\abstract\attribute\Attribute::isValidAttributeName
     */
    public function testSetNameThrowsExceptionWithInvalidName(): void
    {
        $testName = '%7g(';
        self::expectException(InvalidAttributeNameException::class);
        $attribute = $this->getMockBuilder(Attribute::class)
                                ->setConstructorArgs([$testName, $this->tester])
                                ->getMockForAbstractClass();
    }

    /**
     * testGetName
     * @throws InvalidAttributeNameException
     * @covers \pvc\html\abstract\attribute\Attribute::setName
     * @covers \pvc\html\abstract\attribute\Attribute::getName
     */
    public function testSetGetName(): void
    {
        self::assertEquals($this->testName, $this->attribute->getName());
    }

    /**
     * testSetGetTester
     * @covers \pvc\html\abstract\attribute\Attribute::setTester
     * @covers \pvc\html\abstract\attribute\Attribute::getTester
     */
    public function testSetGetTester(): void
    {
        self::assertEquals($this->tester, $this->attribute->getTester());
    }

    /**
     * testSetValueIsCaseSensitive
     * @covers \pvc\html\abstract\attribute\Attribute::setCaseSensitive
     * @covers \pvc\html\abstract\attribute\Attribute::isCaseSensitive
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
     * @covers \pvc\html\abstract\attribute\Attribute::setGlobalYn
     * @covers \pvc\html\abstract\attribute\Attribute::isGlobalYn
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
