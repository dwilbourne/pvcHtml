<?php

/**
 * @author: Doug Wilbourne (dougwilbourne@gmail.com)
 */

declare(strict_types=1);

namespace pvc\html\tag;

use pvc\html\attribute\Event;
use pvc\html\err\AttributeNotAllowedException;
use pvc\html\err\InvalidDefinitionIdException;
use pvc\html\err\InvalidAttributeIdNameException;
use pvc\interfaces\html\attribute\AttributeCustomDataInterface;
use pvc\interfaces\html\attribute\AttributeInterface;
use pvc\interfaces\html\attribute\EventInterface;
use pvc\interfaces\html\factory\definitions\AbstractDefinitionFactoryInterface;
use pvc\interfaces\html\factory\definitions\DefinitionType;
use pvc\interfaces\html\factory\HtmlFactoryInterface;
use pvc\interfaces\html\tag\TagVoidInterface;
use pvc\interfaces\validator\ValTesterInterface;

/**
 * class TagVoid
 *
 * Base class for all html elements in the pvc framework.  This class produces and
 * handles html5 compliant tags / code.
 *
 * This class is for "voidTags" or "empty" tags, which are tags that do not have closing tags (e.g. "<br>" or "<col>")
 *
 * @template Definition of AbstractDefinitionFactoryInterface
 * @implements TagVoidInterface<Definition>
 */
class TagVoid implements TagVoidInterface
{
    /**
     * @var HtmlFactoryInterface<Definition>
     *
     * ordinarily, I would set this in the constructor.  But doing so in this case creates a circular
     * dependency in the ContainerFactory class.  The HtmlFactory has a dependency on the ContainerFactory
     * and the circular dependency is created if the ContainerFactory needs to resolve HtmlFactory as part of the
     * constructor of any given tag. The problem is resolved by having the htmlFactory get the tag from the container and
     * then manually set the HtmlFactory object using setter injection in the HtmlFactory class.
     */
    protected HtmlFactoryInterface $htmlFactory;

    public const ATTRIBUTES = 1;

    public const EVENTS = 2;

    /**
     * @var string
     */
    protected string $defId;

    /**
     * @var string
     */
    protected string $name;

    /**
     * @var array<string>
     */
    protected array $allowedAttributeDefIds = [];

    /**
     * @var array<string, AttributeInterface>
     */
    protected array $attributes = [];

    /**
     * getFactory
     * @return HtmlFactoryInterface<Definition>
     */
    public function getHtmlFactory(): HtmlFactoryInterface
    {
        return $this->htmlFactory;
    }

    /**
     * setFactory
     * @param HtmlFactoryInterface<Definition> $htmlFactory
     */
    public function setHtmlFactory(HtmlFactoryInterface $htmlFactory): void
    {
        $this->htmlFactory = $htmlFactory;
    }

    public function getDefId(): string
    {
        return $this->defId;
    }

    public function setDefId(string $defId): void
    {
        $this->defId = $defId;
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
     * getAllowedAttributeDefIds
     * @return array<string>
     */
    public function getAllowedAttributeDefIds(): array
    {
        return $this->allowedAttributeDefIds;
    }

    /**
     * setAllowedAttributeDefIds
     * @param array<string> $allowedAttributeDefIds
     */
    public function setAllowedAttributeDefIds(array $allowedAttributeDefIds): void
    {
        $this->allowedAttributeDefIds = $allowedAttributeDefIds;
    }

    /**
     * isAllowedAttribute
     * @param AttributeInterface|string $attribute
     * @return bool
     */
    public function isAllowedAttribute(AttributeInterface|string $attribute): bool
    {
        /**
         * as far as I know it is not "illegal" to put any event into any tag, although there are some form-based
         * events that are typically used in forms.  See https://www.w3schools.com/tags/ref_eventattributes.asp
         * for a place to start
         */
        if (!is_string($attribute)) {
            if ($attribute instanceof EventInterface || $attribute->isGlobal()) {
                return true;
            } else {
                $defId = $attribute->getDefId();
            }
        } else {
            $defId = $attribute;
        }

        if (in_array($defId, $this->getAllowedAttributeDefIds())) return true;
        if (in_array($defId, $this->getHtmlFactory()->getDefinitionIds(DefinitionType::Attribute)) &&
            empty($this->getAllowedAttributeDefIds())) return true;
        if (in_array($defId, $this->getHtmlFactory()->getDefinitionIds(DefinitionType::Event))) return true;

        return false;
    }

    /**
     * getAttribute
     * @param string $defId
     * @return AttributeInterface|null
     */
    public function getAttribute(string $defId): AttributeInterface|null
    {
        return $this->attributes[$defId] ?? null;
    }

    /**
     * setAttribute
     * @param string|AttributeInterface $attribute
     * @param ...$values
     * @return $this
     * @throws AttributeNotAllowedException
     * @throws InvalidDefinitionIdException
     */
    public function setAttribute(string|AttributeInterface $attribute, ...$values): TagVoid
    {
        /**
         * convert $attribute if necessary to AttributeInterface
         */
        if (is_string($attribute)) {
            $attribute = $this->makeOrGetAttribute($attribute);
        }
        $attribute->setValue(...$values);
        $this->attributes[$attribute->getDefId()] = $attribute;
        return $this;
    }

    /**
     * setCustomData
     * @param string|AttributeCustomDataInterface $attribute
     *
     * If $attribute is a string, then it is interpreted as a definition id.  Normally, attribute definition id's need
     * to be unique within the attribute container.  But since this is a custom attribute, it does not exist within
     * the container at all.  The 'uniqueness' of the defId is what distinguishes it from other attributes within this
     * element. It can be any string that you want as long as it starts with 'data-'.  It was tempting to make the
     * parameter just the part of the string after the 'data-' prefix, but in order to get the attribute, you *must*
     * use the entire identifier (e.g. 'data-foo').  Here's why: it would be confusing but not technically wrong to
     * create a custom attribute called 'data-name', for example, for an element that already has a name attribute.
     * Then if you accessed it by the suffix (e.g. 'name'), you would not know whether you are
     * referring to the name attribute or the data-name attribute. (Either that or you would end up setting the
     * attribute with one name and getting it with another). So to keep things symmetrical and neat, custom
     * attributes always use the fully qualified name (which is used as the definition id and the key within the
     * attributes array).
     *
     * @return TagVoid<Definition>
     */
    public function setCustomData(
        string|AttributeCustomDataInterface $attribute,
        string $value,
        ValTesterInterface $valTester = null
    ): TagVoid {
        if (is_string($attribute)) {
            $attribute = $this->getHtmlFactory()->makeCustomData($attribute, $value, $valTester);
        } else {
            /**
             * if not null, set the value tester *before* setting the value :)
             */
            if ($valTester) {
                $attribute->setTester($valTester);
            }
            $attribute->setValue($value);
        }
        $this->attributes[$attribute->getDefId()] = $attribute;
        return $this;
    }

    /**
     * setEvent
     * @param EventInterface $event
     * @return TagVoidInterface<Definition>
     */
    public function setEvent(EventInterface $event): TagVoidInterface
    {
        $this->attributes[$event->getDefId()] = $event;
        return $this;
    }

    /**
     * __call
     * @param string|AttributeInterface $attribute
     * @param array<string> $arguments
     * @return mixed
     * @throws AttributeNotAllowedException
     * @throws InvalidDefinitionIdException
     * in terms of semantics, I would really rather that we could use __set here.  But php requires that __set return
     * void, whereas __call returns mixed, which is necessary for creating fluent setters for attribute values.
     */
    public function __call(string|AttributeInterface $attribute, array $arguments): mixed
    {
        /**
         * unpack the array into an argument list for setAttribute
         */
        return $this->setAttribute($attribute, ...$arguments);
    }

    /**
     * __get
     * make or get the attribute
     * @param string $defId
     * @return AttributeInterface|null
     */
    public function __get(string $defId): ?AttributeInterface
    {
        return $this->makeOrGetAttribute($defId);
    }

    protected function makeOrGetAttribute(string $defId) : AttributeInterface
    {
        /**
         * if the attribute exists, return it.
         */
        if ($attribute = $this->getAttribute($defId)) return $attribute;

        /**
         * even if the allowedAttributes array is empty (meaning that any attribute is OK), the isAllowedAttribute
         * method will return false if the defId is in the container but is not an attribute or an event (e.g. if it
         * is an element, a valueTester or an 'other')
         */
        if (!$this->isAllowedAttribute($defId)) {
            throw new AttributeNotAllowedException($defId, $this->getDefId());
        }

        /**
         * do we know how to make it?
         */
        if (!$type = $this->getHtmlFactory()->getDefinitionType($defId)) {
            throw new InvalidDefinitionIdException($defId);
        }

        $method =  ($type == 'Attribute') ? 'makeAttribute' : 'makeEvent';
        $result = $this->getHtmlFactory()->$method($defId);

        assert($result instanceof  AttributeInterface);
        return $result;
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
     * @param string $defId
     * removes an attribute (or event)
     */
    public function removeAttribute(string $defId): void
    {
        unset($this->attributes[$defId]);
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
