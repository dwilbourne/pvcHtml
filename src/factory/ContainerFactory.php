<?php

/**
 * @author: Doug Wilbourne (dougwilbourne@gmail.com)
 */
declare(strict_types=1);

namespace pvc\html\factory;

use League\Container\Argument\LiteralArgument;
use League\Container\Container;
use League\Container\Definition\Definition;
use League\Container\Definition\DefinitionInterface;
use League\Container\ReflectionContainer;
use Psr\Container\ContainerInterface;
use pvc\html\attribute\Event;
use pvc\html\err\DefinitionsFileException;
use pvc\html\val_tester\EventScriptTester;
use pvc\html\val_tester\MediaTypeTester;
use pvc\http\mime\MimeTypeFactory;
use pvc\http\mime\MimeTypes;
use pvc\http\mime\MimeTypesSrc;
use pvc\interfaces\html\factory\HtmlContainerFactoryInterface;
use pvc\msg\Msg;
use pvc\parser\date_time\ParserJavascriptDateTime;
use Throwable;

/**
 * Class LeagueContainerFactory
 *
 * @phpcs:ignore
 * @phpstan-type ElementDef array{'Name':string,'TagType':string,'AllowedAttributes':array<string>,'AllowedSubTags':array<string>}
 *
 * @phpcs:ignore
 * @phpstan-type AttributeDef array{'Id':string,'Name':string,'AttributeType':string,'ValueTester':string,'Global':bool,'CaseSensitive':bool}
 *
 * @phpstan-type AttributeValueTesterDef array{'Name':string,'TesterType':string,'TesterArg':string|array<string>}
 *
 * containers need to be separate because there are 9 attributes and elements that have the same id,
 * so the definitions would step on each other
 */

class ContainerFactory implements HtmlContainerFactoryInterface
{
    /**
     * @var string
     */
    protected string $definitionsDir;

    /**
     * @var array<string>
     */
    protected array $elementNames = [];

    /**
     * @var array<string>
     */
    protected array $attributeNames = [];

    /**
     * @var array<string>
     */
    protected array $attributeValueTesterNames = [];

    /**
     * @var array<string>
     */
    protected array $eventNames = [];

    public function __construct(string $definitionsDir = __DIR__ . '/definitions/')
    {
        $this->definitionsDir = $definitionsDir;
    }

    /**
     * getElementNames
     * @return string[]
     */
    public function getElementNames(): array
    {
        return $this->elementNames;
    }

    /**
     * getAttributeNames
     * @return string[]
     */
    public function getAttributeNames(): array
    {
        return $this->attributeNames;
    }

    /**
     * getAttributeValueTesterNames
     * @return string[]
     */
    public function getAttributeValueTesterNames(): array
    {
        return $this->attributeValueTesterNames;
    }

    /**
     * getEventNames
     * @return string[]
     */
    public function getEventNames(): array
    {
        return $this->eventNames;
    }

    /**
     * getDefinitionArray
     * @param string $fileName
     * @return array<mixed>
     * @throws DefinitionsFileException
     */
    protected function getDefinitionArray(string $fileName): array
    {
        $fileName = $this->definitionsDir . $fileName;

        try {
            $jsonString = file_get_contents($fileName);
        } catch (Throwable $e) {
            throw new DefinitionsFileException($fileName, $e);
        }

        assert(is_string($jsonString));
        $elementDefs = json_decode($jsonString, true);
        if (!$elementDefs) {
            throw new DefinitionsFileException($fileName);
        }
        assert(is_array($elementDefs));
        return $elementDefs;
    }

    /**
     * makeElementContainer
     * @return ContainerInterface
     * @throws DefinitionsFileException
     */
    public function makeElementContainer(): ContainerInterface
    {
        $container = new Container();
        /**
         * enable autowiring
         */
        $container->delegate(new ReflectionContainer());

        /** @var array<ElementDef> $elementDefs */
        $elementDefs = $this->getDefinitionArray('ElementDefs.json');

        foreach ($elementDefs as $def) {
            $leagueDef = $this->makeElementDefinition(
                $def['Name'],
                $def['TagType'],
                $def['AllowedAttributes'],
                $def['AllowedSubTags'],
            );
            $this->elementNames[] = $leagueDef->getAlias();
            $container->add($leagueDef->getAlias(), $leagueDef);
        }

        return $container;
    }

    /**
     * makeElementDefinition
     * @param string $elementName
     * @param string $tagType
     * @param array<string> $allowedAttributes
     * @param array<string> $allowedSubTags
     * @return DefinitionInterface
     */
    public function makeElementDefinition(
        string $elementName,
        string $tagType,
        array $allowedAttributes,
        array $allowedSubTags
    ): DefinitionInterface {
        $def = (new Definition($elementName, 'pvc\\html\\tag\\' . $tagType))
                        ->addMethodCall('setName', [new LiteralArgument($elementName)])
                        ->addMethodCall('setAllowedAttributeIds', [$allowedAttributes]);

        /**
         * subtags are only applicable to the Tag class (not TagVoid)
         */
        if ($tagType == 'Tag') {
            $def->addMethodCall('setAllowedSubTags', [$allowedSubTags]);
        }
        return $def;
    }

    /**
     * makeAttributeContainer
     * @return ContainerInterface
     * @throws DefinitionsFileException
     */
    public function makeAttributeContainer(): ContainerInterface
    {
        $container = new Container();
        /**
         * enable autowiring
         */
        $container->delegate(new ReflectionContainer());

        /** @var array<AttributeDef> $attributeDefs */
        $attributeDefs = $this->getDefinitionArray('AttributeDefs.json');

        foreach ($attributeDefs as $def) {
            $leagueDef = $this->makeAttributeDefinition(
                $def['Id'],
                $def['Name'],
                $def['AttributeType'],
                $def['ValueTester'],
                $def['Global'],
                $def['CaseSensitive'],
            );
            $this->attributeNames[] = $leagueDef->getAlias();
            $container->add($leagueDef->getAlias(), $leagueDef);
        }

        /**
         * the following definitions are needed to construct a couple of the ValueTesters.  Value Testers
         * and their supporting objects are stateless and should be shared, e.g. the same instance is returned
         * each time the definition is resolved.
         */
        $container->addShared(ParserJavascriptDateTime::class)->addArgument(Msg::class);
        $container->addShared(MimeTypesSrc::class)->addArgument(MimeTypeFactory::class);
        $container->addShared(MimeTypes::class)->addArgument(MimeTypesSrc::class);
        $container->addShared(MediaTypeTester::class)->addArgument(MimeTypes::class);

        /** @var array<AttributeValueTesterDef> $attributeValueTesterDefs */
        $attributeValueTesterDefs = $this->getDefinitionArray('AttributeValueTesterDefs.json');

        foreach ($attributeValueTesterDefs as $def) {
            $leagueDef = $this->makeAttributeValueTesterDefinition(
                $def['Name'],
                $def['TesterType'],
                $def['TesterArg'],
            );
            $this->attributeValueTesterNames[] = $leagueDef->getAlias();
            $container->addShared($leagueDef->getAlias(), $leagueDef);
        }
        return $container;
    }

    /**
     * makeAttributeDefinition
     * @param string $attributeId
     * @param string $attributeName
     * @param string $attributeType
     * @param string|null $valTesterName
     * @param bool $global
     * @return DefinitionInterface
     *
     * unlike other definitions, the id of the attribute and the id of the attribute can be different.  The
     * type attribute can be used inside an input element or a button element, and the values of the type
     * attribute are different between the two.  So we creat a type attribute with an id of type_input
     * and a type attribute with an id of type_button.
     */
    public function makeAttributeDefinition(
        string $attributeId,
        string $attributeName,
        string $attributeType,
        string $valTesterName = null,
        bool $global = false,
        bool $caseSensitive = false,
    ): DefinitionInterface {
        $def = (new Definition($attributeId, 'pvc\\html\\attribute\\' . $attributeType))
            ->addMethodCall('setId', [new LiteralArgument($attributeId)])
            ->addMethodCall('setName', [new LiteralArgument($attributeName)])
            ->addMethodCall('setGlobal', [$global]);

        if ($valTesterName) {
            $def->addMethodCall('setTester', [$valTesterName])
                ->addMethodCall('setCaseSensitive', [$caseSensitive]);
        }
        return $def;
    }

    /**
     * makeAttributeValueTesterDefinition
     * @param string $testerName
     * @param string $testerType
     * @param string|array<string> $testerArg
     * @return DefinitionInterface
     */
    public function makeAttributeValueTesterDefinition(
        string $testerName,
        string $testerType,
        string|array $testerArg
    ): DefinitionInterface {
        return (new Definition($testerName, $testerType))->addArgument($testerArg);
    }

    /**
     * makeEventContainer
     * @return ContainerInterface
     * @throws DefinitionsFileException
     */
    public function makeEventContainer(): ContainerInterface
    {
        $container = new Container();
        /**
         * enable autowiring
         */
        $container->delegate(new ReflectionContainer());

        $eventDefs = $this->getDefinitionArray('EventDefs.json');

        /** @var string $def */
        foreach ($eventDefs as $def) {
            $leagueDef = $this->makeEventDefinition($def);
            $this->eventNames[] = $leagueDef->getAlias();
            $container->add($leagueDef->getAlias(), $leagueDef);
        }
        return $container;
    }

    public function makeEventDefinition(string $eventName): DefinitionInterface
    {
        return (new Definition($eventName, Event::class))
            ->addArgument(EventScriptTester::class)
            ->addMethodCall('setId', [new LiteralArgument($eventName)])
            ->addMethodCall('setName', [new LiteralArgument($eventName)]);
    }
}