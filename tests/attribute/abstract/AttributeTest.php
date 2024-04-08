<?php

/**
 * @author: Doug Wilbourne (dougwilbourne@gmail.com)
 */
declare (strict_types=1);

namespace pvcTests\html\attribute\abstract;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use pvc\html\attribute\abstract\Attribute;
use pvc\html\err\InvalidAttributeNameException;
use pvc\interfaces\validator\ValTesterInterface;

class AttributeTest extends TestCase
{
    protected Attribute $attribute;

    protected string $testName = 'foo';


    protected ValTesterInterface|MockObject $tester;

    public function setUp(): void
    {
        $this->tester = $this->createMock(ValTesterInterface::class);
        $this->attribute = $this->getMockBuilder(Attribute::class)
            ->setConstructorArgs([$this->tester])
                                ->getMockForAbstractClass();
    }

    /**
     * testConstructor
     * @covers \pvc\html\attribute\abstract\Attribute::__construct
     */
    public function testConstructor()
    {
        self::assertInstanceOf(Attribute::class, $this->attribute);
    }

    /**
     * testSetInvalidNameThrowsException
     * @covers \pvc\html\attribute\abstract\Attribute::setName
     */
    public function testSetInvalidNameThrowsException(): void
    {
        $testName = 'foo';
        self::expectException(InvalidAttributeNameException::class);
        $this->attribute->setName($testName);
    }

    /**
     * testSetGetName
     * @throws InvalidAttributeNameException
     * @covers \pvc\html\attribute\abstract\Attribute::setName
     * @covers \pvc\html\attribute\abstract\Attribute::getName
     */
    public function testSetGetName(): void
    {
        $testName = 'target';
        $this->attribute->setName($testName);
        self::assertEquals($testName, $this->attribute->getName());
    }

    /**
     * testSetGetValTester
     * @covers \pvc\html\attribute\abstract\Attribute::setTester
     * @covers \pvc\html\attribute\abstract\Attribute::getTester
     */
    public function testSetGetValTester(): void
    {
        $valTester = $this->createMock(ValTesterInterface::class);
        $this->attribute->setTester($valTester);
        self::assertEquals($valTester, $this->attribute->getTester());
    }
}
