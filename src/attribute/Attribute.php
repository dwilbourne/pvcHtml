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
     */
    protected mixed $value;

    /**
     * @param ValTesterInterface<ValTesterType> $tester
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
     * @throws InvalidAttributeNameException
     */
    public function setName(string $name): void
    {
        $this->name = $name;
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
