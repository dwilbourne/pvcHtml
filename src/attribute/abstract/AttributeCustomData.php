<?php

/**
 * @author: Doug Wilbourne (dougwilbourne@gmail.com)
 */
declare(strict_types=1);

namespace pvc\html\attribute\abstract;

use pvc\html\err\InvalidCustomDataNameException;
use pvc\interfaces\html\attribute\AttributeCustomDataInterface;
use pvc\interfaces\validator\ValTesterInterface;

/**
 * Class AttributeCustomData
 *
 * custom data attribute names should be used elsewhere outside of this class using the name of the attribute
 * without the 'data-' prefix.  In other words, if you have a custom attribute named 'foo', you set the
 * name of the attribute like this:  setName('foo') and similarly, getName returns 'foo', not 'data-foo'.
 */
class AttributeCustomData extends AttributeSingleValue implements AttributeCustomDataInterface
{
    /**
     * @var ValTesterInterface<string>
     */
    protected ValTesterInterface $customDataNameTester;

    /**
     * @param ValTesterInterface<string> $customDataNameTester
     * @param ValTesterInterface<string> $valTester
     */
    public function __construct(ValTesterInterface $customDataNameTester, ValTesterInterface $valTester)
    {
        parent::__construct($valTester);
        $this->customDataNameTester = $customDataNameTester;
    }

    /**
     * setName
     * @param string $name
     *
     * override parent class because parent class tests to make sure that the attribute name appears in the
     * configuration file so that it is a valid attribute and so that we have a value valTester.
     * Obviously, a custom attribute is not 'known' and the value valTester injection
     * is part of the tagFactory.  html requires the 'data-' prefix for rendering.
     */
    public function setName(string $name): void
    {
        if (!$this->customDataNameTester->testValue($name)) {
            throw new InvalidCustomDataNameException();
        }
        $this->name = 'data-' . $name;
    }

    /**
     * getName
     * @return string
     */
    public function getName(): string
    {
        /**
         * remove the 'data-' prefix
         */
        return substr($this->name, 5);
    }
}