<?php

/**
 * @author: Doug Wilbourne (dougwilbourne@gmail.com)
 */

declare(strict_types=1);

namespace pvc\html\abstract\attribute;

use pvc\html\abstract\err\InvalidAttributeNameException;
use pvc\interfaces\html\attribute\AttributeWithValueInterface;
use pvc\interfaces\validator\ValTesterInterface;

/**
 * Class AttributeWithValue
 */
abstract class AttributeWithValue extends AttributeVoid implements AttributeWithValueInterface
{
    /**
     * @var ValTesterInterface<string>
     *
     * Attributes usually accept values whose validity can be determined in a context free manner.  But there
     * are a few values which are context-sensitive.  For example, the 'for' attribute has value(s) which are id(s)
     * of other elements within the same structural block.  The ValTester object is a context free tester, because
     * it knows nothing about other attributes and elements which are outside its own scope.
     *
     * Also, values are strings when you write html by hand.  But because this library makes live objects,
     * we can work with various kinds of data datypes if we need to.
     */
    protected ValTesterInterface $tester;

    /**
     * @var bool
     * many (most?) attributes values are not case-sensitive, but some are.  A good example is the 'id'
     * attribute.
     */
    protected bool $isCaseSensitive = false;

    /**
     * @var bool
     */
    protected bool $globalYn = false;

    /**
     * @param string $name
     * @param ValTesterInterface<string> $tester
     * @throws InvalidAttributeNameException
     *
     * the name of the attribute and the value tester are tightly coupled by design so both are set at construction.
     * Further, once instantiated, you cannot change the name of the attribute or change the value tester because
     * changing one without the other could put the object in an invalid state.
     */
    public function __construct(string $name, ValTesterInterface $tester)
    {
        parent::__construct($name);
        $this->setTester($tester);
    }

    /**
     * getTester
     * @return ValTesterInterface<string>
     */
    public function getTester(): ValTesterInterface
    {
        return $this->tester;
    }

    /**
     * setTester
     * @param ValTesterInterface<string> $tester
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
