<?php

/**
 * @author: Doug Wilbourne (dougwilbourne@gmail.com)
 */
declare(strict_types=1);

namespace pvc\html\attribute\abstract;

use pvc\html\err\InvalidAttributeNameException;
use pvc\interfaces\html\attribute\AttributeInterface;
use pvc\interfaces\validator\ValTesterInterface;

/**
 * Class Attribute
 * @template DataType
 * @implements AttributeInterface<DataType>
 */
abstract class Attribute implements AttributeInterface
{
    /**
     * @var string
     */
    protected string $name;

    /**
     * @var ValTesterInterface<string>
     */
    protected ValTesterInterface $tester;

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
     * method is protected so that the type of attribute is immutable.  Being able to change the name of the attribute
     * would imply changing the value nameTester and destroying any value that had already been set......
     * @param string $name
     * @throws InvalidAttributeNameException
     */
    protected function setName(string $name): void
    {
        if (empty($name)) {
            $name = '{empty string}';
            throw new InvalidAttributeNameException($name);
        }
        $this->name = $name;
    }

    abstract function render(): string;

    /**
     * testValue
     * @param string $value
     * @return bool
     * encapsulate the logic that if the nameTester is not set, then the value is assumed to be acceptable.  This logic
     * is used both by AttributeSingleValue and AttributeMultiValue
     */
    protected function testValue(string $value): bool
    {
        return (!is_null($this->getTester()) ? $this->tester->testValue($value) : true);
    }

    /**
     * getTester
     * @return ValTesterInterface<string>|null
     */
    public function getTester(): ?ValTesterInterface
    {
        return $this->tester ?? null;
    }

    /**
     * setTester
     * seems fair enough if you want to change the implementation of the nameTester to produce a change in terms
     * of what is an acceptable value
     * @param ValTesterInterface<string> $tester
     */
    public function setTester(ValTesterInterface $tester): void
    {
        $this->tester = $tester;
    }
}
