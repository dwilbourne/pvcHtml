<?php

/**
 * @author: Doug Wilbourne (dougwilbourne@gmail.com)
 */

declare (strict_types=1);

namespace pvcTests\html\unit_tests\attribute;

use PHPUnit\Framework\TestCase;
use pvc\html\attribute\AttributeVoid;
use pvc\html\err\InvalidAttributeIdNameException;
use pvc\html\err\InvalidNumberOfParametersException;

class AttributeVoidTest extends TestCase
{
    protected AttributeVoid $attribute;

    /**
     * @throws InvalidAttributeIdNameException
     */
    public function setUp(): void
    {
        $this->attribute = new AttributeVoid();
    }

    /**
     * testSetGetDefId
     * @covers \pvc\html\attribute\AttributeVoid::setDefId()
     * @covers \pvc\html\attribute\AttributeVoid::getDefId()
     */
    public function testSetGetDefId(): void
    {
        $testId = 'hidden';
        $this->attribute->setDefId($testId);
        self::assertEquals($testId, $this->attribute->getDefId());
    }

    /**
     * testSetNameThrowsExceptionWithInvalidName
     * @covers \pvc\html\attribute\AttributeVoid::setName
     * @covers \pvc\html\attribute\AttributeVoid::isValidAttributeIdName
     */
    public function testSetNameThrowsExceptionWithInvalidName(): void
    {
        $attribute = new AttributeVoid();
        $testName = '%7g(';
        self::expectException(InvalidAttributeIdNameException::class);
        $attribute->setName($testName);
        unset($attribute);
    }


    /**
     * testSetGetName
     * @throws InvalidAttributeIdNameException
     * @covers \pvc\html\attribute\AttributeVoid::setName
     * @covers \pvc\html\attribute\AttributeVoid::getName
     */
    public function testSetGetName(): void
    {
        $testName = 'hidden';
        $this->attribute->setName($testName);
        self::assertEquals($testName, $this->attribute->getName());
    }

    /**
     * testGlobal
     * @covers \pvc\html\attribute\AttributeVoid::setGlobal
     * @covers \pvc\html\attribute\AttributeVoid::isGlobal
     */
    public function testGlobal(): void
    {
        /**
         * default value is false
         */
        self::assertFalse($this->attribute->isGlobal());
        $this->attribute->setGlobal(true);
        self::assertTrue($this->attribute->isGlobal());
    }

    /**
     * testSetValueFailsWithParameter
     * @throws InvalidNumberOfParametersException
     * @covers \pvc\html\attribute\AttributeVoid::setValue
     */
    public function testSetValueFailsWithParameter(): void
    {
        $value = 'foo';
        self::expectException(InvalidNumberOfParametersException::class);
        $this->attribute->setValue($value);
    }

    /**
     * testSetValueSucceedsWithZeroParameters
     * @throws InvalidNumberOfParametersException
     * @covers \pvc\html\attribute\AttributeVoid::setValue
     * @covers \pvc\html\attribute\AttributeVoid::getValue
     */
    public function testSetValueSucceedsWithZeroParameters(): void
    {
        $this->attribute->setValue();
        self::assertNull($this->attribute->getValue());
    }

    /**
     * testRenderReturnsAttributeName
     * @covers \pvc\html\attribute\AttributeVoid::render
     */
    public function testRenderReturnsAttributeName(): void
    {
        $this->attribute->setName('hidden');
        $expectedOutput = $this->attribute->getName();
        self::assertEquals($expectedOutput, $this->attribute->render());
    }
}
