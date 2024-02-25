<?php

/**
 * @author: Doug Wilbourne (dougwilbourne@gmail.com)
 */

declare(strict_types=1);

namespace pvc\html\tag\abstract;

use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;
use pvc\html\attribute\factory\CustomDataAttributeFactory;
use pvc\html\attribute\factory\EventFactory;
use pvc\html\err\InvalidAttributeException;
use pvc\html\err\InvalidAttributeNameException;
use pvc\html\err\InvalidAttributeValueException;
use pvc\html\err\InvalidCustomDataNameException;
use pvc\html\err\InvalidEventNameException;
use pvc\html\err\InvalidEventScriptException;
use pvc\html\err\MissingTagAttributesException;
use pvc\interfaces\html\attribute\AttributeInterface;
use pvc\interfaces\html\attribute\EventInterface;
use pvc\interfaces\html\tag\TagVoidInterface;
use pvc\interfaces\validator\ValTesterInterface;
use ReflectionException;

/**
 * class TagVoid.  Base class for all html output in the pvc framework.  This class produces and
 * handles html5 compliant tags / code.
 *
 * This class is for "voids" or "empty" tags, which are tags that do not have closing tags (e.g. "<br>" or "<col>")
 */
class TagVoid implements TagVoidInterface
{
    /**
     * @var ContainerInterface
     */
    protected ContainerInterface $attributeContainer;

    /**
     * @var array<AttributeInterface<string|array<string>|bool>>
     */
    protected array $attributes = [];

    /**
     * @var string
     */
    protected string $tagName;

    /**
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->attributeContainer = $container;
    }

    /**
     * setCustomAttribute
     * @param string $name
     * @param string $value
     * @param ValTesterInterface<string>|null $tester
     * @throws ContainerExceptionInterface
     * @throws InvalidAttributeValueException
     * @throws NotFoundExceptionInterface
     * @throws InvalidCustomDataNameException
     */
    public function setCustomAttribute(string $name, string $value, ValTesterInterface $tester = null): void
    {
        /** @var CustomDataAttributeFactory $customDataAttributeFactory */
        $customDataAttributeFactory = $this->attributeContainer->get(CustomDataAttributeFactory::class);

        /** @var AttributeInterface<string|array<string>|bool> $attribute */
        $attribute = $this->attributes[$name] ?? $customDataAttributeFactory->makeCustomData($name);

        if ($tester) {
            $attribute->setTester($tester);
        }

        $attribute->setValue($value);
        /**
         * note that the index into the array is the name without the 'data-' prefix.
         */
        $this->attributes[$name] = $attribute;
    }

    /**
     * getAttributes
     * @return array<string, string|array<string>|bool|null>
     */
    public function getAttributes(): array
    {
        $result = [];
        foreach ($this->attributes as $attrName => $attribute) {
            $result[$attrName] = $this->getAttributeValue($attrName);
        }
        return $result;
    }

    /**
     * setAttributes
     * @param array<string, string> $attributes
     * @throws ContainerExceptionInterface
     * @throws InvalidAttributeException
     * @throws InvalidEventScriptException
     * @throws MissingTagAttributesException
     * @throws NotFoundExceptionInterface
     * @throws ReflectionException
     * @throws InvalidAttributeNameException
     */
    public function setAttributes(array $attributes): void
    {
        foreach ($attributes as $name => $value) {
            $this->setAttribute($name, $value);
        }
    }

    /**
     * getAttributeValue
     * @param string $attributeName
     * @return string|array<string>|bool|null
     */
    public function getAttributeValue(string $attributeName): string|array|bool|null
    {
        $attribute = $this->attributes[$attributeName] ?? null;
        $value = $attribute?->getValue();
        return $value;
    }

    /**
     * setAttribute
     *
     * because events are a kind of attribute, you can use this method to set events as well.  This sets the stage
     * for the usage of the magic setter __set for both attributes and events
     *
     * @param string $name
     * @param string|array<string>|bool $value
     * @throws ContainerExceptionInterface
     * @throws InvalidAttributeException
     * @throws InvalidAttributeNameException
     * @throws InvalidEventScriptException
     * @throws MissingTagAttributesException
     * @throws NotFoundExceptionInterface
     */
    public function setAttribute(string $name, string|array|bool $value): void
    {
        if (TagAttributes::isValidAttribute($this->getTagName(), $name)) {
            $attribute = ($this->attributes[$name] ?? $this->attributeContainer->get($name));
            assert($attribute instanceof AttributeInterface);
            $attribute->setValue($value);
            $this->attributes[$name] = $attribute;
            return;
        }

        if (TagAttributes::IsValidEvent($name)) {
            assert(is_string($value));
            $this->setEvent($name, $value);
            return;
        }

        throw new InvalidAttributeException($name);
    }

    /**
     * getTagName
     * @return string
     */
    public function getTagName(): string
    {
        return $this->tagName;
    }

    /**
     * setTagName
     * changing the tag name implies having to change the list of attributes the tag supports so attributes are
     * reinitialized!
     * @param string $tagName
     */
    public function setTagName(string $tagName): void
    {
        $this->tagName = $tagName;
        $this->attributes = [];
    }

    /**
     * setEvent
     * @param string $eventName
     * @param string $script
     * @throws ContainerExceptionInterface
     * @throws InvalidAttributeNameException
     * @throws InvalidEventScriptException
     * @throws NotFoundExceptionInterface
     * @throws InvalidEventNameException
     */
    public function setEvent(string $eventName, string $script): void
    {
        $event = $this->attributes[$eventName] ?? null;

        if ($event) {
            if (!$event instanceof EventInterface) {
                throw new InvalidEventNameException();
            }
            $event->setValue($script);
        } else {
            /** @var EventFactory $eventFactory */
            $eventFactory = $this->attributeContainer->get(EventFactory::class);
            $event = $eventFactory->makeEvent($eventName, $script);
        }
        /** @var AttributeInterface<string|array<string>|bool> $attribute */
        $attribute = $event;
        $this->attributes[$event->getName()] = $attribute;
    }

    /**
     * getEvents
     * @return array<string, string>
     */
    public function getEvents(): array
    {
        $callback = function (AttributeInterface $attribute) {
            return $attribute instanceof EventInterface;
        };
        $events = array_filter($this->attributes, $callback);
        $result = [];
        foreach ($events as $eventName => $event) {
            assert($event instanceof EventInterface);
            $script = $this->getEventScript($eventName);
            assert(is_string($script));
            $result[$eventName] = $script;
        }
        return $result;
    }

    /**
     * getEventScript
     * @param string $eventName
     * @return string|null
     */
    public function getEventScript(string $eventName): ?string
    {
        $event = $this->attributes[$eventName] ?? null;
        if ($event) {
            assert($event instanceof EventInterface);
            return $event->getValue();
        }
        return null;
    }

    /**
     * __get
     * @param string $attributeName
     * @return string|array<string, string>|bool|null
     */
    public function __get(string $attributeName): string|array|bool|null
    {
        return $this->getAttributeValue($attributeName);
    }

    /**
     * __set
     * @param string $name
     * @param string|array<string>|bool $value
     * @throws ContainerExceptionInterface
     * @throws InvalidAttributeException
     * @throws InvalidEventScriptException
     * @throws MissingTagAttributesException
     * @throws NotFoundExceptionInterface
     * @throws ReflectionException
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

        $z = '<' . $this->tagName;
        $attributes = implode(' ', array_map($callback, $this->attributes));
        $z .= (strlen($attributes) > 0) ? ' ' . $attributes : '';
        $z .= '>';
        return $z;
    }
}
