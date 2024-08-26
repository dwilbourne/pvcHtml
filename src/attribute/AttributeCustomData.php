<?php

/**
 * @author: Doug Wilbourne (dougwilbourne@gmail.com)
 */
declare(strict_types=1);

namespace pvc\html\attribute;

use pvc\html\err\InvalidCustomDataNameException;
use pvc\interfaces\html\config\HtmlConfigInterface;
use pvc\interfaces\validator\ValTesterInterface;

/**
 * Class AttributeCustomData
 */
class AttributeCustomData extends AttributeSingleValue
{
    /**
     * @var ValTesterInterface<string>
     */
    protected ValTesterInterface $customDataNameTester;

    /**
     * @param ValTesterInterface<string> $valTester
     * @param HtmlConfigInterface $htmlConfig
     */
    public function __construct(
        ValTesterInterface $valTester,
        HtmlConfigInterface $htmlConfig
    ) {
        parent::__construct($valTester, $htmlConfig);
    }

    /**
     * setName
     * @param string $name
     * @throws InvalidCustomDataNameException
     *
     * override parent class because parent class tests to make sure that the attribute name appears in the
     * configuration file so that it is a valid attribute and so that we have a value valTester.
     * Obviously, a custom attribute is not 'known' and the value valTester injection
     * is part of the tagFactory.  html requires the 'data-' prefix for rendering.  It is stored with the prefix so
     * that we can allow a tag to have an 'href' attribute and a 'data-href' attribute.
     */
    public function setName(string $name): void
    {
        /**
         * according to various online sources, the data attribute name must be at least one character long and must
         * be prefixed with 'data-'. It should not contain any uppercase letters.  This regex restricts it to lower
         * case letters and numbers
         */
        $pattern = '/^[a-z0-9]*$/';
        if (!preg_match($pattern, $name)) {
            throw new InvalidCustomDataNameException();
        }
        $this->name = 'data-' . $name;
    }
}
