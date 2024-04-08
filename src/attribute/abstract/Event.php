<?php

/**
 * @author: Doug Wilbourne (dougwilbourne@gmail.com)
 */
declare(strict_types=1);

namespace pvc\html\attribute\abstract;

use pvc\html\config\HtmlConfig;
use pvc\html\err\InvalidEventNameException;
use pvc\interfaces\html\attribute\EventInterface;

/**
 * class Event
 *
 * note that event names are validated (because they are not flexible ever:  the DOM events are what they
 * are), whereas attribute names can be anything.  The javascript engine can pick up these unorthodox
 * attributes and use their values if you really want.  You are supposed to use the data-* custom attributes,
 * but browsers will (typically?) not kick out other kinds of unsanctioned attributes.
 */
class Event implements EventInterface
{

    /**
     * @var string
     */
    protected string $name;

    /**
     * @var string
     */
    protected string $script;

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
     * @throws InvalidEventNameException
     */
    public function setName(string $name): void
    {
        if (!HtmlConfig::isValidEventName($name)) {
            throw new InvalidEventNameException();
        }
        $this->name = $name;
    }

    /**
     * getScript
     * @return string|null
     */
    public function getScript(): string|null
    {
        return $this->script ?? null;
    }

    /**
     * setScript
     * @param string $script
     */
    public function setScript(string $script): void
    {
        $this->script = $script;
    }

    /**
     * getValue
     * @return string|null
     */
    public function getValue(): mixed
    {
        return $this->getScript();
    }

    /**
     * setValue
     * @param string $value
     */
    public function setValue($value): void
    {
        $this->setScript($value);
    }

    /**
     * render
     * @return string
     */
    public function render(): string
    {
        if (!empty($this->getScript())) {
            return $this->name . "='" . $this->getScript() . "'";
        } else {
            return '';
        }
    }
}