<?php

/**
 * @author: Doug Wilbourne (dougwilbourne@gmail.com)
 */

declare (strict_types=1);

namespace pvcTests\html\attribute;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use pvc\html\attribute\AttributeCustomData;
use pvc\html\err\InvalidCustomDataNameException;
use pvc\interfaces\html\config\HtmlConfigInterface;
use pvc\interfaces\validator\ValTesterInterface;

class AttributeCustomDataTest extends TestCase
{
    protected AttributeCustomData $attribute;

    protected HtmlConfigInterface|MockObject $htmlConfig;

    protected ValTesterInterface|MockObject $valTester;

    public function setUp(): void
    {
        $this->valTester = $this->createMock(ValTesterInterface::class);
        $this->htmlConfig = $this->createMock(HtmlConfigInterface::class);
        $this->attribute = new AttributeCustomData(
            $this->valTester,
            $this->htmlConfig
        );
    }

    /**
     * testConstruct
     * @covers \pvc\html\attribute\AttributeCustomData::__construct
     */
    public function testConstruct(): void
    {
        self::assertInstanceOf(AttributeCustomData::class, $this->attribute);
    }

    /**
     * testSetNameFailsWithInvalidCustomDataName
     * @throws InvalidCustomDataNameException
     * @covers \pvc\html\attribute\AttributeCustomData::setName
     */
    public function testSetNameFailsWithInvalidCustomDataName(): void
    {
        /**
         * must be lower case and/or numbers
         */
        $customDataName = 'HOB!@';
        self::expectException(InvalidCustomDataNameException::class);
        $this->attribute->setName($customDataName);
    }

    /**
     * testSetNameSucceeds
     * @throws InvalidCustomDataNameException
     * @covers \pvc\html\attribute\AttributeCustomData::setName
     * @covers \pvc\html\attribute\AttributeCustomData::getName
     */
    public function testSetNameSucceeds(): void
    {
        $customDataName = 'foo';
        $this->attribute->setName($customDataName);
        $expectedResult = 'data-' . $customDataName;
        self::assertEquals($expectedResult, $this->attribute->getName());
    }


}
