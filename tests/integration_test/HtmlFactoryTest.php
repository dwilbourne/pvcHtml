<?php

/**
 * @author: Doug Wilbourne (dougwilbourne@gmail.com)
 */

declare (strict_types=1);

namespace pvcTests\html\integration_test;

use PHPUnit\Framework\TestCase;
use pvc\html\attribute\Event;
use pvc\html\err\InvalidAttributeIdNameException;
use pvc\html\err\InvalidEventNameException;
use pvc\html\err\InvalidTagNameException;
use pvc\html\factory\ContainerFactory;
use pvc\html\factory\HtmlFactory;
use pvc\interfaces\html\attribute\AttributeInterface;
use pvc\interfaces\html\tag\TagVoidInterface;
use pvc\interfaces\validator\ValTesterInterface;
use Throwable;

class HtmlFactoryTest extends TestCase
{
    protected ContainerFactory $containerFactory;

    protected HtmlFactory $htmlFactory;
    
    public function setUp(): void
    {
        $this->containerFactory = new ContainerFactory();
        $this->htmlFactory = new HtmlFactory($this->containerFactory);
    }

    /**
     * testMakeElementContainer
     * @covers \pvc\html\factory\HtmlFactory::__construct
     */
    public function testConstruct(): void
    {
        self::assertInstanceOf(HtmlFactory::class, $this->htmlFactory);
    }

    /**
     * testIsAmbiguousName
     * @covers \pvc\html\factory\HtmlFactory::isAmbiguousName
     */
    public function testIsAmbiguousName(): void
    {
        self::assertTrue($this->htmlFactory->isAmbiguousName('id'));
        self::assertFalse($this->htmlFactory->isAmbiguousName('ul'));
    }

    /**
     * testCanMakeElement
     * @covers \pvc\html\factory\HtmlFactory::canMakeElement
     */
    public function testCanMakeElement(): void
    {
        self::assertTrue($this->htmlFactory->canMakeElement('html'));
    }

    /**
     * testCannotMakeElement
     * @covers \pvc\html\factory\HtmlFactory::canMakeElement
     */
    public function testCannotMakeElement(): void
    {
        self::assertFalse($this->htmlFactory->canMakeElement('foo'));
    }

    /**
     * testCanMakeAttribute
     * @covers \pvc\html\factory\HtmlFactory::canMakeAttribute
     */
    public function testCanMakeAttribute(): void
    {
        self::assertTrue($this->htmlFactory->canMakeAttribute('href'));
    }

    /**
     * testCannotMakeAttribute
     * @covers \pvc\html\factory\HtmlFactory::canMakeAttribute
     */
    public function testCannotMakeAttribute(): void
    {
        self::assertFalse($this->htmlFactory->canMakeAttribute('foo'));
    }

    /**
     * testCanMakeEvent
     * @covers \pvc\html\factory\HtmlFactory::canMakeEvent
     */
    public function testCanMakeEvent(): void
    {
        self::assertTrue($this->htmlFactory->canMakeEvent('onchange'));
    }

    /**
     * testCannotMakeEvent
     * @covers \pvc\html\factory\HtmlFactory::canMakeEvent
     */
    public function testCannotMakeEvent(): void
    {
        self::assertFalse($this->htmlFactory->canMakeEvent('foo'));
    }


    /**
     * testMakeElementThrowsExceptionForUnknownElement
     * @throws InvalidTagNameException
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     * @covers \pvc\html\factory\HtmlFactory::makeElement
     */
    public function testMakeElementThrowsExceptionForUnknownElement(): void
    {
        self::expectException(InvalidTagNameException::class);
        $this->htmlFactory->makeElement('foo');
    }

    /**
     * testMakeAttributeThrowsExceptionForUnknownAttribute
     * @throws InvalidAttributeIdNameException
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     * @covers \pvc\html\factory\HtmlFactory::makeAttribute
     */
    public function testMakeAttributeThrowsExceptionForUnknownAttribute(): void
    {
        self::expectException(InvalidAttributeIdNameException::class);
        $this->htmlFactory->makeAttribute('foo');
    }

    /**
     * testMakeEventThrowsExceptionForUnknownEvent
     * @throws InvalidEventNameException
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     * @covers \pvc\html\factory\HtmlFactory::makeEvent
     */
    public function testMakeEventThrowsExceptionForUnknownEvent(): void
    {
        self::expectException(InvalidEventNameException::class);
        $this->htmlFactory->makeEvent('foo');
    }

    /**
     * testMakeEvents
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     * @throws \pvc\html\err\InvalidEventNameException
     * @covers \pvc\html\factory\HtmlFactory::makeEvent
     */
    public function testMakeEvents(): void
    {
        foreach($this->containerFactory->getEventNames() as $name) {
            self::assertInstanceOf(Event::class, $this->htmlFactory->makeEvent($name));
        }
    }

    /**
     * testMakeElements
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     * @throws \pvc\html\err\InvalidTagNameException
     * @covers \pvc\html\factory\HtmlFactory::makeElement
     */
    public function testMakeElements(): void
    {
        foreach($this->containerFactory->getElementNames() as $elementName) {
            try {
                self::assertInstanceOf(TagVoidInterface::class, $this->htmlFactory->makeElement($elementName));
            } catch (Throwable $e) {
                echo 'unable to create element ' . $elementName . PHP_EOL;
                throw $e;
            }
        }
    }

    /**
     * testMakeAttributes
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     * @throws \pvc\html\err\InvalidAttributeIdNameException
     * @covers \pvc\html\factory\HtmlFactory::makeAttribute
     */
    public function testMakeAttributes(): void
    {
        foreach ($this->containerFactory->getAttributeNames() as $attributeName) {
            try {
                self::assertInstanceOf(AttributeInterface::class, $this->htmlFactory->makeAttribute($attributeName));
            } catch (Throwable $e) {
                echo 'unable to create attribute ' . $attributeName . PHP_EOL;
                throw $e;
            }
        }
    }

    /**
     * testMakeAttributeValueTesters
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     * @covers \pvc\html\factory\HtmlFactory::makeAttributeValueTester
     */
    public function testMakeAttributeValueTesters(): void
    {
        foreach ($this->containerFactory->getAttributeValueTesterNames() as $attributeValueTesterName) {
            try {
                self::assertInstanceOf(ValTesterInterface::class, $this->htmlFactory->makeAttributeValueTester
                ($attributeValueTesterName));
            } catch (Throwable $e) {
                echo 'unable to create attribute value tester ' . $attributeValueTesterName . PHP_EOL;
                throw $e;
            }
        }
    }
}
