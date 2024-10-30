<?php

/**
 * @author: Doug Wilbourne (dougwilbourne@gmail.com)
 */

declare(strict_types=1);

namespace pvc\html\factory;

use Psr\Container\ContainerInterface;
use pvc\html\err\InvalidEventNameException;
use pvc\html\err\InvalidTagNameException;
use pvc\html\err\InvalidAttributeIdNameException;
use pvc\interfaces\html\attribute\AttributeInterface;
use pvc\interfaces\html\attribute\EventInterface;
use pvc\interfaces\html\factory\HtmlContainerFactoryInterface;
use pvc\interfaces\html\factory\HtmlFactoryInterface;
use pvc\interfaces\html\tag\TagVoidInterface;
use pvc\interfaces\validator\ValTesterInterface;

/**
 * Class HtmlFactory
 */
class HtmlFactory implements HtmlFactoryInterface
{
    protected ContainerInterface $elementContainer;
    protected ContainerInterface $attributeContainer;
    protected ContainerInterface $eventContainer;

    /**
     * @var array<string>
     */
    protected array $overlappingAttributeTagNames =
        [
            'id'
        ];

    protected string $definitionsDir = __DIR__ . '/definitions/';

    public function __construct(HtmlContainerFactoryInterface $containerFactory)
    {
        $this->elementContainer = $containerFactory->makeElementContainer();
        $this->attributeContainer = $containerFactory->makeAttributeContainer();
        $this->eventContainer = $containerFactory->makeEventContainer();
    }

    /**
     * isAmbiguousName
     * @param string $name
     * @return bool
     */
    public function isAmbiguousName(string $name): bool
    {
        return in_array($name, $this->overlappingAttributeTagNames);
    }

    /**
     * canMakeElement
     * @param string $elementName
     * @return bool
     */
    public function canMakeElement(string $elementName): bool
    {
        return $this->elementContainer->has($elementName);
    }

    /**
     * canMakeAttribute
     * @param string $attributeId
     * @return bool
     */
    public function canMakeAttribute(string $attributeId): bool
    {
        return $this->attributeContainer->has($attributeId);
    }

    /**
     * canMakeEvent
     * @param string $eventId
     * @return bool
     */
    public function canMakeEvent(string $eventId): bool
    {
        return $this->eventContainer->has($eventId);
    }

    /**
     * makeElement
     * @param string $elementName
     * @return TagVoidInterface
     * @throws InvalidTagNameException
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function makeElement(string $elementName): TagVoidInterface
    {
        if (!$this->canMakeElement($elementName)) {
            throw new InvalidTagNameException($elementName);
        }
        /** @var TagVoidInterface $element */
        $element = $this->elementContainer->get($elementName);
        $element->setHtmlFactory($this);
        return $element;
    }

    /**
     * makeAttribute
     * @param string $attributeId
     * @return AttributeInterface
     * @throws InvalidAttributeIdNameException
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function makeAttribute(string $attributeId): AttributeInterface
    {
        if (!$this->canMakeAttribute($attributeId)) {
            throw new InvalidAttributeIdNameException($attributeId);
        }
        /** @var AttributeInterface $attribute */
        $attribute = $this->attributeContainer->get($attributeId);
        return $attribute;
    }

    /**
     * makeAttributeValueTester
     * @param string $testerName
     * @return ValTesterInterface<string>
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function makeAttributeValueTester(string $testerName): ValTesterInterface
    {
        /** @var ValTesterInterface<string> $valueTester */
        $valueTester = $this->attributeContainer->get($testerName);
        return $valueTester;
    }

    /**
     * makeEvent
     * @param string $eventId
     * @return EventInterface
     * @throws InvalidEventNameException
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function makeEvent(string $eventId): EventInterface
    {
        if (!$this->canMakeEvent($eventId)) {
            throw new InvalidEventNameException($eventId);
        }
        /** @var EventInterface $event */
        $event = $this->eventContainer->get($eventId);
        return $event;
    }
}
