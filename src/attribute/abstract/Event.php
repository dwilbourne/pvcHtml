<?php

/**
 * @author: Doug Wilbourne (dougwilbourne@gmail.com)
 */
declare(strict_types=1);

namespace pvc\html\attribute\abstract;

use pvc\html\err\InvalidEventNameException;
use pvc\html\err\InvalidEventScriptException;
use pvc\html\tag\abstract\TagAttributes;
use pvc\interfaces\html\attribute\EventInterface;
use pvc\interfaces\validator\ValTesterInterface;

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
     * @var ValTesterInterface<string>
     */
    protected ValTesterInterface $tester;


    /**
     * @param ValTesterInterface<string> $tester
     */
    public function __construct(ValTesterInterface $tester)
    {
        $this->setTester($tester);
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        if (!TagAttributes::isValidEvent($name)) {
            throw new InvalidEventNameException();
        }
        $this->name = $name;
    }

    /**
     * getValue
     * @return string
     */
    public function getValue(): mixed
    {
        return $this->script;
    }

    /**
     * setValue
     * @param string $value
     * @throws InvalidEventScriptException
     */
    public function setValue(mixed $value): void
    {
        if (!$this->tester->testValue($value)) {
            throw new InvalidEventScriptException();
        }
        $this->script = $value;
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
     * render
     * @return string
     */
    public function render(): string
    {
        return $this->name . "='" . $this->script . "'";
    }
}