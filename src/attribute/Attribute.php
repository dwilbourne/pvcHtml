<?php

/**
 * @author: Doug Wilbourne (dougwilbourne@gmail.com)
 */
declare(strict_types=1);

namespace pvc\html\attribute;

use pvc\html\err\InvalidAttributeNameException;
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
     */
    protected ValTesterInterface $tester;

    /**
     * @var bool
     * many (most?) attributes values are not case-sensitive, but some are.  A good example is the 'id'
     * attribute
     */
    protected bool $valueIsCaseSensitive = false;

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
     * @param bool $caseSensitive
     */
    public function setValueIsCaseSensitive(bool $caseSensitive): void
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
