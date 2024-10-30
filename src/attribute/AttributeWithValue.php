<?php

/**
 * @author: Doug Wilbourne (dougwilbourne@gmail.com)
 */

declare(strict_types=1);

namespace pvc\html\attribute;

use pvc\html\err\UnsetValueTesterException;
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
     * I would normally put in a constructor with the ValueTester object as an argument since it's a dependency.  But
     * ContainerFactory makes void attributes as well as attributes that have values within the same method and void
     * attributes obviously do not have value testers.  The Containerfactory checks to see if there is a value tester
     * in the definition and then sets it if there is one using a method call.  So do not put the dependency into the
     * constructor here.
     */
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
    public function setTester(ValTesterInterface $tester): void
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
}
