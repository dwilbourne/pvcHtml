<?php

/**
 * @author: Doug Wilbourne (dougwilbourne@gmail.com)
 */

declare(strict_types=1);

namespace pvc\html\tag\abstract;

use pvc\html\attribute\factory\AttributeFactory;
use pvc\html\err\InvalidAttributeException;
use pvc\html\err\InvalidAttributeNameException;
use pvc\html\err\InvalidCustomDataNameException;
use pvc\html\err\InvalidEventScriptException;
use pvc\html\err\MissingTagAttributesException;
use pvc\interfaces\html\attribute\AttributeInterface;
use pvc\interfaces\html\attribute\EventInterface;
use pvc\interfaces\html\tag\TagVoidInterface;
use pvc\interfaces\validator\ValTesterInterface;

/**
 * class TagVoid.  Base class for all html output in the pvc framework.  This class produces and
 * handles html5 compliant tags / code.
 *
 * This class is for "voids" or "empty" tags, which are tags that do not have closing tags (e.g. "<br>" or "<col>")
 */
class TagVoid implements TagVoidInterface
{
    /**
     * @var string
     */
    protected string $name;

    /**
     * @var array<string, AttributeInterface|EventInterface>
     */
    protected array $attributes = [];

    protected AttributeFactory $attributeFactory;

    public function __construct(AttributeFactory $attributeFactory)
    {
        $this->attributeFactory = $attributeFactory;
    }

    /**
     * getName
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * setName
     * changing the tag name implies having to change the list of attributes the tag supports so attributes are
     * reinitialized!
     * @param string $name
     */
    public function setName(string $name): void
    {
        $this->name = $name;
        $this->attributes = [];
    }

    /**
     * setAttribute
     * @param string $name
     * @param mixed $value
     * @throws InvalidAttributeException
     * @throws InvalidAttributeNameException
     */
    public function setAttribute(string $name, mixed $value): void
    {
        $attribute = $this->attributes[$name] ?? $this->attributeFactory->makeAttribute($name);
        $attribute->setValue($value);
        $this->attributes[$name] = $attribute;
    }

    /**
     * setCustomDataAttribute
     * @param string $name
     * @param string $value
     * @param ValTesterInterface<string> $tester
     * @throws InvalidAttributeNameException
     * @throws InvalidCustomDataNameException
     * @throws \pvc\html\err\InvalidAttributeValueException
     *
     * It was tempting to eliminate this method and allow setAttribute to make custom data attributes as well.  But
     * because the $name argument to setAttribute is a string, the logic would be to create a custom attribute
     * if there is no such standard attribute.  Thus, there would be no way to catch a typo in the call to
     * setAttribute
     */
    public function setCustomDataAttribute(string $name, string $value, ValTesterInterface $tester): void
    {
        $attribute = $this->attributes[$name] ?? $this->attributeFactory->makeCustomDataAttribute($name, $tester);
        $attribute->setValue($value);
        $this->attributes[$name] = $attribute;
    }

    public function setEvent(string $eventName, string $script): void
    {
        $event = $this->attributes[$eventName] ?? $this->attributeFactory->makeEvent($eventName);
        $event->setValue($script);
        $this->attributes[$eventName] = $event;
    }

    /**
     * getAttributes
     * @return array<string, AttributeInterface>
     */
    public function getAttributes(): array
    {
        $callback = function ($item) {
            return ($item instanceof AttributeInterface);
        };
        /** @var array<AttributeInterface> */
        return array_filter($this->attributes, $callback);
    }

    /**
     * getEvents
     * @return array<string, EventInterface>
     */
    public function getEvents(): array
    {
        $callback = function ($item) {
            return ($item instanceof EventInterface);
        };
        /** @var array<EventInterface> */
        return array_filter($this->attributes, $callback);
    }

    /**
     * getAttribute
     * @param string $attributeName
     * @return AttributeInterface|null
     */
    public function getAttribute(string $attributeName): AttributeInterface|null
    {
        $attributes = $this->getAttributes();
        return $attributes[$attributeName] ?? null;
    }

    /**
     * getEvent
     * @param string $eventName
     * @return EventInterface|null
     */
    public function getEvent(string $eventName): EventInterface|null
    {
        $events = $this->getEvents();
        return $events[$eventName] ?? null;
    }

    /**
     * __get
     * @param string $name
     * @return mixed
     */
    public function __get(string $name): mixed
    {
        $attribute = $this->attributes[$name] ?? null;
        return $attribute?->getValue();
    }

    /**
     * __set
     * @param string $name
     * @param string|array<string>|bool $value
     * @throws InvalidAttributeException
     * @throws InvalidAttributeNameException
     */
    public function __set(string $name, string|array|bool $value): void
    {
        $this->setAttribute($name, $value);
    }

    /**
     * generateOpeningTag
     * @return string
     */
    public function generateOpeningTag(): string
    {
        $callback = function (AttributeInterface|EventInterface $attribute_event): string {
            return $attribute_event->render();
        };

        $z = '<' . $this->name;
        $attributes = implode(' ', array_map($callback, $this->getAttributes()));
        $z .= (strlen($attributes) > 0) ? ' ' . $attributes : '';
        $z .= '>';
        return $z;
    }
}
