<?php

/**
 * @author: Doug Wilbourne (dougwilbourne@gmail.com)
 */
declare(strict_types=1);

namespace pvc\html\abstract\attribute;

use pvc\html\abstract\err\InvalidAttributeNameException;
use pvc\interfaces\html\attribute\AttributeInterface;
use pvc\interfaces\validator\ValTesterInterface;

/**
 * Class Attribute
 * @template ValueType
 * @template ValTesterType
 * @implements AttributeInterface<ValueType, ValTesterType>
 *
 */
abstract class Attribute implements AttributeInterface
{
    /**
     * @var string
     */
    protected string $name;

    /**
     * @var ValTesterInterface<ValTesterType>
     *
     * Attributes usually accept values whose validity can be determined in a context free manner.  But there
     * are a few values which are context-sensitive.  For example, the 'for' attribute has value(s) which are id(s)
     * of other elements within the same structural block.  The ValTester object is a context free tester, because
     * it knows nothing about other attributes and elements which are outside its own scope.
     */
    protected ValTesterInterface $tester;

    /**
     * @var bool
     * many (most?) attributes values are not case-sensitive, but some are.  A good example is the 'id'
     * attribute
     */
    protected bool $isCaseSensitive = false;

    /**
     * @var bool
     */
    protected bool $globalYn = false;

    /**
     * @var ValueType
     * the value type for void attributes is boolean
     * the value type for single-valued, custom, and multivalued attributes is string.
     */
    protected mixed $value;

    /**
     * @param string $name
     * @param ValTesterInterface<ValTesterType> $tester
     * @throws InvalidAttributeNameException
     *
     * the name of the attribute and the value tester are tightly coupled by design so both are set at construction.
     * Further, once instantiated, you cannot change the name of the attribute or change the value tester because
     * changing one without the other could put the object in an invalid state.
     */
    public function __construct(string $name, ValTesterInterface $tester)
    {
        $this->setName($name);
        $this->setTester($tester);
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
     * This regex restricts the name to lower case letters and numbers.
     *
     *  As a practical matter, pre-defined html attribute names are purely alphabetic.  And since the usual manner of
     *  creating an attribute is in a factory / container, most times the attribute names come right from the
     *  html specification.  Also, you can create an attribute using an arbitrary name and at least in some browsers,
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
        $pattern = '/^[a-z0-9]+$/';
        return (bool) preg_match($pattern, $name);
    }

    /**
     * getTester
     * @return ValTesterInterface<ValTesterType>
     */
    public function getTester(): ValTesterInterface
    {
        return $this->tester;
    }

    /**
     * setTester
     * @param ValTesterInterface<ValTesterType> $tester
     */
    protected function setTester(ValTesterInterface $tester): void
    {
        $this->tester = $tester;
    }

    /**
     * setValueIsCaseSensitive
     * @param bool $isCaseSensitive
     */
    public function setCaseSensitive(bool $isCaseSensitive): void
    {
        $this->isCaseSensitive = $isCaseSensitive;
    }

    /**
     * isCaseSensitive
     * @return bool
     */
    public function isCaseSensitive(): bool
    {
        return $this->isCaseSensitive;
    }

    /**
     * isGlobalYn
     * @return bool
     */
    public function isGlobalYn(): bool
    {
        return $this->globalYn;
    }

    /**
     * setGlobalYn
     * @param bool $globalYn
     */
    public function setGlobalYn(bool $globalYn): void
    {
        $this->globalYn = $globalYn;
    }
}
