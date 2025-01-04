<?php

/**
 * @author: Doug Wilbourne (dougwilbourne@gmail.com)
 */

declare (strict_types=1);

namespace pvcTests\html\integration_test;

use League\Container\Container;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use pvc\html\attribute\Event;
use pvc\html\err\InvalidAttributeIdNameException;
use pvc\html\err\InvalidEventNameException;
use pvc\html\err\InvalidTagNameException;
use pvc\html\factory\definitions\implementations\league\HtmlContainer;
use pvc\html\factory\definitions\implementations\league\HtmlDefinitionFactory;
use pvc\html\factory\HtmlFactory;
use pvc\html\tag\TagVoid;
use pvc\interfaces\html\attribute\AttributeCustomDataInterface;
use pvc\interfaces\html\attribute\AttributeInterface;
use pvc\interfaces\html\factory\definitions\DefinitionFactoryInterface;
use pvc\interfaces\html\factory\definitions\DefinitionType;
use pvc\interfaces\validator\ValTesterInterface;
use Throwable;

/**
 * Class HtmlLeagueFactoryTest
 * @template VendorSpecificDefinition of DefinitionFactoryInterface
 */
class HtmlLeagueFactoryTest extends TestCase
{
    protected HtmlContainer $container;

    /**
     * @var DefinitionFactoryInterface<VendorSpecificDefinition>
     */
    protected DefinitionFactoryInterface $definitionFactory;

    /**
     * @var HtmlFactory
     */
    protected HtmlFactory $htmlFactory;
    
    public function setUp(): void
    {
        $leagueContainer = new Container();
        $this->container = new HtmlContainer($leagueContainer);
        $this->definitionFactory = new HtmlDefinitionFactory();
        $this->htmlFactory = new HtmlFactory($this->container, $this->definitionFactory);
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
     * testGetDefinitionTypes
     * @covers \pvc\html\factory\HtmlFactory::getDefinitionTypes
     */
    public function testGetDefinitionTypes(): void
    {
        $defTypes = $this->htmlFactory->getDefinitionTypes();
        self::assertIsArray($defTypes);
        self::assertTrue(in_array('html', array_keys($defTypes)));

        $attributeTypes = $this->htmlFactory->getDefinitionTypes(DefinitionType::Attribute);
        foreach($attributeTypes as $type) {
            self::assertEquals(DefinitionType::Attribute->value, $type);
        }
    }

    /**
     * testGetDefinitionType
     * @covers \pvc\html\factory\HtmlFactory::getDefinitionType()
     */
    public function testGetDefinitionType(): void
    {
        self::assertEquals('Element', $this->htmlFactory->getDefinitionType('html'));
    }

    /**
     * testGetDefinitionIds
     * @covers \pvc\html\factory\HtmlFactory::getDefinitionIds
     */
    public function testGetDefinitionIds(): void
    {
        $defTypes = $this->htmlFactory->getDefinitionTypes();
        self::assertEqualsCanonicalizing($this->htmlFactory->getDefinitionIds(), array_keys($defTypes));
    }

    /**
     * testMakeCustomData
     * @throws InvalidTagNameException
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @covers \pvc\html\factory\HtmlFactory::makeCustomData
     */
    public function testMakeCustomData(): void
    {
        $defId = 'data-foo';
        $value = 'bar';
        $valTester = $this->createMock(ValTesterInterface::class);
        $valTester->expects($this->once())->method('testValue')->with($value)->willReturn(true);

        self::assertInstanceOf(
            AttributeCustomDataInterface::class,
            $this->htmlFactory->makeCustomData($defId, $value, $valTester));
    }

    /**
     * testMakeEvents
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws InvalidEventNameException
     * @covers \pvc\html\factory\HtmlFactory::makeEvent
     */
    public function testMakeEvents(): void
    {
        $eventDefIds = $this->htmlFactory->getDefinitionIds(DefinitionType::Event);
        foreach($eventDefIds as $defId) {
            self::assertInstanceOf(Event::class, $this->htmlFactory->makeEvent($defId));
        }
    }

    /**
     * testMakeElements
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws InvalidTagNameException
     * @covers \pvc\html\factory\HtmlFactory::makeElement
     */
    public function testMakeElements(): void
    {
        $elementDefIds = $this->htmlFactory->getDefinitionIds(DefinitionType::Element);
        foreach($elementDefIds as $defId) {
            self::assertInstanceOf(TagVoid::class, $this->htmlFactory->makeElement($defId));
        }
    }

    /**
     * testMakeOthers
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws InvalidTagNameException
     * @covers \pvc\html\factory\HtmlFactory::getContainer
     */
    public function testMakeOthers(): void
    {
        $otherDefIds = $this->htmlFactory->getDefinitionIds(DefinitionType::Other);
        foreach($otherDefIds as $defId) {
            self::assertIsObject($this->htmlFactory->getContainer()->get($defId));
        }
    }

    /**
     * testMakeAttributeValueTesters
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @covers \pvc\html\factory\HtmlFactory::getContainer
     */
    public function testMakeAttributeValueTesters(): void
    {
        $attributeValueTesterDefIds = $this->htmlFactory->getDefinitionIds(DefinitionType::AttributeValueTester);
        foreach ($attributeValueTesterDefIds as $defId) {
            try {
                self::assertInstanceOf(ValTesterInterface::class, $this->htmlFactory->getContainer()->get($defId));
            } catch(Throwable $e) {
                echo 'Unable to make val tester ' . $defId . PHP_EOL;
                throw $e;
            }
        }
    }

    /**
     * testMakeAttributes
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws InvalidAttributeIdNameException
     * @covers \pvc\html\factory\HtmlFactory::makeAttribute
     */
    public function testMakeAttributes(): void
    {
        $attributeDefIds = $this->htmlFactory->getDefinitionIds(DefinitionType::Attribute);
        foreach ($attributeDefIds as $defId) {
            self::assertInstanceOf(AttributeInterface::class, $this->htmlFactory->makeAttribute($defId));
        }
    }
}
