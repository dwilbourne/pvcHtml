<?php

/**
 * @author: Doug Wilbourne (dougwilbourne@gmail.com)
 */
declare (strict_types=1);

namespace pvcTests\html\abstract\attribute;

use pvc\html\abstract\attribute\Event;
use PHPUnit\Framework\TestCase;
use pvc\html\abstract\err\InvalidAttributeNameException;
use pvc\html\abstract\err\InvalidAttributeValueException;

use function PHPUnit\Framework\assertEquals;

class EventTest extends TestCase
{
    protected string $eventName;

    protected Event $event;

    public function setUp(): void
    {
        $this->eventName = 'onchange';
        $this->event = new Event($this->eventName);
    }

    /**
     * testConstructAndSetName
     * @covers \pvc\html\abstract\attribute\Event::isValidAttributeName
     */
    public function testConstructAndSetName(): void
    {
        self::assertInstanceOf(Event::class, $this->event);
        self::assertEquals($this->eventName, $this->event->getName());
    }

    /**
     * testConstructFailsWithMixedCase
     * @covers \pvc\html\abstract\attribute\Event::isValidAttributeName
     */
    public function testConstructFailsWithMixedCase(): void
    {
        self::expectException(InvalidAttributeNameException::class);
        $event = new Event('onChange');
    }

    /**
     * testSetScriptThrowsExceptionWithEmptyScript
     * @covers \pvc\html\abstract\attribute\Event::setScript
     */
    public function testSetScriptThrowsExceptionWithEmptyScript(): void
    {
        $script = '';
        self::expectException(InvalidAttributeValueException::class);
        $this->event->setScript($script);
    }

    /**
     * testRenderThrowsExceptionWithEmptyScript
     * @throws InvalidAttributeValueException
     * @covers \pvc\html\abstract\attribute\Event::render
     */
    public function testRenderThrowsExceptionWithEmptyScript(): void
    {
        self::expectException(InvalidAttributeValueException::class);
        $this->event->render();
    }

    /**
     * testSetGetScript
     * @throws InvalidAttributeValueException
     * @covers \pvc\html\abstract\attribute\Event::setScript
     * @covers \pvc\html\abstract\attribute\Event::getScript
     */
    public function testSetGetScript(): void
    {
        $script = 'some javascript';
        $this->event->setScript($script);
        self:;assertEquals($script, $this->event->getScript());
    }

    /**
     * testRender
     * @throws InvalidAttributeValueException
     * @covers \pvc\html\abstract\attribute\Event::render
     */
    public function testRender(): void
    {
        $value = 'bar\'s';
        $this->event->setScript($value);
        $expectedRendering = $this->eventName . '=\'bar\'s\'';
        self::assertEquals($expectedRendering, $this->event->render());
    }
}
