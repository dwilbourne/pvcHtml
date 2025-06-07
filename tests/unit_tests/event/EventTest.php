<?php

/**
 * @author: Doug Wilbourne (dougwilbourne@gmail.com)
 */
declare (strict_types=1);

namespace pvcTests\html\unit_tests\event;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use pvc\html\err\InvalidAttributeIdNameException;
use pvc\html\err\InvalidAttributeValueException;
use pvc\html\event\Event;
use pvc\htmlbuilder\definitions\types\AttributeValueDataType;
use pvc\interfaces\validator\ValTesterInterface;

use function PHPUnit\Framework\assertEquals;

class EventTest extends TestCase
{
    protected string $name = 'foo';
    protected AttributeValueDataType $dataType = AttributeValueDataType::String;
    protected bool $caseSensitive = true;
    protected ValTesterInterface|MockObject $scriptTester;
    protected Event $event;

    public function setUp(): void
    {
        $this->scriptTester = $this->createMock(ValTesterInterface::class);
        $this->scriptTester->method('testValue')->willReturn(true);
        $this->event = new Event(
            $this->name,
            $this->dataType,
            $this->caseSensitive,
            $this->scriptTester,
        );
    }

    /**
     * testConstruct
     *
     * @covers \pvc\html\event\Event::__construct
     */
    public function testConstructAndSetName(): void
    {
        self::assertInstanceOf(Event::class, $this->event);
    }

    /**
     * testConstructFailsWithMixedCase
     *
     * @covers \pvc\html\event\Event::isValidAttributeIdName
     */
    public function testConstructFailsWithMixedCase(): void
    {
        $name = 'onChange';
        self::expectException(InvalidAttributeIdNameException::class);
        $this->event = new Event(
            $name,
            $this->dataType,
            $this->caseSensitive,
            $this->scriptTester,
        );
    }

    /**
     * testSetScriptGetScriptCallBaseMethods
     *
     * @covers \pvc\html\event\Event::setScript
     * @covers \pvc\html\event\Event::getScript
     */
    public function testSetScriptGetScript(): void
    {
        $script = 'some javascript';
        $this->event->setScript($script);
        self::assertEquals($script, $this->event->getScript());
    }

    /**
     * testSetGetScript
     *
     * @throws InvalidAttributeValueException
     * @covers \pvc\html\event\Event::setScript
     * @covers \pvc\html\event\Event::getScript
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
     *
     * @throws InvalidAttributeValueException
     * @covers \pvc\html\event\Event::render
     */
    public function testRenderWithScriptSet(): void
    {
        $value = 'bar';
        $this->event->setScript($value);
        $expectedRendering = $this->name . '=\'bar\'';
        self::assertEquals($expectedRendering, $this->event->render());
    }

    /**
     * @return void
     * @covers \pvc\html\event\Event::render
     */
    public function testRenderWithNoScriptSet(): void
    {
        $expectedRendering = null;
        self::assertEquals($expectedRendering, $this->event->render());
    }
}
