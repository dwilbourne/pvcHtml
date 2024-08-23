<?php

/**
 * @author: Doug Wilbourne (dougwilbourne@gmail.com)
 */
declare (strict_types=1);

namespace pvcTests\html\attribute\abstract;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use pvc\html\attribute\abstract\Attribute;
use pvc\html\err\InvalidAttributeEventNameException;
use pvc\interfaces\validator\ValTesterInterface;

class AttributeTest extends TestCase
{
    protected ValTesterInterface|MockObject $tester;

    protected Attribute $attribute;

    public function setUp(): void
    {
        $this->tester = $this->createMock(ValTesterInterface::class);
        $this->attribute = $this->getMockBuilder(Attribute::class)
            ->setConstructorArgs([$this->tester])
            ->getMockForAbstractClass();
    }

    /**
     * testConstruct
     * @covers \pvc\html\attribute\abstract\Attribute::__construct
     */
    public function testConstruct(): void
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
        self::expectException(InvalidAttributeEventNameException::class);
        $this->attribute->setName($testName);
    }

    /**
     * testGetNameReturnsEmptyStringWhenNameIsNotSet
     * @covers \pvc\html\attribute\abstract\Attribute::getName
     */
    public function testgetNameReturnsEmptyStringWhenNameIsNotSet(): void
    {
        $expectedName = '';
        self::assertEquals($expectedName, $this->attribute->getName());
    }

    /**
     * testSetGetName
     * @throws InvalidAttributeEventNameException
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
     * testSetGetTester
     * @covers \pvc\html\attribute\abstract\Attribute::setTester
     * @covers \pvc\html\attribute\abstract\Attribute::getTester
     */
    public function testSetGetTester(): void
    {
        $tester = $this->createMock(ValTesterInterface::class);
        $this->attribute->setTester($tester);
        self::assertEquals($tester, $this->attribute->getTester());
    }

    /**
     * testSetIsCaseSensitive
     * @covers \pvc\html\attribute\abstract\Attribute::setCaseSensitive
     * @covers \pvc\html\attribute\abstract\Attribute::valueIsCaseSensitive
     */
    public function testSetIsCaseSensitive(): void
    {
        self::assertFalse($this->attribute->valueIsCaseSensitive());
        $this->attribute->setCaseSensitive(true);
        self::assertTrue($this->attribute->valueIsCaseSensitive());
        $this->attribute->setCaseSensitive(false);
        self::assertFalse($this->attribute->valueIsCaseSensitive());
    }

}
