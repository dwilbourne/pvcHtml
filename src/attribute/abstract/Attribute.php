<?php

/**
 * @author: Doug Wilbourne (dougwilbourne@gmail.com)
 */
declare(strict_types=1);

namespace pvc\html\attribute\abstract;

use pvc\html\config\AttributeTypes;
use pvc\html\config\HtmlConfig;
use pvc\html\err\InvalidAttributeNameException;
use pvc\interfaces\html\attribute\AttributeInterface;
use pvc\interfaces\validator\ValTesterInterface;

/**
 * Class Attribute
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
     * @param ValTesterInterface<string> $defaultValTester
     */
    public function __construct(ValTesterInterface $defaultValTester)
    {
        $this->setTester($defaultValTester);
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
     */
    public function setName(string $name): void
    {
        if (!HtmlConfig::isValidAttributeName($name)) {
            throw new InvalidAttributeNameException($name);
        }
        $this->name = $name;
    }

    /**
     * testValue
     * @param string $value
     * @return bool
     */
    protected function testValue(string $value): bool
    {
        return $this->tester->testValue($value);
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
    public function setTester(ValTesterInterface $tester): void
    {
        $this->tester = $tester;
    }
}
