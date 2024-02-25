<?php

/**
 * @author: Doug Wilbourne (dougwilbourne@gmail.com)
 */
declare(strict_types=1);

namespace pvc\html\attribute\factory;

use pvc\html\attribute\abstract\Event;
use pvc\html\err\InvalidAttributeNameException;
use pvc\html\err\InvalidEventScriptException;
use pvc\interfaces\validator\ValTesterInterface;

/**
 * Class EventFactory
 */
class EventFactory
{
    /**
     * @var ValTesterInterface<string>
     */
    protected ValTesterInterface $scriptTester;

    /**
     * @param ValTesterInterface<string> $scriptTester
     */
    public function __construct(ValTesterInterface $scriptTester)
    {
        $this->setScriptTester($scriptTester);
    }

    /**
     * makeEvent
     * @param string $eventName
     * @param string $script
     * @return Event
     * @throws InvalidEventScriptException|InvalidAttributeNameException
     */
    public function makeEvent(string $eventName, string $script): Event
    {
        $event = new Event($this->getScriptTester());
        $event->setName($eventName);
        $event->setValue($script);
        return $event;
    }

    /**
     * getScriptTester
     * @return ValTesterInterface<string>
     */
    public function getScriptTester(): ValTesterInterface
    {
        return $this->scriptTester;
    }

    /**
     * setScriptTester
     * @param ValTesterInterface<string> $scriptTester
     */
    public function setScriptTester(ValTesterInterface $scriptTester): void
    {
        $this->scriptTester = $scriptTester;
    }
}