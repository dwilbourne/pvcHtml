<?php

/**
 * @author: Doug Wilbourne (dougwilbourne@gmail.com)
 */
declare(strict_types=1);

namespace pvc\html\abstract\attribute;

use pvc\html\abstract\err\InvalidAttributeValueException;
use pvc\interfaces\html\attribute\EventInterface;

/**
 * Class Event
 */
class Event extends AttributeVoid implements EventInterface
{
    protected string $script = '';

    /**
     * isValidAttributeName
     * @param string $name
     * @return bool
     * need to override the testing of the attribute name in the AttributeVoid class because javascript
     * event names are lower case, alphabetic only.  This is different from the restrictions placed on all other
     * attribute names.
     */
    protected function isValidAttributeName(string $name): bool
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
        /**
         * script cannot be empty
         */
        if (empty($script)) {
            throw new InvalidAttributeValueException($this->getName());
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
        if (empty($this->getScript())) {
            throw new InvalidAttributeValueException($this->getName());
        }
        return $this->getName() . "='" . $this->getScript() . "'";
    }
}