<?php

/**
 * @author: Doug Wilbourne (dougwilbourne@gmail.com)
 */

declare (strict_types=1);

namespace pvcTests\html\attribute\factory;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use pvc\html\attribute\abstract\AttributeSingleValue;
use pvc\html\attribute\factory\CustomDataAttributeFactory;
use pvc\html\err\InvalidCustomDataNameException;
use pvc\interfaces\validator\ValTesterInterface;

class CustomDataAttributeFactoryTest extends TestCase
{
    protected ValTesterInterface|MockObject $tester;

    protected CustomDataAttributeFactory $factory;

    public function setUp(): void
    {
        $this->tester = $this->createMock(ValTesterInterface::class);
        $this->factory = new CustomDataAttributeFactory($this->tester);
    }

    /**
     * testConstruct
     * @covers \pvc\html\attribute\factory\CustomDataAttributeFactory::__construct
     */
    public function testConstruct(): void
    {
        self::assertInstanceOf(CustomDataAttributeFactory::class, $this->factory);
    }

    /**
     * testMakeCustomDataSucceeds
     * @throws \pvc\html\err\InvalidCustomDataNameException
     * @covers \pvc\html\attribute\factory\CustomDataAttributeFactory::makeCustomData
     */
    public function testMakeCustomDataSucceeds(): void
    {
        $customDataName = 'foo';
        $this->tester->expects($this->once())->method('testValue')->with($customDataName)->willReturn(true);
        $attribute = $this->factory->makeCustomData($customDataName);
        self::assertInstanceOf(AttributeSingleValue::class, $attribute);
    }

    /**
     * testMakeCustomDataFailsWithInvalidCustomDataName
     * @throws InvalidCustomDataNameException
     * @covers \pvc\html\attribute\factory\CustomDataAttributeFactory::makeCustomData
     */
    public function testMakeCustomDataFailsWithInvalidCustomDataName(): void
    {
        $customDataName = 'foo';
        $this->tester->expects($this->once())->method('testValue')->with($customDataName)->willReturn(false);
        self::expectException(InvalidCustomDataNameException::class);
        $attribute = $this->factory->makeCustomData($customDataName);
        unset($attribute);
    }
}
