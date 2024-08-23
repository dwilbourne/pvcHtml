<?php

/**
 * @author: Doug Wilbourne (dougwilbourne@gmail.com)
 */

declare(strict_types=1);

namespace pvc\html\tag\abstract;

use Psr\Container\ContainerInterface;
use pvc\html\attribute\abstract\Event;
use pvc\html\config\HtmlConfig;
use pvc\html\err\InvalidAttributeEventNameException;
use pvc\html\err\InvalidGetValueCall;
use pvc\html\err\UnsetAttributeNameException;
use pvc\html\err\UnsetTagNameException;
use pvc\interfaces\html\attribute\AttributeInterface;
use pvc\interfaces\html\tag\TagVoidInterface;
use pvc\interfaces\validator\ValTesterInterface;

/**
 * class TagVoid.  Base class for all html output in the pvc framework.  This class produces and
 * handles html5 compliant tags / code.
 *
 * This class is for "voidTags" or "empty" tags, which are tags that do not have closing tags (e.g. "<br>" or "<col>")
 */
class TagVoid implements TagVoidInterface
{
    const ATTRIBUTES = 1;

    const EVENTS = 2;

    /**
     * @var string
     */
    protected string $name;

    /**
     * @var array<string, AttributeInterface>
     */
    protected array $attributes = [];

    /**
     * @var ContainerInterface
     */
    protected ContainerInterface $attributeFactory;

    public function __construct(ContainerInterface $attributeFactory)
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
     * setAttribute
     * @param AttributeInterface $attribute
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
     * @param string $value
     * @param ValTesterInterface<string> $tester
     *
     * It was tempting to eliminate this method and allow set to make custom data attributes as well.  But
     * because the $name argument to set is a string, the logic would be to create a custom attribute
     * if there is no such standard attribute.  Thus, there would be no way to catch a typo in the call to
     * set
     */
    public function setCustomData(string $name, string $value, ValTesterInterface $tester): void
    {
        $attribute = $this->attributes[$name] ?? $this->attributeFactory->get('customData');
        $attribute->setName($name);
        $attribute->setTester($tester);
        $attribute->setValue($value);
        $this->attributes[$name] = $attribute;
    }
    /**
     * getAttribute
     * @param string $name
     * @return AttributeInterface|null
     */
    public function getAttribute(string $name): AttributeInterface|null
    {
        return $this->attributes[$name] ?? null;
    }

    /**
     * getAttributes
     * @param int $attributeTypes
     * @return array|AttributeInterface[]
     */
    public function getAttributes(int $attributeTypes = self::ATTRIBUTES | self::EVENTS): array
    {
        $callback = function ($item) use ($attributeTypes) {
            /**
             * $item is an attribute if it is not an event.......
             */
            $isEvent = ($item instanceof Event) ? 2 : 0;
            $isAttribute = $isEvent ? 0 : 1;


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
     * @return string|array<string>|bool
     *
     * this magic getter returns attribute values and event scripts, not with the objects themselves.
     * If you want to get the attribute/event object, use getAttribute explicitly.
     *
     * In terms of return values, one typically thinks that attribute/event values are all strings (which is true
     * when they are rendered). But AttributeVoid has boolean values and AttributeMultiValue has an
     * array of values.
     */
    public function __get(string $name): string|array|bool|null
    {
        $attribute = $this->attributes[$name] ?? null;
        return $attribute?->getValue();
    }

    /**
     * __set
     * @param string $name
     * @param string|array<string> $value
     */
    public function __set(string $name, string|array|bool $value): void
    {
        if (!HtmlConfig::isValidAttributeName($name)) {
            throw new InvalidAttributeEventNameException($name);
        }
        $attribute = $this->attributes[$name] ?? $this->attributeFactory->get($name);
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
