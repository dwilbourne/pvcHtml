<?php

/**
 * @author: Doug Wilbourne (dougwilbourne@gmail.com)
 */
declare (strict_types=1);

namespace pvcTests\html\attribute\abstract;

use PHPUnit\Framework\TestCase;
use pvc\html\attribute\abstract\Attribute;
use pvc\html\err\InvalidAttributeNameException;
use pvc\interfaces\validator\ValTesterInterface;

class AttributeTest extends TestCase
{
    protected Attribute $attribute;

    protected string $testName = 'foo';

    public function setUp(): void
    {
        $this->attribute = $this->getMockBuilder(Attribute::class)
                                ->setConstructorArgs([$this->testName])
                                ->getMockForAbstractClass();
    }

    /**
     * testConstructor
     * @covers \pvc\html\attribute\abstract\Attribute::__construct
     * @covers \pvc\html\attribute\abstract\Attribute::getName
     * @covers \pvc\html\attribute\abstract\Attribute::setName
     */
    public function testConstructor()
    {
        self::assertInstanceOf(Attribute::class, $this->attribute);
        self::assertEquals($this->testName, $this->attribute->getName());
    }

    /**
     * testSetNameToEmptyStringThrowsException
     * @throws InvalidAttributeNameException
     * @covers \pvc\html\attribute\abstract\Attribute::setName
     */
    public function testSetNameToEmptyStringThrowsException(): void
    {
        $testName = '';
        self::expectException(InvalidAttributeNameException::class);
        $attribute = $this->getMockBuilder(Attribute::class)
                          ->setConstructorArgs([$testName])
                          ->getMockForAbstractClass();
        unset($attribute);
    }

    /**
     * testSetGetValTester
     * @covers \pvc\html\attribute\abstract\Attribute::setTester
     * @covers \pvc\html\attribute\abstract\Attribute::getTester
     */
    public function testSetGetValTester(): void
    {
        self::assertNull($this->attribute->getTester());
        $valTester = $this->createMock(ValTesterInterface::class);
        $this->attribute->setTester($valTester);
        self::assertEquals($valTester, $this->attribute->getTester());
    }
}
