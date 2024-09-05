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
