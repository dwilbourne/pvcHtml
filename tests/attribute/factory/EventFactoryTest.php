<?php

/**
 * @author: Doug Wilbourne (dougwilbourne@gmail.com)
 */
declare (strict_types=1);

namespace pvcTests\html\attribute\factory;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use pvc\html\attribute\abstract\Event;
use pvc\html\attribute\factory\EventFactory;
use pvc\html\err\InvalidEventNameException;
use pvc\interfaces\validator\ValTesterInterface;

class EventFactoryTest extends TestCase
{
    protected EventFactory $factory;

    protected ValTesterInterface|MockObject $scriptTester;

    public function setUp(): void
    {
        $this->scriptTester = $this->createMock(ValTesterInterface::class);
        /**
         * set a default return value on the tester
         */
        $this->scriptTester->method('testValue')->willReturn(true);
        $this->factory = new EventFactory($this->scriptTester);
    }

    /**
     * testConstruct
     * @covers \pvc\html\attribute\factory\EventFactory::__construct
     */
    public function testConstruct(): void
    {
        self::assertInstanceOf(EventFactory::class, $this->factory);
    }

    /**
     * testSetGetScriptTester
     * @covers \pvc\html\attribute\factory\EventFactory::setScriptTester
     * @covers \pvc\html\attribute\factory\EventFactory::getScriptTester
     */
    public function testSetGetScriptTester(): void
    {
        self::assertEquals($this->scriptTester, $this->factory->getScriptTester());
    }

    /**
     * testMakeEvent
     * @covers \pvc\html\attribute\factory\EventFactory::makeEvent
     */
    public function testMakeEvent(): void
    {
        $eventName = 'onclick';
        $script = 'some javascript';
        self::assertInstanceOf(Event::class, $this->factory->makeEvent($eventName, $script));
    }

    /**
     * testMakeEventFails
     * @covers \pvc\html\attribute\factory\EventFactory::makeEvent
     */
    public function testMakeEventFails(): void
    {
        $eventName = 'foo';
        $script = 'some javascript';
        self::expectException(InvalidEventNameException::class);
        $event = $this->factory->makeEvent($eventName, $script);
        unset($event);
    }
}
