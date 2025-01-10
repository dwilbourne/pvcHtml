<?php

/**
 * @author: Doug Wilbourne (dougwilbourne@gmail.com)
 */

declare(strict_types=1);

namespace pvc\html\element;

use pvc\html\err\AttributeNotAllowedException;
use pvc\html\err\InvalidDefinitionIdException;
use pvc\interfaces\html\attribute\AttributeCustomDataInterface;
use pvc\interfaces\html\attribute\AttributeInterface;
use pvc\interfaces\html\attribute\EventInterface;
use pvc\interfaces\html\builder\definitions\DefinitionFactoryInterface;
use pvc\interfaces\html\builder\definitions\DefinitionType;
use pvc\interfaces\html\builder\HtmlBuilderInterface;
use pvc\interfaces\html\element\ElementVoidInterface;
use pvc\interfaces\validator\ValTesterInterface;

/**
 * class ElementVoid
 *
 * Base class for all html elements in the pvc framework.  This class produces and
 * handles html5 compliant tags / code.
 *
 * This class is for "voidElements" or "empty" tags, which are tags that do not have closing tags (e.g. "<br>" or "<col>")
 *
 * @template VendorSpecificDefinition of DefinitionFactoryInterface
 * @implements ElementVoidInterface<VendorSpecificDefinition>
 */
class ElementVoid implements ElementVoidInterface
{
    /**
     * @var HtmlBuilderInterface<VendorSpecificDefinition>
     *
     * ordinarily, I would set this in the constructor.  But doing so in this case creates a circular
     * dependency in the ContainerFactory class.  The HtmlBuilder has a dependency on the ContainerFactory
     * and the circular dependency is created if the ContainerFactory needs to resolve HtmlBuilder as part of the
     * constructor of any given element. The problem is resolved by having the htmlBuilder get the element from the container and
     * then manually set the HtmlFactory object using setter injection in the HtmlFactory class.
     */
    protected HtmlBuilderInterface $htmlBuilder;

    /**
     * TODO: consider using actual deftypes instead of these constants
     */
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
     * @var array<string>
     */
    protected array $globalAttributes = [
        'accesskey',
        'class',
        'contenteditable',
        'dir',
        'draggable',
        'enterkeyhint',
        'hidden',
        'id',
        'inert',
        'inputmode',
        'lang',
        'popover',
        'spellcheck',
        'style',
        'tabindex',
        'title',
        'translate'
    ];


    /**
     * @var array<string, AttributeInterface>
     */
    protected array $attributes = [];

    /**
     * getFactory
     * @return HtmlBuilderInterface<VendorSpecificDefinition>
     */
    public function getHtmlBuilder(): HtmlBuilderInterface
    {
        return $this->htmlBuilder;
    }

    /**
     * setFactory
     * @param HtmlBuilderInterface<VendorSpecificDefinition> $htmlBuilder
     */
    public function setHtmlBuilder(HtmlBuilderInterface $htmlBuilder): void
    {
        $this->htmlBuilder = $htmlBuilder;
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
     * getGlobalAttributeDefIds
     * @return string[]
     */
    public function getGlobalAttributeDefIds(): array
    {
        return $this->globalAttributes;
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
     * changing the element id implies having to change the list of attributes the element supports so attributes are
     * reinitialized!
     * @param string $name
     */
    public function setName(string $name): void
    {
        $this->name = $name;
        $this->attributes = [];
    }

    /**
     * setAllowedAttributeDefIds
     * @param array<string> $allowedAttributeDefIds
     */
    public function setAllowedAttributeDefIds(array $allowedAttributeDefIds): void
    {
        $this->allowedAttributeDefIds = $allowedAttributeDefIds;
    }

    public function getAllowedAttributeDefIds(): array
    {
        return $this->allowedAttributeDefIds;
    }

    /**
     * isAllowedAttribute
     * @param AttributeInterface|string $attribute
     * @return bool
     */
    public function isAllowedAttribute(AttributeInterface|string $attribute): bool
    {
        $defId = ($attribute instanceof AttributeInterface) ? $attribute->getDefId() : $attribute;

        /**
         * if it is a global attributeArrayElement, it is ok
         */
        if (in_array($defId, $this->getGlobalAttributeDefIds())) return true;

        /**
         * if the defId is in the list of allowed attributes, it is OK
         */
        if (in_array($defId, $this->getAllowedAttributeDefIds())) return true;

        /**
         * as far as I know, it is not 'illegal' to add any event to an element
         */
        if ($this->htmlBuilder->getDefinitionType($defId) == DefinitionType::Event) return true;

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
    public function setAttribute(string|AttributeInterface $attribute, ...$values): ElementVoid
    {
        /**
         * convert $attributeArrayElement if necessary to AttributeInterface
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
     * If $attributeArrayElement is a string, then it is interpreted as a definition id.  Normally, attributeArrayElement definition id's need
     * to be unique within the attributeArrayElement container.  But since this is a custom attributeArrayElement, it does not exist within
     * the container at all.  The 'uniqueness' of the defId is what distinguishes it from other attributes within this
     * element. It can be any string that you want as long as it starts with 'data-'.  It was tempting to make the
     * parameter just the part of the string after the 'data-' prefix, but in order to get the attributeArrayElement, you *must*
     * use the entire identifier (e.g. 'data-foo').  Here's why: it would be confusing but not technically wrong to
     * create a custom attributeArrayElement called 'data-name', for example, for an element that already has a name attributeArrayElement.
     * Then if you accessed it by the suffix (e.g. 'name'), you would not know whether you are
     * referring to the name attributeArrayElement or the data-name attributeArrayElement. (Either that or you would end up setting the
     * attributeArrayElement with one name and getting it with another). So to keep things symmetrical and neat, custom
     * attributes always use the fully qualified name (which is used as the definition id and the key within the
     * attributes array).
     *
     * @return ElementVoid<VendorSpecificDefinition>
     */
    public function setCustomData(
        string|AttributeCustomDataInterface $attribute,
        string $value,
        ValTesterInterface $valTester = null
    ): ElementVoid {
        if (is_string($attribute)) {
            $attribute = $this->getHtmlBuilder()->makeCustomData($attribute, $value, $valTester);
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
     * @return ElementVoidInterface<VendorSpecificDefinition>
     */
    public function setEvent(EventInterface $event): ElementVoidInterface
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
     * void, whereas __call returns mixed, which is necessary for creating fluent setters for attributeArrayElement values.
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
     * make or get the attributeArrayElement
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
         * if the attributeArrayElement exists, return it.
         */
        if ($attribute = $this->getAttribute($defId)) return $attribute;

        /**
         * even if the allowedAttributes array is empty (meaning that any attributeArrayElement is OK), the isAllowedAttribute
         * method will return false if the defId is in the container but is not an attributeArrayElement or an event (e.g. if it
         * is an element, a valueTester or an 'other')
         */
        if (!$this->isAllowedAttribute($defId)) {
            throw new AttributeNotAllowedException($defId, $this->getDefId());
        }

        /**
         * do we know how to make it and is it either an attribute or an event?
         */
        $defType = $this->getHtmlBuilder()->getDefinitionType($defId);
        if (!in_array($defType, [DefinitionType::Attribute, DefinitionType::Event])) {
            throw new InvalidDefinitionIdException($defId);
        }

        $method =  ($defType == DefinitionType::Attribute) ? 'makeAttribute' : 'makeEvent';
        $result = $this->getHtmlBuilder()->$method($defId);

        assert($result instanceof  AttributeInterface);
        return $result;
    }

    /**
     * getAttributes
     * @param int $defTypeMask
     * @return array<AttributeInterface>
     */
    public function getAttributes(int $defTypeMask = self::ATTRIBUTES | self::EVENTS): array
    {
        $callback = function (AttributeInterface $attributesArrayElement) use ($defTypeMask) : bool {
            /**
             * @var DefinitionType $defType
             */
            $defType = $this->getHtmlBuilder()->getDefinitionType($attributesArrayElement->getDefId());
            $isAttribute = ($defType == DefinitionType::Attribute) ? self::ATTRIBUTES : 0;
            $isEvent = ($defType == DefinitionType::Event) ? self::EVENTS : 0;
            return (($defTypeMask & $isAttribute) || ($defTypeMask & $isEvent));
        };

        return array_filter($this->attributes, $callback);
    }

    /**
     * removeAttribute
     * @param string $defId
     * removes an attributeArrayElement (or event)
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
