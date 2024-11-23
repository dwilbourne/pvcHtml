<?php

/**
 * @author: Doug Wilbourne (dougwilbourne@gmail.com)
 */
declare (strict_types=1);

namespace pvcTests\html\unit_tests\factory;

use PHPUnit\Framework\MockObject\MockObject;
use pvc\html\err\DuplicateDefinitionIdException;
use pvc\html\err\InvalidAttributeIdNameException;
use pvc\html\err\DTOInvalidPropertyValueException;
use pvc\html\err\InvalidDefinitionsFileException;
use pvc\html\err\InvalidEventNameException;
use pvc\html\err\InvalidTagNameException;
use pvc\html\factory\HtmlFactory;
use PHPUnit\Framework\TestCase;
use pvc\interfaces\html\attribute\AttributeInterface;
use pvc\interfaces\html\attribute\EventInterface;
use pvc\interfaces\html\factory\definitions\DefinitionFactoryInterface;
use pvc\interfaces\html\factory\HtmlContainerInterface;
use pvc\interfaces\html\factory\HtmlFactoryInterface;
use pvc\interfaces\html\tag\TagVoidInterface;

/**
 * Class HtmlLeagueFactoryTest
 * @template VendorSpecificDefinition of DefinitionFactoryInterface
 */
class HtmlFactoryTest extends TestCase
{
    /**
     * @var HtmlContainerInterface<VendorSpecificDefinition>|MockObject
     */
    protected HtmlContainerInterface|MockObject $container;

    /**
     * @var DefinitionFactoryInterface<VendorSpecificDefinition>|MockObject
     */
    protected DefinitionFactoryInterface|MockObject $definitionFactory;

    /**
     * @var HtmlFactory<VendorSpecificDefinition>
     */
    protected HtmlFactory $htmlFactory;

    protected string $goodDefs = __DIR__ . '/definitions/json/GoodDefinitions.json';

    protected string $badDef = __DIR__ . '/definitions/json/BadDefinition.json';

    protected string $badJson = __DIR__ . '/definitions/json/BadJson.json';

    protected string $noJson = __DIR__ . '/definitions/json/NoDefinition.json';

    protected string $duplicateDef = __DIR__ . '/definitions/json/DuplicateDefinitions.json';

    public function setUp(): void
    {
        $this->container = $this->createMock(HtmlContainerInterface::class);
        $this->definitionFactory = $this->createMock(DefinitionFactoryInterface::class);
    }

    /**
     * testConstruct
     * @covers \pvc\html\factory\HtmlFactory::__construct
     */
    public function testConstruct(): void
    {
        /**
         * does not matter what a definition is for the purposes of this test (it's generic)
         */
        $def = new \stdClass();
        $this->definitionFactory->method('makeElementDefinition')->willReturn($def);
        $this->definitionFactory->method('makeAttributeDefinition')->willReturn($def);
        $this->definitionFactory->method('makeEventDefinition')->willReturn($def);
        $this->definitionFactory->method('makeAttributeValueTesterDefinition')->willReturn($def);
        $this->definitionFactory->method('makeOtherDefinition')->willReturn($def);
        $this->htmlFactory = new HtmlFactory($this->container, $this->definitionFactory, $this->goodDefs);
        self::assertInstanceOf(HtmlFactory::class, $this->htmlFactory);
    }

    /**
     * testSetGetContainer
     * @covers \pvc\html\factory\HtmlFactory::setContainer
     * @covers \pvc\html\factory\HtmlFactory::getContainer
     */
    public function testSetGetContainer(): void
    {
        $this->htmlFactory = new HtmlFactory($this->container, $this->definitionFactory, $this->goodDefs);
        self::assertEquals($this->container, $this->htmlFactory->getContainer());
    }

    /**
     * testSetGetDefinitionsFactory
     * @covers \pvc\html\factory\HtmlFactory::setDefinitionFactory()
     * @covers \pvc\html\factory\HtmlFactory::getDefinitionFactory()
     */
    public function testSetGetDefinitionsFactory(): void
    {
        $this->htmlFactory = new HtmlFactory($this->container, $this->definitionFactory, $this->goodDefs);
        self::assertEquals($this->definitionFactory, $this->htmlFactory->getDefinitionFactory());
    }

    /**
     * testSetDefinitionsFileThrowsExceptionWithInvalidFileName
     * @throws DTOInvalidPropertyValueException
     * @covers \pvc\html\factory\HtmlFactory::setDefinitionsFile
     */
    public function testSetDefinitionsFileThrowsExceptionWithInvalidFileName(): void
    {
        self::expectException(InvalidDefinitionsFileException::class);
        $this->htmlFactory = new HtmlFactory($this->container, $this->definitionFactory, 'foo');
    }

    /**
     * testSetGetDefinitionsFile
     * @covers \pvc\html\factory\HtmlFactory::setDefinitionsFile
     * @covers \pvc\html\factory\HtmlFactory::getDefinitionsFile
     */
    public function testSetGetDefinitionsFile(): void
    {
        $testFile = __DIR__ . '/definitions/json/GoodDefinitions.json';
        self::assertInstanceOf(
            HtmlFactoryInterface::class,
            new HtmlFactory($this->container, $this->definitionFactory, $testFile)
        );
    }
    /**
     * testGetDefinitionsArrayThrowsExceptionWithBadFileContents
     * @throws InvalidDefinitionsFileException
     * @covers \pvc\html\factory\HtmlFactory::getDefinitionArray
     */
    public function testGetDefinitionsArrayThrowsExceptionWithBadFileContents(): void
    {
        self::expectException(InvalidDefinitionsFileException::class);
        $this->htmlFactory = new HtmlFactory($this->container, $this->definitionFactory, $this->badJson);
    }

    /**
     * testGetDefinitionsArrayThrowsExceptionWithNoFileContents
     * @covers \pvc\html\factory\HtmlFactory::getDefinitionArray
     */
    public function testGetDefinitionsArrayThrowsExceptionWithNoFileContents(): void
    {
        self::expectException(InvalidDefinitionsFileException::class);
        $this->htmlFactory = new HtmlFactory($this->container, $this->definitionFactory, $this->noJson);
    }

    /**
     * testHydrateContainer
     * @throws InvalidDefinitionsFileException
     * @covers \pvc\html\factory\HtmlFactory::__construct
     * @covers \pvc\html\factory\HtmlFactory::hydrateContainer
     * @covers \pvc\html\factory\HtmlFactory::getDefinitionArray
     * @covers \pvc\html\factory\HtmlFactory::makeDefinition
     */
    public function testHydrateContainer(): void
    {
        $def = 'def';

        /**
         * there are 6 definitions in the test data
         */
        $this->definitionFactory->expects($this->once())->method('makeElementDefinition')->willReturn($def);
        $this->definitionFactory->expects($this->exactly(2))->method('makeAttributeDefinition')->willReturn($def);
        $this->definitionFactory->expects($this->once())->method('makeEventDefinition')->willReturn($def);
        $this->definitionFactory->expects($this->once())->method('makeAttributeValueTesterDefinition')->willReturn($def);
        $this->definitionFactory->expects($this->once())->method('makeOtherDefinition')->willReturn($def);

        $this->container
            ->expects($this->exactly(6))
            ->method('add');

        $this->htmlFactory = new HtmlFactory($this->container,$this->definitionFactory, $this->goodDefs);
    }

    /**
     * testHydateContainerThrowsExceptionWithDuplicateDefId
     * @covers \pvc\html\factory\HtmlFactory::hydrateContainer()
     */
    public function testHydateContainerThrowsExceptionWithDuplicateDefId(): void
    {
        $slug = 'def';

        $this->definitionFactory
            ->expects($this->exactly(2))
            ->method('makeAttributeDefinition')
            ->willReturn($slug);
        self::expectException(DuplicateDefinitionIdException::class);
        $this->htmlFactory = new HtmlFactory($this->container,$this->definitionFactory, $this->duplicateDef);
    }

    /**
     * testing the getDefinitionIds method in a unit test is kind of pointless, all it would do is test that we set
     * up a mock iterator to return the expected values.  See the integration test for a proper demonstration.
     */

    protected function makeMethodsSuccessSetup(string $defId, MockObject $madeObject): void
    {
        $this->htmlFactory = new HtmlFactory($this->container, $this->definitionFactory, $this->goodDefs);
        $this->container->expects($this->once())->method('has')->with($defId)->willReturn(true);
        $this->container->expects($this->once())->method('get')->with($defId)->willReturn($madeObject);
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
        $defId = 'foo';
        $this->htmlFactory = new HtmlFactory($this->container,$this->definitionFactory, $this->goodDefs);
        self::expectException(InvalidTagNameException::class);
        $this->htmlFactory->makeElement($defId);
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
        $defId = 'foo';
        $this->htmlFactory = new HtmlFactory($this->container,$this->definitionFactory, $this->goodDefs);
        self::expectException(InvalidAttributeIdNameException::class);
        $this->htmlFactory->makeAttribute($defId);
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
        $defId = 'foo';
        $this->htmlFactory = new HtmlFactory($this->container,$this->definitionFactory, $this->goodDefs);
        self::expectException(InvalidEventNameException::class);
        $this->htmlFactory->makeEvent($defId);
    }

    /**
     * testMakeElement
     * @throws InvalidTagNameException
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     * @covers \pvc\html\factory\HtmlFactory::makeElement()
     */
    public function testMakeElement(): void
    {
        $elementDefId = 'address';
        $mockElement = $this->createMock(TagVoidInterface::class);
        $this->makeMethodsSuccessSetup($elementDefId, $mockElement);
        $mockElement->expects($this->once())->method('setHtmlFactory')->with($this->htmlFactory);
        self::assertequals($mockElement, $this->htmlFactory->makeElement($elementDefId));
    }

    /**
     * testMakeAttribute
     * @throws InvalidTagNameException
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     * @covers \pvc\html\factory\HtmlFactory::makeAttribute
     * @covers \pvc\html\factory\HtmlFactory::isAmbiguousName
     */
    public function testMakeAttribute(): void
    {
        /**
         * label is an ambiguous identifier which is present in the test data
         */
        $attributeDefId = 'label';
        $mockAttribute = $this->createMock(AttributeInterface::class);
        $this->makeMethodsSuccessSetup($attributeDefId . '_attr', $mockAttribute);
        self::assertequals($mockAttribute, $this->htmlFactory->makeAttribute($attributeDefId));
    }

    /**
     * testMakeEvent
     * @throws InvalidAttributeIdNameException
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     * @covers \pvc\html\factory\HtmlFactory::makeEvent()
     */
    public function testMakeEvent(): void
    {
        $eventDefId = 'onabort';
        $mockEvent = $this->createMock(EventInterface::class);
        $this->makeMethodsSuccessSetup($eventDefId, $mockEvent);
        self::assertequals($mockEvent, $this->htmlFactory->makeAttribute($eventDefId));
    }
}
