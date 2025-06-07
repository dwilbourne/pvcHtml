<?php

namespace pvcTests\html\unit_tests\factory;

use PHPUnit\Framework\MockObject\MockObject;
use Psr\Container\ContainerInterface;
use pvc\html\attribute\AttributeCustomData;
use pvc\html\err\InvalidAttributeException;
use pvc\html\err\InvalidEventNameException;
use pvc\html\err\InvalidTagNameException;
use pvc\html\factory\HtmlFactory;
use PHPUnit\Framework\TestCase;
use pvc\interfaces\html\attribute\AttributeInterface;
use pvc\interfaces\html\attribute\EventInterface;
use pvc\interfaces\html\element\ElementInterface;
use pvc\interfaces\html\element\ElementVoidInterface;

class HtmlFactoryTest extends TestCase
{
    protected HtmlFactory $factory;

    protected ContainerInterface|MockObject $container;

    public function setUp(): void
    {
        $this->container = $this->createMock(ContainerInterface::class);
        $this->factory = new HtmlFactory($this->container);
    }

    /**
     * @return void
     * @throws InvalidTagNameException
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     * @covers \pvc\html\factory\HtmlFactory::makeElement
     */
    public function testMakeElementThrowsExceptionWithBadElementName(): void
    {
        $badElementName = 'foo';
        self::expectException(InvalidTagNameException::class);
        $this->factory->makeElement($badElementName);
    }

    /**
     * @return void
     * @throws InvalidTagNameException
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     * @covers \pvc\html\factory\HtmlFactory::makeElement
     */
    public function testMakeElementGetsElementFromContainer(): void
    {
        $elementName = 'foo';
        $element = $this->createMock(ElementVoidInterface::class);
        $this->container->expects($this->once())
            ->method('has')
            ->with($elementName)
            ->willReturn(true);
        $this->container->expects($this->once())
            ->method('get')
            ->with($elementName)
            ->willReturn($element);
        self::assertSame($element, $this->factory->makeElement($elementName));
    }

    /**
     * @return void
     * @throws InvalidTagNameException
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     * @covers \pvc\html\factory\HtmlFactory::makeAttribute
     */
    public function testMakeAttributeThrowsExceptionWithBadAttributeName(): void
    {
        $badAttributeName = 'foo';
        self::expectException(InvalidAttributeException::class);
        $this->factory->makeAttribute($badAttributeName);
    }

    /**
     * @return void
     * @throws InvalidTagNameException
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     * @covers \pvc\html\factory\HtmlFactory::makeAttribute
     */
    public function testMakeAttributeGetsAttributeFromContainer(): void
    {
        $attributeName = 'foo';
        $attribute = $this->createMock(AttributeInterface::class);
        $this->container->expects($this->once())
            ->method('has')
            ->with($attributeName)
            ->willReturn(true);
        $this->container->expects($this->once())
            ->method('get')
            ->with($attributeName)
            ->willReturn($attribute);
        self::assertSame($attribute, $this->factory->makeAttribute($attributeName));
    }


    /**
     * @return void
     * @throws InvalidTagNameException
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     * @covers \pvc\html\factory\HtmlFactory::makeEvent
     */
    public function testMakeEventThrowsExceptionWithBadEventName(): void
    {
        $badEventName = 'foo';
        self::expectException(InvalidEventNameException::class);
        $this->factory->makeEvent($badEventName);
    }

    /**
     * @return void
     * @throws InvalidTagNameException
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     * @covers \pvc\html\factory\HtmlFactory::makeEvent
     */
    public function testMakeEventGetsEventFromContainer(): void
    {
        $eventName = 'foo';
        $event = $this->createMock(EventInterface::class);
        $this->container->expects($this->once())
            ->method('has')
            ->with($eventName)
            ->willReturn(true);
        $this->container->expects($this->once())
            ->method('get')
            ->with($eventName)
            ->willReturn($event);
        self::assertSame($event, $this->factory->makeEvent($eventName));
    }

    /**
     * @return void
     * @covers \pvc\html\factory\HtmlFactory::makeCustomData
     */
    public function testMakeCustomDataHasDefaults(): void
    {
        $name = 'data-foo';
        self::assertInstanceOf(AttributeCustomData::class, $this->factory->makeCustomData($name));
    }

}
