<?php

/**
 * @author: Doug Wilbourne (dougwilbourne@gmail.com)
 */

declare(strict_types=1);

namespace pvc\html\builder;

use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use pvc\html\attribute\AttributeCustomData;
use pvc\html\builder\definitions\implementations\league\HtmlContainer;
use pvc\html\builder\definitions\implementations\league\HtmlDefinitionFactory;
use pvc\html\err\DTOInvalidPropertyValueException;
use pvc\html\err\DuplicateDefinitionIdException;
use pvc\html\err\InvalidAttributeIdNameException;
use pvc\html\err\InvalidDefinitionsFileException;
use pvc\html\err\InvalidEventNameException;
use pvc\html\err\InvalidTagNameException;
use pvc\interfaces\html\attribute\AttributeCustomDataInterface;
use pvc\interfaces\html\attribute\AttributeInterface;
use pvc\interfaces\html\attribute\EventInterface;
use pvc\interfaces\html\builder\definitions\DefinitionFactoryInterface;
use pvc\interfaces\html\builder\definitions\DefinitionType;
use pvc\interfaces\html\builder\HtmlBuilderInterface;
use pvc\interfaces\html\builder\HtmlContainerInterface;
use pvc\interfaces\html\element\ElementInterface;
use pvc\interfaces\html\element\ElementVoidInterface;
use pvc\interfaces\validator\ValTesterInterface;
use pvc\validator\val_tester\always_true\AlwaysTrueTester;

/**
 * Class HtmlFactory
 *
 * @phpstan-import-type DefArray from DefinitionFactoryInterface
 *
 * @template VendorSpecificDefinition of DefinitionFactoryInterface
 *
 * @implements HtmlBuilderInterface<VendorSpecificDefinition>
 */
class HtmlBuilder implements HtmlBuilderInterface
{
    /**
     * @var HtmlContainerInterface<VendorSpecificDefinition>
     */
    protected HtmlContainerInterface $container;

    /**
     * @var DefinitionFactoryInterface<VendorSpecificDefinition>
     */
    protected DefinitionFactoryInterface $definitionFactory;

    /**
     * @var string
     */
    protected string $definitionsFile = __DIR__ . '/definitions/Definitions.json';

    /**
     * @var array<string, DefinitionType>
     * key is the defId, value is whether it is an attribute, element, etc
     */
    protected array $definitionTypes;

    /**
     * there are several identifiers in html which are duplicates, i.e. out of context, you would not know whether
     * you are referring to an attribute or an element.  For this reason and because we are using a single container,
     * there are some cases where the definition id needs to be different from the name of the object.  The
     * method of disambiguation is to append an _attr or _element to the names of the objects and make those the
     * definition ids.  For example, cite => cite_attr / cite_element.
     *
     * The ambiguous identifiers are:
     *
     * cite
     * data
     * form
     * label
     * span
     * style
     * title
     *
     * @var array<string>
     */
    protected array $ambiguousIdentifiers = [
        'cite',
        'data',
        'form',
        'label',
        'span',
        'style',
        'title',
        'type',
    ];

    /**
     * @param HtmlContainerInterface<VendorSpecificDefinition> $container
     * @param DefinitionFactoryInterface<VendorSpecificDefinition> $definitionFactory
     */
    public function __construct(
        HtmlContainerInterface $container = null,
        DefinitionFactoryInterface $definitionFactory = null,
        string $definitionsFile = null,
    ) {
        $this->setContainer($container ?: new HtmlContainer());
        $this->setDefinitionFactory($definitionFactory ?: new HtmlDefinitionFactory());

        if ($definitionsFile) {
            $this->setDefinitionsFile($definitionsFile);
        }
        $this->hydrateContainer($this->getContainer());
    }

    /**
     * getContainer
     * @return HtmlContainerInterface<VendorSpecificDefinition>
     */
    public function getContainer(): HtmlContainerInterface
    {
        return $this->container;
    }

    /**
     * setContainer
     * @param HtmlContainerInterface<VendorSpecificDefinition> $container
     */
    public function setContainer(HtmlContainerInterface $container): void
    {
        $this->container = $container;
    }

    /**
     * getDefinitionFactory
     * @return DefinitionFactoryInterface<VendorSpecificDefinition>
     */
    public function getDefinitionFactory(): DefinitionFactoryInterface
    {
        return $this->definitionFactory;
    }

    /**
     * setDefinitionFactory
     * @param DefinitionFactoryInterface<VendorSpecificDefinition> $definitionFactory
     */
    public function setDefinitionFactory(DefinitionFactoryInterface $definitionFactory): void
    {
        $this->definitionFactory = $definitionFactory;
    }

    protected function setDefinitionsFile(string $filename): void
    {
        if (!is_readable($filename)) {
            throw new InvalidDefinitionsFileException($filename);
        }
        $this->definitionsFile = $filename;
    }

    /**
     * getDefinitionsFile
     * @return string
     */
    public function getDefinitionsFile(): string
    {
        return $this->definitionsFile;
    }

    /**
     * getDefinitionArray
     * @return array<mixed>
     * @throws InvalidDefinitionsFileException
     */
    protected function getDefinitionArray(): array
    {
        /** @var string $jsonString */
        $jsonString = file_get_contents($this->getDefinitionsFile());
        $defs = json_decode($jsonString, true);

        if (is_null($defs)) {
            throw new InvalidDefinitionsFileException($this->getDefinitionsFile());
        }

        assert(is_array($defs));
        return $defs;
    }

    /**
     * hydrateContainer
     * @param HtmlContainerInterface<VendorSpecificDefinition> $container
     * @throws DTOInvalidPropertyValueException
     * @throws DuplicateDefinitionIdException
     * @throws InvalidDefinitionsFileException
     */
    protected function hydrateContainer(HtmlContainerInterface $container): void
    {
        /** @var array<DefArray> $jsonDefs */
        $jsonDefs = $this->getDefinitionArray();

        foreach ($jsonDefs as $jsonDef) {
            /**
             * this artifact is used to see if a defId is referring to an Attribute, Event, etc
             */
            $defId = $jsonDef['defId'];

            /**
             * $defType is an enum such as Attribute or Element.
             */
            $defType = DefinitionType::tryFrom($jsonDef['defType']);

            $def = $this->makeDefinition($jsonDef);

            if (isset($this->definitionTypes[$defId])) {
                throw new DuplicateDefinitionIdException($defId);
            } else {
                $this->definitionTypes[$defId] = $defType;
            }

            $container->add($defId, $def);
        }
    }

    /**
     * makeDefinition
     * @param DefArray $defArray
     * @return VendorSpecificDefinition
     * @throws DTOInvalidPropertyValueException
     */
    protected function makeDefinition(array $defArray): mixed
    {
        $defType = DefinitionType::tryFrom($defArray['defType']);

        $result = match($defType) {
            DefinitionType::Attribute => $this->definitionFactory->makeAttributeDefinition($defArray),
            DefinitionType::AttributeValueTester => $this->definitionFactory->makeAttributeValueTesterDefinition($defArray),
            DefinitionType::Element => $this->definitionFactory->makeElementDefinition($defArray),
            DefinitionType::Event => $this->definitionFactory->makeEventDefinition($defArray),
            DefinitionType::Other => $this->definitionFactory->makeOtherDefinition($defArray),
            default => throw new DTOInvalidPropertyValueException('defType', $defType->value, 'DTOTrait'),
        };
        return $result;
    }

    /**
     * getDefinitionTypes
     * @param DefinitionType $typeFilter
     * @return array<string, DefinitionType>
     */
    public function getDefinitionTypes(DefinitionType $typeFilter = null): array
    {
        $result = $this->definitionTypes;
        if ($typeFilter) {
            $result = array_filter($result,
                function (DefinitionType $defType) use ($typeFilter) { return ($defType == $typeFilter); });
        }
        return $result;
    }

    /**
     * getDefinitionType
     * @param string $defId
     * @return DefinitionType|null
     */
    public function getDefinitionType(string $defId): ?DefinitionType
    {
        return $this->definitionTypes[$defId] ?? null;
    }

    /**
     * getDefinitionIds
     * @param DefinitionType|null $type
     * @return array<string>
     */
    public function getDefinitionIds(DefinitionType $type = null): array
    {
        return array_keys($this->getDefinitionTypes($type));
    }

    protected function isAmbiguousName(string $name): bool
    {
        return(in_array($name, $this->ambiguousIdentifiers));
    }

    /**
     * makeElement
     * @param string $elementName
     * @return ElementVoidInterface<VendorSpecificDefinition>
     * @throws InvalidTagNameException
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function makeElement(string $elementName): ElementVoidInterface|ElementInterface
    {
        $elementDefId = ($this->isAmbiguousName($elementName) ? $elementName . '_element' : $elementName);

        if (!$this->container->has($elementDefId)) {
            throw new InvalidTagNameException($elementName);
        }
        /** @var ElementVoidInterface<VendorSpecificDefinition> $element */
        $element = $this->getContainer()->get($elementDefId);
        $element->setHtmlBuilder($this);
        return $element;
    }

    /**
     * makeAttribute
     * @param string $attributeName
     * @return AttributeInterface
     * @throws InvalidAttributeIdNameException
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function makeAttribute(string $attributeName): AttributeInterface
    {
        $attributeDefId = ($this->isAmbiguousName($attributeName) ? $attributeName . '_attr' : $attributeName);
        if (!$this->container->has($attributeDefId)) {
            throw new InvalidAttributeIdNameException($attributeName);
        }
        /** @var AttributeInterface $attribute */
        $attribute = $this->getContainer()->get($attributeDefId);
        return $attribute;
    }

    /**
     * makeEvent
     * @param string $eventName
     * @return EventInterface
     * @throws InvalidEventNameException
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function makeEvent(string $eventName): EventInterface
    {
        if (!$this->container->has($eventName)) {
            throw new InvalidEventNameException($eventName);
        }
        /** @var EventInterface $event */
        $event = $this->getContainer()->get($eventName);
        return $event;
    }


    /**
     * makeCustomData
     * @param string $attributeName
     * @param ValTesterInterface<string>|null $valTester
     * @return AttributeCustomDataInterface
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     *
     * although we never like hard dependencies inside a method, this class is a htmlBuilder.  And this method is, by
     * definition, tightly coupled to the AttributeCustomData class.......
     */
    public function makeCustomData(
        string $attributeName,
        string $value,
        ValTesterInterface $valTester = null
    ): AttributeCustomDataInterface {

        $valTester = $valTester ?: new AlwaysTrueTester();

        $attribute = new AttributeCustomData();
        $attribute->setDefId($attributeName);
        $attribute->setName($attributeName);
        $attribute->setTester($valTester);
        $attribute->setValue($value);

        return $attribute;
    }
}
