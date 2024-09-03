<?php

/**
 * @author: Doug Wilbourne (dougwilbourne@gmail.com)
 */

declare(strict_types=1);

namespace pvc\html\abstract\tag;

use pvc\html\abstract\attribute\Event;
use pvc\html\abstract\err\InvalidAttributeNameException;
use pvc\html\abstract\err\UnsetAttributeNameException;
use pvc\html\abstract\err\UnsetTagNameException;
use pvc\interfaces\html\attribute\AttributeFactoryInterface;
use pvc\interfaces\html\attribute\AttributeInterface;
use pvc\interfaces\html\tag\TagVoidInterface;
use pvc\interfaces\validator\ValTesterInterface;

/**
 * class TagVoid.  Base class for all html output in the pvc framework.  This class produces and
 * handles html5 compliant tags / code.
 *
 * This class is for "voidTags" or "empty" tags, which are tags that do not have closing tags (e.g. "<br>" or "<col>")
 *
 * @template ValueType
 * @template ValTesterType
 * @implements TagVoidInterface<ValueType, ValTesterType>
 */
class TagVoid implements TagVoidInterface
{
    public const ATTRIBUTES = 1;

    public const EVENTS = 2;

    /**
     * @var string
     */
    protected string $name;

    /**
     * @var array<string>
     */
    protected array $allowedAttributes = [];

    /**
     * @var array<string, AttributeInterface<ValueType, ValTesterType>>
     */
    protected array $attributes = [];

    /**
     * @var AttributeFactoryInterface<ValueType, ValTesterType>
     */
    protected AttributeFactoryInterface $attributeFactory;


    /**
     * @param AttributeFactoryInterface<ValueType, ValTesterType> $attributeFactory
     */
    public function __construct(AttributeFactoryInterface $attributeFactory)
    {
        $this->setAttributeFactory($attributeFactory);
    }

    /**
     * getAttributeFactory
     * @return AttributeFactoryInterface<ValueType, ValTesterType>
     */
    public function getAttributeFactory(): AttributeFactoryInterface
    {
        return $this->attributeFactory;
    }

    /**
     * setAttributeFactory
     * @param AttributeFactoryInterface<ValueType, ValTesterType> $attributeFactory
     */
    public function setAttributeFactory(AttributeFactoryInterface $attributeFactory): void
    {
        $this->attributeFactory = $attributeFactory;
    }

    /**
     * getName
     * @return string
     */
    public function getName(): string
    {
        return $this->name ?? '';
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
     * getAllowedAttributes
     * @return array<string>
     */
    public function getAllowedAttributes(): array
    {
        return $this->allowedAttributes;
    }

    /**
     * setAllowedAttributes
     * @param array<string> $allowedAttributes
     */
    public function setAllowedAttributes(array $allowedAttributes): void
    {
        foreach($allowedAttributes as $attribute) {
            /**
             * not testing for "validity" per se, but allowed attributes are typed as strings and the
             * keys to the actual attributes array are strings.  So we will just enforce type consistency here.
             */
            if (!is_string($attribute)) {
                throw new InvalidAttributeNameException((string) $attribute);
            }
        }
        $this->allowedAttributes = $allowedAttributes;
    }

    /**
     * setAttribute
     * @param AttributeInterface<ValueType, ValTesterType> $attribute
     * @throws UnsetAttributeNameException
     */
    public function setAttribute(AttributeInterface $attribute): void
    {
        if (empty($name = $attribute->getName())) {
            throw new UnsetAttributeNameException();
        }
        $this->attributes[$name] = $attribute;
    }

    /**
     * setCustomData
     * @param string $name
     * @param ValueType $value
     * @param ValTesterInterface<ValTesterType>|null $tester
     *
     * It was tempting to eliminate this method and allow set to make custom data attributes as well.  But
     * because the $name argument to set is a string, the logic would be to create a custom attribute
     * if there is no such standard attribute.  Thus, there would be no way to catch a typo in the call to
     * set.
     */
    public function setCustomData(string $name, $value, ValTesterInterface $tester = null): void
    {
        /**
         * $dataName is just the name prefixed with 'data-'.  It is actually stored with the 'data-' prefix so
         * that it can differentiate, for example, between 'href' and 'data-href'.
         */
        $dataName = 'data-' . $name;
        $attribute = $this->attributes[$dataName] ?? $this->attributeFactory->makeCustomData($name, $tester);
        $attribute->setValue($value);
        $this->attributes[$dataName] = $attribute;
    }

    /**
     * getAttribute
     * @param string $name
     * @return AttributeInterface<ValueType, ValTesterType>|null
     */
    public function getAttribute(string $name): AttributeInterface|null
    {
        return $this->attributes[$name] ?? null;
    }

    /**
     * getAttributes
     * @param int $attributeTypes
     * @return array<AttributeInterface<ValueType, ValTesterType>>
     */
    public function getAttributes(int $attributeTypes = self::ATTRIBUTES | self::EVENTS): array
    {
        $callback = function ($item) use ($attributeTypes) {
            /**
             * $item is an attribute if it is not an event.......
             */
            $isEvent = ($item instanceof Event) ? self::EVENTS : 0;
            $isAttribute = $isEvent ? 0 : self::ATTRIBUTES;

            if (1 == (self::ATTRIBUTES & $attributeTypes & $isAttribute)) {
                return true;
            }
            if (2 == (self::EVENTS & $attributeTypes & $isEvent)) {
                return true;
            }
            return false;
        };
        return array_filter($this->attributes, $callback);
    }

    /**
     * removeAttribute
     * @param string $name
     * removes an attribute (or event)
     */
    public function removeAttribute(string $name): void
    {
        unset($this->attributes[$name]);
    }

    /**
     * __get
     * @param string $name
     * @return ValueType|null
     *
     * this magic getter returns attribute values and event scripts, not with the objects themselves.
     * If you want to get the attribute/event object, use getAttribute explicitly.
     *
     * In terms of return values, one typically thinks that attribute/event values are all strings (which is true
     * when they are rendered). But AttributeVoid has boolean values and AttributeMultiValue has an
     * array of values.
     */
    public function __get(string $name): mixed
    {
        $attribute = $this->attributes[$name] ?? null;
        return $attribute?->getValue();
    }

    /**
     * __set
     * @param string $name
     * @param ValueType $value
     */
    public function __set(string $name, mixed $value): void
    {
        $attribute = $this->attributes[$name] ?? $this->attributeFactory->makeAttribute($name);
        $attribute->setValue($value);
        $this->attributes[$name] = $attribute;
    }

    /**
     * generateOpeningTag
     * @return string
     */
    public function generateOpeningTag(): string
    {
        if (empty($this->getName())) {
            throw new UnsetTagNameException();
        }
        $z = '<' . $this->name;
        $callback = function (AttributeInterface $attribute): string {
            return $attribute->render();
        };
        $attributes = implode(' ', array_map($callback, $this->getAttributes()));
        $z .= (strlen($attributes) > 0) ? ' ' . $attributes : '';
        $z .= '>';
        return $z;
    }
}
