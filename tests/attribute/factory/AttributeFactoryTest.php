<?php

/**
 * @author: Doug Wilbourne (dougwilbourne@gmail.com)
 */
declare (strict_types=1);

namespace pvcTests\html\attribute\factory;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use pvc\html\attribute\abstract\Attribute;
use pvc\html\attribute\abstract\AttributeCustomData;
use pvc\html\attribute\abstract\Event;
use pvc\html\attribute\factory\AttributeFactory;
use pvc\html\err\InvalidAttributeException;
use pvc\html\err\InvalidEventNameException;
use pvc\interfaces\validator\ValTesterInterface;

class AttributeFactoryTest extends TestCase
{
    protected ValTesterInterface|MockObject $defaultValTester;

    protected ValTesterInterface|MockObject $customDataNameTester;

    protected ContainerInterface|MockObject $container;

    protected AttributeFactory $factory;

    public function setUp(): void
    {
        $this->defaultValTester = $this->createMock(ValTesterInterface::class);
        $this->customDataNameTester = $this->createMock(ValTesterInterface::class);
        $this->container = $this->createMock(ContainerInterface::class);
        $this->factory = new AttributeFactory($this->defaultValTester, $this->customDataNameTester, $this->container);
    }

    /**
     * testConstruction
     * @covers \pvc\html\attribute\factory\AttributeFactory::__construct
     */
    public function testConstruction(): void
    {
        self::assertInstanceOf(AttributeFactory::class, $this->factory);
    }

    /**
     * testMakeAttributeThrowsExceptionWithInvalidName
     * @throws \pvc\html\err\InvalidAttributeNameException
     * @covers \pvc\html\attribute\factory\AttributeFactory::makeAttribute()
     */
    public function testMakeAttributeThrowsExceptionWithInvalidName(): void
    {
        $testName = 'foo';
        self::expectException(InvalidAttributeException::class);
        $this->factory->makeAttribute($testName);
    }

    /**
     * testFactoryUsesDefaultTesterIfContainerDoesNotHaveAttributeTester
     * @throws \pvc\html\err\InvalidAttributeNameException
     * @covers \pvc\html\attribute\factory\AttributeFactory::makeAttribute()
     */
    public function testFactoryUsesDefaultTesterIfContainerDoesNotHaveAttributeTester(): void
    {
        $attributeName = 'target';
        $this->container->expects($this->once())->method('has')->with($attributeName)->willReturn(false);
        /**
         * because you cannot use Phpunit to test statics, this is really something of an integration test, but it is
         * best we can do for now....
         */
        $attribute = $this->factory->makeAttribute($attributeName);
        self::assertInstanceOf(Attribute::class, $attribute);
        self::assertEquals($this->defaultValTester, $attribute->getTester());
    }

    /**
     * testFactoryUsesTesterFromContainerIfItExists
     * @throws \pvc\html\err\InvalidAttributeNameException
     * @covers \pvc\html\attribute\factory\AttributeFactory::makeAttribute()
     */
    public function testFactoryUsesTesterFromContainerIfItExists(): void
    {
        $newTester = $this->createMock(ValTesterInterface::class);
        $attributeName = 'target';
        $this->container->expects($this->once())->method('has')->with($attributeName)->willReturn(true);
        $this->container->expects($this->once())->method('get')->with($attributeName)->willReturn($newTester);

        $attribute = $this->factory->makeAttribute($attributeName);
        self::assertInstanceOf(Attribute::class, $attribute);
        self::assertEquals($newTester, $attribute->getTester());
    }

    /**
     * testMakeCustomDataAttribute
     * @throws \pvc\html\err\InvalidCustomDataNameException
     * @covers \pvc\html\attribute\factory\AttributeFactory::makeCustomDataAttribute
     */
    public function testMakeCustomDataAttribute(): void
    {
        $customDataName = 'foo';
        $this->customDataNameTester->expects($this->once())->method('testValue')->with($customDataName)->willReturn(true);
        $attribute = $this->factory->makeCustomDataAttribute($customDataName, $this->defaultValTester);
        self::assertInstanceOf(AttributeCustomData::class, $attribute);
        self::assertEquals($customDataName, $attribute->getName());
        self::assertEquals($this->defaultValTester, $attribute->getTester());
    }

    /**
     * testMakeEvent
     * @covers \pvc\html\attribute\factory\AttributeFactory::makeEvent
     */
    public function testMakeEvent(): void
    {
        $eventName = 'onclick';
        self::assertInstanceOf(Event::class, $this->factory->makeEvent($eventName));
    }

    /**
     * testMakeEventFails
     * @covers \pvc\html\attribute\factory\AttributeFactory::makeEvent
     */
    public function testMakeEventFails(): void
    {
        $eventName = 'foo';
        self::expectException(InvalidEventNameException::class);
        $event = $this->factory->makeEvent($eventName);
        unset($event);
    }
}
