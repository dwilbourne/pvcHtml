<?php

/**
 * @author: Doug Wilbourne (dougwilbourne@gmail.com)
 */

declare(strict_types=1);

namespace pvc\html\tag;

use pvc\html\attribute\Event;
use pvc\html\err\AttributeNotAllowedException;
use pvc\html\err\UnsetAttributeNameException;
use pvc\html\err\UnsetTagNameException;
use pvc\html\err\InvalidAttributeIdNameException;
use pvc\interfaces\html\attribute\AttributeInterface;
use pvc\interfaces\html\attribute\EventInterface;
use pvc\interfaces\html\factory\HtmlFactoryInterface;
use pvc\interfaces\html\tag\TagVoidInterface;

/**
 * class TagVoid
 *
 * Base class for all html elements in the pvc framework.  This class produces and
 * handles html5 compliant tags / code.
 *
 * This class is for "voidTags" or "empty" tags, which are tags that do not have closing tags (e.g. "<br>" or "<col>")
 */
class TagVoid implements TagVoidInterface
{
    /**
     * @var HtmlFactoryInterface
     *
     * ordinarily, I would set this in the constructor.  But doing so in this case creates a circular
     * dependency in the ContainerFactory class.  The HtmlFactory has a dependency on the ContainerFactory
     * and the circular dependency is created if the ContainerFactory needs to resolve HtmlFactory as part of the
     * constructor of any given tag.
     */
    protected HtmlFactoryInterface $factory;

    public const ATTRIBUTES = 1;

    public const EVENTS = 2;

    /**
     * @var string
     */
    protected string $name;

    /**
     * @var array<string>
     */
    protected array $allowedAttributeIds = [];

    /**
     * @var array<string, AttributeInterface>
     */
    protected array $attributes = [];

    /**
     * getFactory
     * @return HtmlFactoryInterface
     */
    public function getHtmlFactory(): HtmlFactoryInterface
    {
        return $this->factory;
    }

    /**
     * setFactory
     * @param HtmlFactoryInterface $htmlFactory
     */
    public function setHtmlFactory(HtmlFactoryInterface $htmlFactory): void
    {
        $this->factory = $htmlFactory;
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
     * changing the tag id implies having to change the list of attributes the tag supports so attributes are
     * reinitialized!
     * @param string $name
     */
    public function setName(string $name): void
    {
        $this->name = $name;
        $this->attributes = [];
    }

    /**
     * getAllowedAttributeIds
     * @return array<string>
     */
    public function getAllowedAttributeIds(): array
    {
        return $this->allowedAttributeIds;
    }

    /**
     * setAllowedAttributeIds
     * @param array<string> $allowedAttributeIds
     */
    public function setAllowedAttributeIds(array $allowedAttributeIds): void
    {
        $this->allowedAttributeIds = $allowedAttributeIds;
    }

    protected function isAllowedAttribute(AttributeInterface $attribute): bool
    {
        /**
         * as far as I know it is not "illegal" to put any event into any tag, although there are some form-based
         * events that are typically used in forms.  See https://www.w3schools.com/tags/ref_eventattributes.asp
         * for a place to start
         */
        if ($attribute instanceof EventInterface) {
            return true;
        }
        return ($attribute->isGlobal() || in_array($attribute->getId(), $this->getAllowedAttributeIds()));
    }

    /**
     * setAttributeObject
     * @param AttributeInterface $attribute
     * @throws UnsetAttributeNameException
     * @return TagVoid
     */
    public function setAttributeObject(AttributeInterface $attribute): TagVoid
    {
        if (!$this->isAllowedAttribute($attribute)) {
            throw new AttributeNotAllowedException($attribute->getId(), $this->getName());
        }
        $this->attributes[$attribute->getId()] = $attribute;
        return $this;
    }

    public function makeOrGetAttribute(string $attributeId) : AttributeInterface
    {
        /**
         * make the attribute/event if it does not already exist
         */
        if (!$attribute = $this->getAttribute($attributeId)) {
            if ($this->factory->canMakeAttribute($attributeId)) {
                $attribute = $this->factory->makeAttribute($attributeId);
            } elseif ($this->factory->canMakeEvent($attributeId)) {
                $attribute = $this->factory->makeEvent($attributeId);
            } else {
                throw new InvalidAttributeIdNameException($attributeId);
            }
        }
        return $attribute;
    }


    /**
     * setAttribute
     * @param string $attributeId
     * @param string ...$values
     * @return TagVoid
     * @throws InvalidAttributeIdNameException
     * @throws UnsetAttributeNameException
     */
    public function setAttribute(string $attributeId, ...$values): TagVoid
    {
        $attribute = $this->makeOrGetAttribute($attributeId);
        $attribute->setValue(...$values);

        /**
         * add the attribute to the tag
         */
        return $this->setAttributeObject($attribute);
    }

    /**
     * __call
     * @param string $name
     * @param array<string> $arguments
     * @return mixed
     * @throws InvalidAttributeIdNameException
     * @throws UnsetAttributeNameException
     */
    public function __call(string $name, array $arguments): mixed
    {
        /**
         * unpack the array into an argument list for setAttribute
         */
        return $this->setAttribute($name, ...$arguments);
    }

    /**
     * getAttribute
     * @param string $attributeId
     * @return AttributeInterface|null
     */
    public function getAttribute(string $attributeId): AttributeInterface|null
    {
        return $this->attributes[$attributeId] ?? null;
    }

    /**
     * getAttributes
     * @param int $attributeTypes
     * @return array<AttributeInterface>
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
        $z = '<' . $this->getName();
        $callback = function (AttributeInterface $attribute): string {
            return $attribute->render();
        };
        $attributes = implode(' ', array_map($callback, $this->getAttributes()));
        $z .= (strlen($attributes) > 0) ? ' ' . $attributes : '';
        $z .= '>';
        return $z;
    }
}
