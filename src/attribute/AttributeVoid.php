<?php

/**
 * @author: Doug Wilbourne (dougwilbourne@gmail.com)
 */

declare(strict_types=1);

namespace pvc\html\attribute;

use pvc\html\err\InvalidAttributeIdNameException;
use pvc\html\err\InvalidNumberOfParametersException;
use pvc\html\err\UnsetAttributeNameException;
use pvc\interfaces\html\attribute\AttributeInterface;

/**
 * Class AttributeVoid
 */
class AttributeVoid implements AttributeInterface
{
    /**
     * @var string
     * because the 'type' attribute appears in both input and button elements and the values of the type attribute
     * are different between the two, the id can be different from the name.  The definitions use ids of
     * type_input and type_button to differentiate between the two
     */
    protected string $id;

    /**
     * @var string
     */
    protected string $name;

    /**
     * @var bool
     * set to true if attribute is a global attribute
     */
    protected bool $global = false;

    /**
     * getId
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * setId
     * @param string $id
     */
    public function setId(string $id): void
    {
        if (!$this->isValidAttributeIdName($id)) {
            throw new InvalidAttributeIdNameException($id);
        }
        $this->id = $id;
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
     * @param string $name
     * @throws InvalidAttributeIdNameException
     *
     */
    public function setName(string $name): void
    {
        if (!$this->isValidAttributeIdName($name)) {
            throw new InvalidAttributeIdNameException($name);
        }
        $this->name = $name;
    }

    /**
     * isValidAttributeName
     * @param string $name
     * @return bool
     *
     * This regex restricts the id to start with a lower case letter and then can be followed by lower case letters
     * and numbers and hyphens.
     *
     *  As a practical matter, pre-defined html attribute names are purely alphabetic with a couple that are
     *  hyphenated.  And since the usual manner of
     *  creating an attribute is in a factory / container, most times the attribute names come right from the
     *  html specification.  However, you can create an attribute using an arbitrary id and at least in some browsers,
     *  you can get at the value using javascript even if the id is not prefixed with 'data-'.  So this
     *  validation tries to find the middle ground between what the language spec says and how browsers
     *  actually work.
     *
     *  Moreover, according to various online sources, the data attribute id (i.e. custom attribute names that
     *  are prefixed with 'data-') must be at least one character long, must be prefixed with 'data-', and
     *  should not contain any uppercase letters.  This method is inherited and used by the AttributeCustomData
     *  class.
     */
    protected function isValidAttributeIdName(string $name): bool
    {
        $pattern = '/^[a-z]+[a-z0-9\-]*$/';
        return (bool) preg_match($pattern, $name);
    }

    /**
     * isGlobal
     * @return bool
     */
    public function isGlobal(): bool
    {
        return $this->global;
    }

    /**
     * setGlobal
     * @param bool $global
     */
    public function setGlobal(bool $global): void
    {
        $this->global = $global;
    }

    /**
     * render
     * @return string
     */
    public function render(): string
    {
        return $this->getName();
    }

    /**
     * setValue
     * @param string ...$values
     * @throws InvalidNumberOfParametersException
     */
    public function setValue(...$values): void
    {
        if (!empty($values)) {
            throw new InvalidNumberOfParametersException('0');
        }
    }

    /**
     * getValue
     * @return array<string>|string|string[]|null
     */
    public function getValue(): array|string|null
    {
        return null;
    }
}