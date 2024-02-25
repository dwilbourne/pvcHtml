<?php

/**
 * @author: Doug Wilbourne (dougwilbourne@gmail.com)
 */
declare (strict_types=1);

namespace pvcTests\html\attribute\abstract;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use pvc\html\attribute\abstract\Event;
use pvc\html\err\InvalidEventNameException;
use pvc\html\err\InvalidEventScriptException;
use pvc\interfaces\validator\ValTesterInterface;

class EventTest extends TestCase
{
    /**
     * @var Event
     */
    protected Event $event;

    /**
     * @var string
     */
    protected $eventName = 'onclick';

    /**
     * @var string
     */
    protected $script = 'some javascript';

    /**
     * @var ValTesterInterface|MockObject
     */
    protected ValTesterInterface|MockObject $scriptTester;

    public function setUp(): void
    {
        $this->scriptTester = $this->createMock(ValTesterInterface::class);
        $this->event = new Event($this->scriptTester);
    }

    /**
     * testConstructor
     * @covers \pvc\html\attribute\abstract\Event::__construct
     */
    public function testConstructor(): void
    {
        self::assertInstanceOf(Event::class, $this->event);
    }

    /**
     * testSetNameThrowsExceptionWithInvalidString
     * @throws InvalidEventNameException
     * @covers \pvc\html\attribute\abstract\Event::setName
     * @covers \pvc\html\tag\abstract\TagAttributes::isValidEvent
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
     * @covers \pvc\html\tag\abstract\TagAttributes::isValidEvent
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
     * @covers \pvc\html\tag\abstract\TagAttributes::isValidEvent
     */
    public function testSetGetEventName(): void
    {
        $eventName = 'onblur';
        $this->event->setName($eventName);
        self::assertEquals($eventName, $this->event->getName());
    }

    /**
     * testSetGetScriptTester
     * @covers \pvc\html\attribute\abstract\Event::getTester
     * @covers \pvc\html\attribute\abstract\Event::setTester
     */
    public function testSetGetScriptTester(): void
    {
        self::assertEquals($this->scriptTester, $this->event->getTester());
    }

    /**
     * testSetScriptThrowsExceptionWhenTesterFails
     * @throws InvalidEventScriptException
     * @covers \pvc\html\attribute\abstract\Event::setValue
     */
    public function testSetScriptThrowsExceptionWhenTesterFails(): void
    {
        $this->scriptTester->method('testValue')->willReturn(false);
        self::expectException(InvalidEventScriptException::class);
        $this->event->setValue('');
    }

    /**
     * testSetGetScript
     * @throws InvalidEventScriptException
     * @covers \pvc\html\attribute\abstract\Event::setValue
     * @covers \pvc\html\attribute\abstract\Event::getValue
     */
    public function testSetGetScript(): void
    {
        $script = 'some more javascript that is different';
        $this->scriptTester->method('testValue')->willReturn(true);
        $this->event->setValue($script);
        self::assertEquals($script, $this->event->getValue());
    }

    /**
     * testRender
     * @covers \pvc\html\attribute\abstract\Event::render
     */
    public function testRender(): void
    {
        $this->event->setName($this->eventName);
        $this->scriptTester->method('testValue')->willReturn(true);
        $this->event->setValue($this->script);
        $expectedOutput = $this->eventName . '=\'' . $this->script . '\'';
        self::assertEquals($expectedOutput, $this->event->render());
    }
}
