<?php

/**
 * @author: Doug Wilbourne (dougwilbourne@gmail.com)
 */
declare(strict_types=1);

namespace pvc\html\attribute\factory;


use pvc\html\attribute\abstract\AttributeSingleValue;
use pvc\html\err\InvalidCustomDataNameException;
use pvc\interfaces\validator\ValTesterInterface;

/**
 * Class AttributeFactory
 */
class CustomDataAttributeFactory
{
    /**
     * @var ValTesterInterface<string>
     */
    protected ValTesterInterface $nameTester;

    /**
     * @param ValTesterInterface<string> $customDataNameTester
     */
    public function __construct(ValTesterInterface $customDataNameTester)
    {
        $this->nameTester = $customDataNameTester;
    }

    /**
     * makeCustomData
     * @param string $name
     */
    public function makeCustomData(string $name): AttributeSingleValue
    {
        if (!$this->nameTester->testValue($name)) {
            throw new InvalidCustomDataNameException();
        }
        $name = 'data-' . $name;
        $attribute = new AttributeSingleValue($name);
        /**
         * at this point there is no value tester because we cannot know the kind of custom data which will be stored
         * in the attribute
         */
        return $attribute;
    }
}
