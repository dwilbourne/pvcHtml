<?php

/**
 * @author: Doug Wilbourne (dougwilbourne@gmail.com)
 */
declare (strict_types=1);

namespace pvcTests\html\unit_tests\attribute;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use pvc\html\attribute\Event;
use pvc\html\err\InvalidAttributeIdNameException;
use pvc\html\err\InvalidAttributeValueException;

use pvc\interfaces\validator\ValTesterInterface;

use function PHPUnit\Framework\assertEquals;

class EventTest extends TestCase
{
    protected ValTesterInterface|MockObject $scriptTester;
    protected Event $event;

    public function setUp(): void
    {
        $this->scriptTester = $this->createMock(ValTesterInterface::class);
        $this->event = new Event($this->scriptTester);
    }

    /**
     * testConstruct
     * @covers \pvc\html\attribute\Event::__construct
     */
    public function testConstructAndSetName(): void
    {
        self::assertInstanceOf(Event::class, $this->event);
    }

    /**
     * testConstructFailsWithMixedCase
     * @covers \pvc\html\attribute\Event::isValidAttributeIdName
     */
    public function testConstructFailsWithMixedCase(): void
    {
        self::expectException(InvalidAttributeIdNameException::class);
        $this->event->setName('onChange');
    }

    /**
     * testSetScriptThrowsExceptionWithEmptyScript
     * @covers \pvc\html\attribute\Event::setScript
     */
    public function testSetScriptThrowsExceptionWithEmptyScript(): void
    {
        $script = '';
        $this->scriptTester->method('testValue')->willReturn(false);
        self::expectException(InvalidAttributeValueException::class);
        $this->event->setScript($script);
    }

    /**
     * testSetGetScript
     * @throws InvalidAttributeValueException
     * @covers \pvc\html\attribute\Event::setScript
     * @covers \pvc\html\attribute\Event::getScript
     */
    public function testSetGetScript(): void
    {
        $script = 'some javascript';
        $this->scriptTester->method('testValue')->willReturn(true);
        $this->event->setScript($script);
        self:;assertEquals($script, $this->event->getScript());
    }

    /**
     * testRender
     * @throws InvalidAttributeValueException
     * @covers \pvc\html\attribute\Event::render
     */
    public function testRender(): void
    {
        $name = 'onchange';
        $value = 'bar\'s';
        $this->scriptTester->method('testValue')->willReturn(true);
        $this->event->setName($name);
        $this->event->setScript($value);
        $expectedRendering = $name . '=\'bar\'s\'';
        self::assertEquals($expectedRendering, $this->event->render());
    }
}
