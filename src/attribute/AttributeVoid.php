<?php

/**
 * @author: Doug Wilbourne (dougwilbourne@gmail.com)
 */

declare(strict_types=1);

namespace pvc\html\abstract\attribute;

use pvc\html\abstract\err\InvalidAttributeNameException;
use pvc\interfaces\html\attribute\AttributeVoidInterface;

/**
 * Class AttributeVoid
 */
class AttributeVoid implements AttributeVoidInterface
{
    /**
     * @var string
     */
    protected string $name;

    /**
     * @param string $name
     * @throws InvalidAttributeNameException
     */
    public function __construct(string $name)
    {
        $this->setName($name);
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
     * @throws InvalidAttributeNameException
     *
     */
    protected function setName(string $name): void
    {
        if (!$this->isValidAttributeName($name)) {
            throw new InvalidAttributeNameException();
        }
        $this->name = $name;
    }

    /**
     * isValidAttributeName
     * @param string $name
     * @return bool
     *
     * This regex restricts the name to start with a lower case letter and then can be followed by lower case letters
     * and numbers and hyphens.
     *
     *  As a practical matter, pre-defined html attribute names are purely alphabetic with a couple that are
     *  hyphenated.  And since the usual manner of
     *  creating an attribute is in a factory / container, most times the attribute names come right from the
     *  html specification.  However, you can create an attribute using an arbitrary name and at least in some browsers,
     *  you can get at the value using javascript even if the name is not prefixed with 'data-'.  So this
     *  validation tries to find the middle ground between what the language spec says and how browsers
     *  actually work.
     *
     *  Moreover, according to various online sources, the data attribute name (i.e. custom attribute names that
     *  are prefixed with 'data-') must be at least one character long, must be prefixed with 'data-', and
     *  should not contain any uppercase letters.  This method is inherited and used by the AttributeCustomData
     *  class.
     */
    protected function isValidAttributeName(string $name): bool
    {
        $pattern = '/^[a-z]+[a-z0-9\-]*$/';
        return (bool) preg_match($pattern, $name);
    }


    /**
     * render
     * @return string
     */
    public function render(): string
    {
        return $this->getName();
    }

}