<?php

/**
 * @author: Doug Wilbourne (dougwilbourne@gmail.com)
 */
declare(strict_types=1);

namespace pvc\html\attribute\abstract;

use pvc\html\config\HtmlConfig;
use pvc\html\err\InvalidAttributeEventNameException;
use pvc\interfaces\html\attribute\AttributeInterface;
use pvc\interfaces\validator\ValTesterInterface;

/**
 * Class Attribute
 * @template ValueType
 * @implements AttributeInterface<ValueType>
 *
 */
abstract class Attribute implements AttributeInterface
{
    /**
     * @var string
     */
    protected string $name;

    /**
     * @var ValTesterInterface
     */
    protected ValTesterInterface $tester;

    /**
     * @var bool
     * many (most?) attributes values are not case-sensitive, but some are.  A good example is the 'id'
     * attribute
     */
    protected bool $valueIsCaseSensitive = false;


    /**
     * @var ValueType
     */
    protected mixed $value;

    /**
     * @param ValTesterInterface $tester
     */
    public function __construct(ValTesterInterface $tester)
    {
        $this->setTester($tester);
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
     * @param string $name
     * @throws InvalidAttributeEventNameException
     */
    public function setName(string $name): void
    {
        if (!HtmlConfig::isValidAttributeName($name)) {
            throw new InvalidAttributeEventNameException($name);
        }
        $this->name = $name;
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

    /**
     * setCaseSensitive
     * @param bool $caseSensitive
     */
    public function setCaseSensitive(bool $caseSensitive): void
    {
        $this->valueIsCaseSensitive = $caseSensitive;
    }

    /**
     * valueIsCaseSensitive
     * @return bool
     */
    public function valueIsCaseSensitive(): bool
    {
        return $this->valueIsCaseSensitive;
    }

}
