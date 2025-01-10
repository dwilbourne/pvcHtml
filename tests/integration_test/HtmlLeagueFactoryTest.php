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
use pvc\html\builder\definitions\implementations\league\HtmlContainer;
use pvc\html\builder\definitions\implementations\league\HtmlDefinitionFactory;
use pvc\html\builder\HtmlBuilder;
use pvc\html\element\ElementVoid;
use pvc\html\err\InvalidAttributeIdNameException;
use pvc\html\err\InvalidEventNameException;
use pvc\html\err\InvalidTagNameException;
use pvc\interfaces\html\attribute\AttributeCustomDataInterface;
use pvc\interfaces\html\attribute\AttributeInterface;
use pvc\interfaces\html\builder\definitions\DefinitionFactoryInterface;
use pvc\interfaces\html\builder\definitions\DefinitionType;
use pvc\interfaces\html\builder\HtmlContainerInterface;
use pvc\interfaces\validator\ValTesterInterface;
use Throwable;

/**
 * Class HtmlLeagueFactoryTest
 * @template VendorSpecificDefinition of DefinitionFactoryInterface
 */
class HtmlLeagueFactoryTest extends TestCase
{
    protected HtmlContainerInterface $container;

    /**
     * @var DefinitionFactoryInterface<VendorSpecificDefinition>
     */
    protected DefinitionFactoryInterface $definitionFactory;

    /**
     * @var HtmlBuilder
     */
    protected HtmlBuilder $htmlBuilder;
    
    public function setUp(): void
    {
        $leagueContainer = new Container();
        $this->container = new HtmlContainer($leagueContainer);
        $this->definitionFactory = new HtmlDefinitionFactory();
        $this->htmlBuilder = new HtmlBuilder($this->container, $this->definitionFactory);
    }

    /**
     * testMakeElementContainer
     * @covers \pvc\html\builder\HtmlBuilder::__construct
     */
    public function testConstruct(): void
    {
        self::assertInstanceOf(HtmlBuilder::class, $this->htmlBuilder);
    }

    /**
     * testGetDefinitionTypes
     * @covers \pvc\html\builder\HtmlBuilder::getDefinitionTypes
     */
    public function testGetDefinitionTypes(): void
    {
        $defTypes = $this->htmlBuilder->getDefinitionTypes();
        self::assertIsArray($defTypes);
        self::assertTrue(in_array('html', array_keys($defTypes)));

        $attributeTypes = $this->htmlBuilder->getDefinitionTypes(DefinitionType::Attribute);
        foreach($attributeTypes as $defType) {
            self::assertEquals(DefinitionType::Attribute, $defType);
        }
    }

    /**
     * testGetDefinitionType
     * @covers \pvc\html\builder\HtmlBuilder::getDefinitionType()
     */
    public function testGetDefinitionType(): void
    {
        self::assertEquals(DefinitionType::Element, $this->htmlBuilder->getDefinitionType('html'));
    }

    /**
     * testGetDefinitionIds
     * @covers \pvc\html\builder\HtmlBuilder::getDefinitionIds
     */
    public function testGetDefinitionIds(): void
    {
        $defTypes = $this->htmlBuilder->getDefinitionTypes();
        self::assertEqualsCanonicalizing($this->htmlBuilder->getDefinitionIds(), array_keys($defTypes));
    }

    /**
     * testMakeCustomData
     * @throws InvalidTagNameException
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @covers \pvc\html\builder\HtmlBuilder::makeCustomData
     */
    public function testMakeCustomData(): void
    {
        $defId = 'data-foo';
        $value = 'bar';
        $valTester = $this->createMock(ValTesterInterface::class);
        $valTester->expects($this->once())->method('testValue')->with($value)->willReturn(true);

        self::assertInstanceOf(
            AttributeCustomDataInterface::class,
            $this->htmlBuilder->makeCustomData($defId, $value, $valTester));
    }

    /**
     * testMakeEvents
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws InvalidEventNameException
     * @covers \pvc\html\builder\HtmlBuilder::makeEvent
     */
    public function testMakeEvents(): void
    {
        $eventDefIds = $this->htmlBuilder->getDefinitionIds(DefinitionType::Event);
        foreach($eventDefIds as $defId) {
            self::assertInstanceOf(Event::class, $this->htmlBuilder->makeEvent($defId));
        }
    }

    /**
     * testMakeElements
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws InvalidTagNameException
     * @covers \pvc\html\builder\HtmlBuilder::makeElement
     */
    public function testMakeElements(): void
    {
        $elementDefIds = $this->htmlBuilder->getDefinitionIds(DefinitionType::Element);
        foreach($elementDefIds as $defId) {
            self::assertInstanceOf(ElementVoid::class, $this->htmlBuilder->makeElement($defId));
        }
    }

    /**
     * testMakeOthers
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws InvalidTagNameException
     * @covers \pvc\html\builder\HtmlBuilder::getContainer
     */
    public function testMakeOthers(): void
    {
        $otherDefIds = $this->htmlBuilder->getDefinitionIds(DefinitionType::Other);
        foreach($otherDefIds as $defId) {
            self::assertIsObject($this->htmlBuilder->getContainer()->get($defId));
        }
    }

    /**
     * testMakeAttributeValueTesters
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @covers \pvc\html\builder\HtmlBuilder::getContainer
     */
    public function testMakeAttributeValueTesters(): void
    {
        $attributeValueTesterDefIds = $this->htmlBuilder->getDefinitionIds(DefinitionType::AttributeValueTester);
        foreach ($attributeValueTesterDefIds as $defId) {
            try {
                self::assertInstanceOf(ValTesterInterface::class, $this->htmlBuilder->getContainer()->get($defId));
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
     * @covers \pvc\html\builder\HtmlBuilder::makeAttribute
     */
    public function testMakeAttributes(): void
    {
        $attributeDefIds = $this->htmlBuilder->getDefinitionIds(DefinitionType::Attribute);
        foreach ($attributeDefIds as $defId) {
            self::assertInstanceOf(AttributeInterface::class, $this->htmlBuilder->makeAttribute($defId));
        }
    }
}
