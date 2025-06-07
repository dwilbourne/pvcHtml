<?php

/**
 * @author: Doug Wilbourne (dougwilbourne@gmail.com)
 */

declare(strict_types=1);

namespace pvc\html\attribute;

use pvc\html\err\InvalidAttributeIdNameException;
use pvc\html\err\InvalidAttributeValueException;
use pvc\htmlbuilder\definitions\types\AttributeValueDataType;
use pvc\interfaces\html\attribute\AttributeInterface;
use pvc\interfaces\validator\ValTesterInterface;
use pvc\validator\val_tester\always_true\AlwaysTrueTester;

/**
 * Class Attribute
 */
abstract class Attribute implements AttributeInterface
{
    /**
     * @var string
     */
    protected(set) string $name;

    protected AttributeValueDataType $valueDataType;

    /**
     * @var bool
     * many (most?) attributes values are not case-sensitive, but some are.  A good example is the 'id'
     * attribute.
     */
    protected bool $isCaseSensitive;


    /**
     * @var ValTesterInterface<string|int|bool>
     *
     * Attributes usually accept values whose validity can be determined in a context free manner.  But there
     * are a few values which are context-sensitive.  For example, the 'for' attribute has value(s) which are id(s)
     * of other elements within the same structural block.  The ValTester object is a context free tester, because
     * it knows nothing about other attributes and elements which are outside its own scope.
     */
    protected ValTesterInterface $tester;

    /**
     * @var array<string|int>|string|integer|bool
     */
    protected array|string|int|bool $value;

    /**
     * @param  string  $name
     * @param AttributeValueDataType $valueType
     * @param  bool  $caseSensitive
     * @param  ValTesterInterface<string|int|bool>|null  $tester
     */
    public function __construct(
        string $name,
        ?AttributeValueDataType $valueType = null,
        ?bool $caseSensitive = null,
        ?ValTesterInterface $tester = null
    ) {
        $this->setName($name);
        $this->valueDataType = $valueType ?? AttributeValueDataType::String;
        $this->isCaseSensitive = $caseSensitive ?? false;
        $this->tester = $tester ?? new AlwaysTrueTester();
    }

    /**
     * getName
     * @return string
     */
    protected function getName(): string
    {
        return $this->name;
    }

    /**
     * setName
     * @param string $name
     * @throws InvalidAttributeIdNameException
     *
     */
    protected function setName(string $name): void
    {
        if (!$this->isValidAttributeIdName($name)) {
            throw new InvalidAttributeIdNameException($name);
        }
        $this->name = $name;
    }

    /**
     * isValidAttributeName
     * @param string $name
     * @return bool
     *
     * This regex restricts the name to start with a lower case letter and then can be followed by lower case letters
     * and numbers and hyphens.
     *
     *  As a practical matter, pre-defined html attribute names are purely alphabetic with a couple that are
     *  hyphenated.  And since the usual manner of
     *  creating an attribute is in a htmlBuilder / container, most times the attribute names come right from the
     *  html specification.  However, you can create an attribute using an arbitrary name and at least in some browsers,
     *  you can get at the value using javascript even if the id is not prefixed with 'data-'.  So this
     *  validation tries to find the middle ground between what the language spec says and how browsers
     *  actually work.
     *
     *  Moreover, according to various online sources, the data attribute id (i.e. custom attribute names that
     *  are prefixed with 'data-') must be at least one character long, must be prefixed with 'data-', and
     *  should not contain any uppercase letters.  This method is inherited and used by the AttributeCustomData
     *  class.
     */
    protected function isValidAttributeIdName(string $name): bool
    {
        $pattern = '/^[a-z]+[a-z0-9\-]*$/';
        return (bool) preg_match($pattern, $name);
    }

    /**
     * @return array<string>|string|int|bool|null
     */
    public function getValue(): array|string|int|bool|null
    {
        return $this->value ?? null;
    }

    public function unsetValue(): void
    {
        unset($this->value);
    }

    /**
     * @param array<string|int>|bool|int|string...$values
     * @return void
     */
    public function setValue(...$values): void
    {
        /**
         * all attributes must have a value (void attributes have a value of
         * true) if they are to be rendered.
         */
        if (empty($values)) {
            throw new InvalidAttributeValueException($this->getName(), '{empty value}');
        }

        $callback = match ($this->valueDataType) {
            AttributeValueDataType::Integer => 'is_integer',
            AttributeValueDataType::String => 'is_string',
            AttributeValueDataType::Bool => 'is_bool',
        };

        /**
         * confirm the data type and value.  String values are potentially subject
         * to case conversion, so build a new array of (possibly case-converted)
         * values before setting the $value attribute in this object
         */
        $newValues = [];
        foreach ($values as $value) {
            if (!$callback($value)) {
                throw new InvalidAttributeValueException($this->name, $value);
            }
            /**
             * if the value is not case-sensitive, set it to lower case
             */
            if (is_string($value) && !$this->isCaseSensitive) {
                $value = strtolower($value);
            }
            /**
             * value must be validated by the tester.
             */
            if (!$this->tester->testValue($value)) {
                throw new InvalidAttributeValueException($this->getName(), $value);
            }
            $newValues[] = $value;
        }
        /**
         * confirm the parameter count
         */
        $value = $this->confirmParameterCount($newValues);
        $this->value = $value;
    }

    /**
     * @param array<string|int|bool> $values
     *
     * @return array<string>|string|int|bool
     */
    abstract protected function confirmParameterCount(array $values): mixed;

    /**
     * @return string
     */
    abstract function render(): string;

}