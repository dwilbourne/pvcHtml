<?php

/**
 * @author: Doug Wilbourne (dougwilbourne@gmail.com)
 */
declare (strict_types=1);

namespace pvcTests\html\unit_tests\factory\definitions\implementations\league;

use League\Container\Definition\Definition;
use PHPUnit\Framework\TestCase;
use pvc\html\err\DTOInvalidPropertyValueException;
use pvc\html\factory\definitions\implementations\league\LeagueDefinitionFactory;
use pvc\interfaces\html\factory\definitions\AbstractDefinitionFactoryInterface;
use pvc\interfaces\html\factory\definitions\DefinitionFactoryInterface;

/**
 * Class LeagueDefinitionFactoryTest
 *
 * @phpstan-import-type ElementDef from AbstractDefinitionFactoryInterface
 * @phpstan-import-type AttributeDef from AbstractDefinitionFactoryInterface
 * @phpstan-import-type AttributeValueTesterDef from AbstractDefinitionFactoryInterface
 * @phpstan-import-type EventDef from AbstractDefinitionFactoryInterface
 * @phpstan-import-type OtherDef from AbstractDefinitionFactoryInterface
 */
class LeagueDefinitionFactoryTest extends TestCase
{
    protected DefinitionFactoryInterface $definitionFactory;

    protected string $fixturesDir = __DIR__ . '/fixture/';

    public function setUp(): void
    {
        $this->definitionFactory = new LeagueDefinitionFactory();
    }

    /**
     * testMakeAttributeDefinition
     * @covers \pvc\html\factory\definitions\implementations\league\LeagueDefinitionFactory::makeAttributeDefinition
     */
    public function testMakeAttributeDefinition(): void
    {
        /** @var AttributeDef $attributeDef */
        $attributeDef = [
            'defId' => 'target',
            'defType' => 'Attribute',
            'concrete' => 'AttributeSingleValue',
            'name' => 'target',
            'valTester' => 'targetTester',
            'global' => false,
            'caseSensitive' => false
        ];

        $def = $this->definitionFactory->makeAttributeDefinition($attributeDef);
        self::assertInstanceOf(Definition::class, $def);
    }

    /**
     * testMakeAttributeDefinitionFailsIfConcreteIsnotAnAttribute
     * @throws \pvc\html\err\DTOMissingPropertyException
     * @covers \pvc\html\factory\definitions\implementations\league\LeagueDefinitionFactory::makeAttributeDefinition
     */
    public function testMakeAttributeDefinitionFailsIfConcreteIsnotAnAttribute(): void
    {
        /** @var AttributeDef $attributeDef */
        $attributeDef = [
            'defId' => 'target',
            'defType' => 'Attribute',
            'concrete' => 'Foo',
            'name' => 'target',
            'valTester' => 'targetTester',
            'global' => false,
            'caseSensitive' => false
        ];
        self::expectException(DTOInvalidPropertyValueException::class);
        $this->definitionFactory->makeAttributeDefinition($attributeDef);
    }

    /**
     * testMakeAttributeTesterDefinition
     * @covers \pvc\html\factory\definitions\implementations\league\LeagueDefinitionFactory::makeAttributeValueTesterDefinition
     * @covers \pvc\html\factory\definitions\implementations\league\LeagueDefinitionFactory::IsValTester
     */
    public function testMakeAttributeTesterDefinition(): void
    {
        /** @var AttributeValueTesterDef $valueTesterDef */
        $valueTesterDef = [
            'defId' => 'urlTester',
            'defType' => 'AttributeValueTester',
            'concrete' => 'pvc\\validator\\val_tester\\filter_var\FilterVarTester',
            'arg' => 'pvc\\filtervar\\FilterVarValidateUrl',
        ];

        $def = $this->definitionFactory->makeAttributeValueTesterDefinition($valueTesterDef);
        self::assertInstanceOf(Definition::class, $def);
    }

    /**
     * testMakeAttributeTesterDefinitionFailsIfNotAValTester
     * @throws DTOInvalidPropertyValueException
     * @covers \pvc\html\factory\definitions\implementations\league\LeagueDefinitionFactory::makeAttributeValueTesterDefinition
     * @covers \pvc\html\factory\definitions\implementations\league\LeagueDefinitionFactory::IsValTester
     */
    public function testMakeAttributeTesterDefinitionFailsIfNotAValTester(): void
    {
        /** @var AttributeValueTesterDef $valueTesterDef */
        $valueTesterDef = [
            'defId' => 'urlTester',
            'defType' => 'AttributeValueTester',
            'concrete' => 'pvc\\validator\\val_tester\\filter_var\FilterVarBooBoo',
            'arg' => 'pvc\\filtervar\\FilterVarValidateUrl',
        ];
        self::expectException(DTOInvalidPropertyValueException::class);
        $this->definitionFactory->makeAttributeValueTesterDefinition($valueTesterDef);

    }

    /**
     * testMakeElementDefinition
     * @covers \pvc\html\factory\definitions\implementations\league\LeagueDefinitionFactory::makeElementDefinition
     * @covers \pvc\html\factory\definitions\implementations\league\LeagueDefinitionFactory::isArrayOfStrings
     */
    public function testMakeElementDefinition(): void
    {
        $elementDef = [
            'defId' => 'ul',
            'defType' => 'Element',
            'name' => 'ul',
            'comment' => 'unordered list',
            'concrete' => 'Tag',
            'allowedAttributeDefIds' => ['attr1', 'attr2'],
            'allowedChildDefIds' => ['tag1', 'tag2'],
        ];

        /** @var ElementDef $def */
        $def = $this->definitionFactory->makeElementDefinition($elementDef);
        self::assertInstanceOf(Definition::class, $def);
    }

    /**
     * testMakeElementDefinitionFailsIfConcreteIsNotAnElement
     * @throws DTOInvalidPropertyValueException
     * @covers \pvc\html\factory\definitions\implementations\league\LeagueDefinitionFactory::makeElementDefinition
     */
    public function testMakeElementDefinitionFailsIfConcreteIsNotAnElement(): void
    {
        /** @var ElementDef $def */
        $elementDef = [
            'defId' => 'ul',
            'defType' => 'Element',
            'name' => 'ul',
            'comment' => 'unordered list',
            'concrete' => 'Foo',
            'allowedAttributeDefIds' => ['attr1', 'attr2'],
            'allowedChildDefIds' => ['tag1', 'tag2'],
        ];
        self::expectException(DTOInvalidPropertyValueException::class);
        $this->definitionFactory->makeElementDefinition($elementDef);
    }

    /**
     * testMakeElementDefinitionFailsIfAllowedAttributeDefIdsAreNotStrings
     * @throws DTOInvalidPropertyValueException
     * @covers \pvc\html\factory\definitions\implementations\league\LeagueDefinitionFactory::makeElementDefinition
     * @covers \pvc\html\factory\definitions\implementations\league\LeagueDefinitionFactory::isArrayOfStrings
     */
    public function testMakeElementDefinitionFailsIfAllowedAttributeDefIdsAreNotStrings(): void
    {
        $elementDef = [
            'defId' => 'ul',
            'defType' => 'Element',
            'name' => 'ul',
            'comment' => 'unordered list',
            'concrete' => 'Tag',
            'allowedAttributeDefIds' => [9, 4],
            'allowedChildDefIds' => ['tag1', 'tag2'],
        ];
        self::expectException(DTOInvalidPropertyValueException::class);
        $this->definitionFactory->makeElementDefinition($elementDef);
    }

    /**
     * testMakeElementDefinitionFailsIfAllowedChildDefIdsAreNotStrings
     * @throws DTOInvalidPropertyValueException
     * @covers \pvc\html\factory\definitions\implementations\league\LeagueDefinitionFactory::makeElementDefinition
     * @covers \pvc\html\factory\definitions\implementations\league\LeagueDefinitionFactory::isArrayOfStrings
     */
    public function testMakeElementDefinitionFailsIfAllowedChildDefIdsAreNotStrings(): void
    {
        $elementDef = [
            'defId' => 'ul',
            'defType' => 'Element',
            'name' => 'ul',
            'comment' => 'unordered list',
            'concrete' => 'Tag',
            'allowedAttributeDefIds' => ['attr1', 'attr2'],
            'allowedChildDefIds' => [true, new \stdClass()],
        ];
        self::expectException(DTOInvalidPropertyValueException::class);
        $this->definitionFactory->makeElementDefinition($elementDef);
    }

    /**
     * testMakeEventDefinition
     * @covers \pvc\html\factory\definitions\implementations\league\LeagueDefinitionFactory::makeEventDefinition
     */
    public function testMakeEventDefinition(): void
    {
        /** @var EventDef $eventDef */
        $eventDef = [
            'defId' => 'onchange',
            'defType' => 'Event',
            'concrete' => 'Event',
        ];

        $def = $this->definitionFactory->makeEventDefinition($eventDef);
        self::assertInstanceOf(Definition::class, $def);
    }

    /**
     * testMakeEventDefinitionFailsIfEventNameIsnotAnEvent
     * @throws DTOInvalidPropertyValueException
     * @covers \pvc\html\factory\definitions\implementations\league\LeagueDefinitionFactory::makeEventDefinition
     */
    public function testMakeEventDefinitionFailsIfEventNameIsnotAnEvent(): void
    {
        /** @var EventDef $eventDef */
        $eventDef = [
            'defId' => 'onchange',
            'defType' => 'Event',
            'concrete' => '\stdClass',
        ];
        self::expectException(DTOInvalidPropertyValueException::class);
        $this->definitionFactory->makeEventDefinition($eventDef);
    }

    /**
     * testMakeOtherDefinition
     * @covers \pvc\html\factory\definitions\implementations\league\LeagueDefinitionFactory::makeOtherDefinition
     */
    public function testMakeOtherDefinition(): void
    {
        /** @var OtherDef $otherDef */
        $otherDef = [
            'defId' => 'foo',
            'defType' => 'Other',
            'concrete' => 'pvc\\msg\\Msg',
            'arg' => 'bar',
            'shared' => false,
        ];

        $def = $this->definitionFactory->makeOtherDefinition($otherDef);
        self::assertInstanceOf(Definition::class, $def);
    }

    /**
     * testMakeOtherDefinitionFailsIfConcreteIsNotAClassString
     * @throws DTOInvalidPropertyValueException
     * @covers \pvc\html\factory\definitions\implementations\league\LeagueDefinitionFactory::makeOtherDefinition
     *
     */
    public function testMakeOtherDefinitionFailsIfConcreteIsNotAClassString(): void
    {
        /** @var OtherDef $otherDef */
        $otherDef = [
            'defId' => 'foo',
            'defType' => 'Other',
            'concrete' => 'foobar',
            'arg' => 'bar',
            'shared' => false,
        ];
        self::expectException(DTOInvalidPropertyValueException::class);
        $def = $this->definitionFactory->makeOtherDefinition($otherDef);
    }
}
