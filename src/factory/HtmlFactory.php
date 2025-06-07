<?php

namespace pvc\html\factory;

use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;
use pvc\html\attribute\AttributeCustomData;
use pvc\html\element\Element;
use pvc\html\element\ElementVoid;
use pvc\html\err\InvalidAttributeException;
use pvc\html\err\InvalidEventNameException;
use pvc\html\err\InvalidTagNameException;
use pvc\htmlbuilder\definitions\types\AttributeValueDataType;
use pvc\htmlbuilder\definitions\types\DefinitionType;
use pvc\interfaces\html\attribute\AttributeCustomDataInterface;
use pvc\interfaces\html\attribute\AttributeInterface;
use pvc\interfaces\html\attribute\EventInterface;
use pvc\interfaces\html\element\ElementInterface;
use pvc\interfaces\html\element\ElementVoidInterface;
use pvc\interfaces\validator\ValTesterInterface;

class HtmlFactory
{

    public function __construct(protected ContainerInterface $container) {}

    /**
     * makeElement
     * @param string $elementName
     * @return ElementVoidInterface
     * @throws InvalidTagNameException
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function makeElement(string $elementName): ElementVoidInterface|ElementInterface
    {
        $defId = DefIdResolver::getDefIdFromName($elementName, DefinitionType::Element);
        if (!$this->container->has($defId)) {
            throw new InvalidTagNameException($elementName);
        }
        /** @var ElementVoid|Element $element */
        $element = $this->container->get($defId);
        return $element;
    }

    /**
     * @param  string  $attributeName
     *
     * @return AttributeInterface
     * @throws ContainerExceptionInterface
     * @throws InvalidAttributeException
     * @throws NotFoundExceptionInterface
     */
    public function makeAttribute(string $attributeName): AttributeInterface
    {
        $defId = DefIdResolver::getDefIdFromName($attributeName, DefinitionType::Attribute);
        if (!$this->container->has($defId)) {
            throw new InvalidAttributeException($attributeName);
        }
        /** @var AttributeInterface $element */
        $attribute = $this->container->get($defId);
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
        $defId = DefIdResolver::getDefIdFromName($eventName, DefinitionType::Event);
        if (!$this->container->has($defId)) {
            throw new InvalidEventNameException($eventName);
        }
        /** @var EventInterface $event */
        $event = $this->container->get($defId);
        return $event;
    }

    /**
     * @param  string  $name (should already be prefixed with 'data-')
     * @param  bool  $caseSensitive
     * @param  ValTesterInterface<string|int|bool>|null  $valTester
     * @return AttributeCustomData
     */
    public function makeCustomData(
        string $name,
        ?AttributeValueDataType $dataType = null,
        ?bool $caseSensitive = null,
        ?ValTesterInterface $valTester = null,
    ): AttributeCustomDataInterface {

        return new AttributeCustomData(
            $name,
            $dataType,
            $caseSensitive,
            $valTester,
        );
    }
}