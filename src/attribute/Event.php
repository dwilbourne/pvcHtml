<?php

/**
 * @author: Doug Wilbourne (dougwilbourne@gmail.com)
 */
declare(strict_types=1);

namespace pvc\html\attribute;

use pvc\html\err\InvalidAttributeValueException;
use pvc\interfaces\html\attribute\EventInterface;
use pvc\interfaces\validator\ValTesterInterface;

/**
 * Class Event
 */
class Event extends AttributeVoid implements EventInterface
{
    /**
     * @var ValTesterInterface<string>
     */
    protected ValTesterInterface $scriptTester;

    protected string $script = '';

    /**
     * @param ValTesterInterface<string> $scriptTester
     */
    public function __construct(ValTesterInterface $scriptTester)
    {
         $this->scriptTester = $scriptTester;
    }

    /**
     * isValidAttributeName
     * @param string $name
     * @return bool
     * need to override the testing of the attribute id in the AttributeVoid class because javascript
     * event names are lower case, alphabetic only.  This is different from the restrictions placed on all other
     * attribute names.
     */
    protected function isValidAttributeIdName(string $name): bool
    {
        $pattern = '/^[a-z]*$/';
        return (bool) preg_match($pattern, $name);
    }

    /**
     * setScript
     * @param string $script
     */
    public function setScript(string $script): void
    {
        if (!$this->scriptTester->testValue($script)) {
            throw new InvalidAttributeValueException($this->getName(), $script);
        }
        $this->script = $script;
    }

    /**
     * getScript
     * @return string
     */
    public function getScript(): string
    {
        return $this->script;
    }

    /**
     * render
     * @return string
     */
    public function render(): string
    {
        return $this->getName() . "='" . $this->getScript() . "'";
    }
}