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
use pvc\interfaces\html\config\HtmlConfigInterface;
use pvc\interfaces\validator\ValTesterInterface;

class AttributeTest extends TestCase
{
    protected HtmlConfigInterface $htmlConfig;

    protected ValTesterInterface|MockObject $tester;

    protected Attribute $attribute;

    public function setUp(): void
    {
        $this->htmlConfig = $this->createMock(HtmlConfigInterface::class);
        $this->tester = $this->createMock(ValTesterInterface::class);
        $this->attribute = $this->getMockBuilder(Attribute::class)
            ->setConstructorArgs([$this->tester, $this->htmlConfig])
            ->getMockForAbstractClass();
    }

    /**
     * testConstruct
     * @covers Attribute::__construct
     */
    public function testConstruct(): void
    {
        self::assertInstanceOf(Attribute::class, $this->attribute);
    }

    /**
     * testSetGetHtmlConfig
     * @covers Attribute::getHtmlConfig
     * @covers Attribute::setHtmlConfig
     */
    public function testSetGetHtmlConfig(): void
    {
        $config = $this->createMock(HtmlConfigInterface::class);
        $this->attribute->setHtmlConfig($config);
        self::assertEquals($config, $this->attribute->getHtmlConfig());
    }

    /**
     * testSetInvalidNameThrowsException
     * @covers Attribute::setName
     */
    public function testSetInvalidNameThrowsException(): void
    {
        $testName = 'foo';
        $this->htmlConfig->method('isValidAttributeName')->with($testName)->willReturn(false);
        self::expectException(InvalidAttributeNameException::class);
        $this->attribute->setName($testName);
    }

    /**
     * testGetNameReturnsEmptyStringWhenNameIsNotSet
     * @covers Attribute::getName
     */
    public function testgetNameReturnsEmptyStringWhenNameIsNotSet(): void
    {
        $expectedName = '';
        self::assertEquals($expectedName, $this->attribute->getName());
    }

    /**
     * testSetGetName
     * @throws InvalidAttributeNameException
     * @covers Attribute::setName
     * @covers Attribute::getName
     */
    public function testSetGetName(): void
    {
        $testName = 'target';
        $this->htmlConfig->method('isValidAttributeName')->with($testName)->willReturn(true);
        $this->attribute->setName($testName);
        self::assertEquals($testName, $this->attribute->getName());
    }

    /**
     * testSetGetTester
     * @covers Attribute::setTester
     * @covers Attribute::getTester
     */
    public function testSetGetTester(): void
    {
        $tester = $this->createMock(ValTesterInterface::class);
        $this->attribute->setTester($tester);
        self::assertEquals($tester, $this->attribute->getTester());
    }

    /**
     * testSetIsCaseSensitive
     * @covers Attribute::setCaseSensitive
     * @covers Attribute::valueIsCaseSensitive
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
