<?php

/**
 * @author: Doug Wilbourne (dougwilbourne@gmail.com)
 */
declare (strict_types=1);

namespace pvcTests\html\attribute;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use pvc\html\attribute\Attribute;
use pvc\html\err\InvalidAttributeNameException;
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
     * @covers \pvc\html\attribute\Attribute::__construct
     */
    public function testConstruct(): void
    {
        self::assertInstanceOf(Attribute::class, $this->attribute);
    }

    /**
     * testGetNameReturnsEmptyStringWhenNameIsNotSet
     * @covers \pvc\html\attribute\Attribute::getName
     */
    public function testgetNameReturnsEmptyStringWhenNameIsNotSet(): void
    {
        $expectedName = '';
        self::assertEquals($expectedName, $this->attribute->getName());
    }

    /**
     * testSetGetName
     * @throws InvalidAttributeNameException
     * @covers \pvc\html\attribute\Attribute::setName
     * @covers \pvc\html\attribute\Attribute::getName
     */
    public function testSetGetName(): void
    {
        $testName = 'target';
        $this->attribute->setName($testName);
        self::assertEquals($testName, $this->attribute->getName());
    }

    /**
     * testSetGetTester
     * @covers \pvc\html\attribute\Attribute::setTester
     * @covers \pvc\html\attribute\Attribute::getTester
     */
    public function testSetGetTester(): void
    {
        $tester = $this->createMock(ValTesterInterface::class);
        $this->attribute->setTester($tester);
        self::assertEquals($tester, $this->attribute->getTester());
    }

    /**
     * testSetValueIsCaseSensitive
     * @covers \pvc\html\attribute\Attribute::setValueIsCaseSensitive
     * @covers \pvc\html\attribute\Attribute::valueIsCaseSensitive
     */
    public function testSetValueIsCaseSensitive(): void
    {
        self::assertFalse($this->attribute->valueIsCaseSensitive());
        $this->attribute->setValueIsCaseSensitive(true);
        self::assertTrue($this->attribute->valueIsCaseSensitive());
        $this->attribute->setValueIsCaseSensitive(false);
        self::assertFalse($this->attribute->valueIsCaseSensitive());
    }

    /**
     * testSetGlobalIsGlobal
     * @covers \pvc\html\attribute\Attribute::setGlobalYn
     * @covers \pvc\html\attribute\Attribute::isGlobalYn
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
