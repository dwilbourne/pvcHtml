<?php

/**
 * @author: Doug Wilbourne (dougwilbourne@gmail.com)
 */
declare (strict_types=1);

namespace pvcTests\html\attribute\abstract;

use PHPUnit\Framework\TestCase;
use pvc\html\attribute\abstract\Event;
use pvc\html\err\InvalidEventNameException;
use pvc\html\err\InvalidEventScriptException;

class EventTest extends TestCase
{
    /**
     * @var Event
     */
    protected Event $event;

    /**
     * @var string
     */
    protected string $eventName = 'onclick';

    /**
     * @var string
     */
    protected string $script = 'some javascript';

    public function setUp(): void
    {
        $this->event = new Event();
    }

    /**
     * testSetNameThrowsExceptionWithInvalidString
     * @throws InvalidEventNameException
     * @covers \pvc\html\attribute\abstract\Event::setName
     */
    public function testSetNameThrowsExceptionWithInvalidString(): void
    {
        self::expectException(InvalidEventNameException::class);
        $this->event->setName('foo');
    }

    /**
     * testSetNameThrowsExceptionWithEmptyString
     * @throws InvalidEventNameException
     * @covers \pvc\html\attribute\abstract\Event::setName
     */
    public function testSetNameThrowsExceptionWithEmptyString(): void
    {
        self::expectException(InvalidEventNameException::class);
        $this->event->setName('');
    }

    /**
     * testSetGetEventName
     * @throws InvalidEventNameException
     * @covers \pvc\html\attribute\abstract\Event::setName
     * @covers \pvc\html\attribute\abstract\Event::getName
     */
    public function testSetGetEventName(): void
    {
        $eventName = 'onblur';
        $this->event->setName($eventName);
        self::assertEquals($eventName, $this->event->getName());
    }

    /**
     * testSetGetScript
     * @throws InvalidEventScriptException
     * @covers \pvc\html\attribute\abstract\Event::setValue
     * @covers \pvc\html\attribute\abstract\Event::getValue
     * @covers \pvc\html\attribute\abstract\Event::setScript
     * @covers \pvc\html\attribute\abstract\Event::getScript
     */
    public function testSetGetScript(): void
    {
        $script = 'some more javascript that is different';
        $this->event->setValue($script);
        self::assertEquals($script, $this->event->getValue());
        $newScript = 'something different';
        $this->event->setScript($newScript);
        self::assertEquals($newScript, $this->event->getScript());
    }

    /**
     * testRenderReturnsEmptyStringWhenScriptIsNotSet
     * @throws InvalidEventNameException
     * @covers \pvc\html\attribute\abstract\Event::render
     */
    public function testRenderReturnsEmptyStringWhenScriptIsNotSet(): void
    {
        $this->event->setName($this->eventName);
        $expectedOutput = '';
        self::assertEquals($expectedOutput, $this->event->render());
    }

    /**
     * testRender
     * @covers \pvc\html\attribute\abstract\Event::render
     */
    public function testRender(): void
    {
        $this->event->setName($this->eventName);
        $this->event->setValue($this->script);
        $expectedOutput = $this->eventName . '=\'' . $this->script . '\'';
        self::assertEquals($expectedOutput, $this->event->render());
    }
}
