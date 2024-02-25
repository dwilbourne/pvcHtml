<?php

/**
 * @author Doug Wilbourne (dougwilbourne@gmail.com)
 */

declare(strict_types=1);

namespace pvcTests\html\tag\abstract;


use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use pvc\html\attribute\abstract\AttributeSingleValue;
use pvc\html\attribute\abstract\Event;
use pvc\html\attribute\factory\CustomDataAttributeFactory;
use pvc\html\attribute\factory\EventFactory;
use pvc\html\err\InvalidAttributeException;
use pvc\html\err\InvalidAttributeValueException;
use pvc\html\err\InvalidEventNameException;
use pvc\html\tag\abstract\TagVoid;
use pvc\interfaces\html\attribute\AttributeMultiValueInterface;
use pvc\interfaces\html\attribute\AttributeSingleValueInterface;
use pvc\interfaces\html\attribute\AttributeVoidInterface;
use pvc\interfaces\validator\ValTesterInterface;

class TagVoidTest extends TestCase
{
    /**
     * @var ContainerInterface|MockObject
     */
    protected ContainerInterface|MockObject $container;

    /**
     * @var string
     */
    protected string $tagName;

    /**
     * @var TagVoid
     */
    protected TagVoid $tag;

    /**
     * setUp
     */
    public function setUp(): void
    {
        $this->tagName = 'a';
        $this->container = $this->createMock(ContainerInterface::class);
        $this->tag = new TagVoid($this->container);
        $this->tag->setTagName($this->tagName);
    }

    /**
     * testConstruct
     * @covers \pvc\html\tag\abstract\TagVoid::__construct
     */
    public function testConstruct(): void
    {
        self::assertInstanceOf(TagVoid::class, $this->tag);
    }

    /**
     * testSetGetTagName
     * @covers \pvc\html\tag\abstract\TagVoid::getTagName
     * @covers \pvc\html\tag\abstract\TagVoid::setTagName
     */
    public function testSetGetTagName(): void
    {
        self::assertEquals($this->tagName, $this->tag->getTagName());
    }

    /**
     * testGetAttributeReturnsNull
     * @covers \pvc\html\tag\abstract\TagVoid::getAttributeValue()
     * @covers \pvc\html\tag\abstract\TagVoid::getAttributes()
     */
    public function testGetAttributeReturnsNull(): void
    {
        self::assertEmpty($this->tag->getAttributes());
        self::assertNull($this->tag->getAttributeValue('href'));
    }

    /**
     * testSetGetAttributes
     * @covers \pvc\html\tag\abstract\TagVoid::setAttribute()
     * @covers \pvc\html\tag\abstract\TagVoid::getAttributeValue()
     * @covers \pvc\html\tag\abstract\TagVoid::getAttributes()
     */
    public function testSetGetAttributes(): void
    {
        /**
         * because there's a call to a static function in the TagAttributes class, which cannot be mocked, we have to
         * use real attribute names in the testing.....
         */
        $attr1Name = 'href';
        $attr1Value = 'www.microsoft.com';
        $attr1 = $this->createMock(AttributeSingleValueInterface::class);
        $attr1->expects($this->once())->method('setValue')->with($attr1Value);
        $attr1->method('getValue')->willReturn($attr1Value);

        $attr2Name = 'target';
        $attr2Value = '_blank';
        $attr2 = $this->createMock(AttributeSingleValueInterface::class);
        $attr2->expects($this->once())->method('setValue')->with($attr2Value);
        $attr2->expects($this->once())->method('getValue')->willReturn($attr2Value);

        $expectedResult1 = [$attr1Name => $attr1Value];
        $expectedResult2 = [$attr1Name => $attr1Value, $attr2Name => $attr2Value];

        $matcher = $this->exactly(2);
        $this->container
            ->expects($matcher)
            ->method('get')
            ->willReturnCallback(function () use ($matcher, $attr1, $attr2) {
                return match ($matcher->getInvocationCount()) {
                    1 => $attr1,
                    2 => $attr2,
                };
            });

        $this->tag->setAttribute($attr1Name, $attr1Value);
        self::assertEquals($expectedResult1, $this->tag->getAttributes());

        $this->tag->setAttribute($attr2Name, $attr2Value);
        self::assertEqualsCanonicalizing($expectedResult2, $this->tag->getAttributes());
    }

    /**
     * testSetMultipleAttributesAtOnce
     * @covers \pvc\html\tag\abstract\TagVoid::setAttributes()
     * @covers \pvc\html\tag\abstract\TagVoid::getAttributes()
     */
    public function testSetMultipleAttributesAtOnce(): void
    {
        $attr1Name = 'href';
        $attr1Value = 'www.microsoft.com';
        $attr1 = $this->createMock(AttributeSingleValueInterface::class);
        $attr1->expects($this->once())->method('setValue')->with($attr1Value);
        $attr1->method('getValue')->willReturn($attr1Value);

        $attr2Name = 'target';
        $attr2Value = '_blank';
        $attr2 = $this->createMock(AttributeSingleValueInterface::class);
        $attr2->expects($this->once())->method('setValue')->with($attr2Value);
        $attr2->expects($this->once())->method('getValue')->willReturn($attr2Value);

        $expectedResult = [$attr1Name => $attr1Value, $attr2Name => $attr2Value];

        $matcher = $this->exactly(2);
        $this->container
            ->expects($matcher)
            ->method('get')
            ->willReturnCallback(function () use ($matcher, $attr1, $attr2) {
                return match ($matcher->getInvocationCount()) {
                    1 => $attr1,
                    2 => $attr2,
                };
            });
        $attributes = [
            $attr1Name => $attr1Value,
            $attr2Name => $attr2Value,
        ];
        $this->tag->setAttributes($attributes);
        self::assertEqualsCanonicalizing($expectedResult, $this->tag->getAttributes());
    }

    /**
     * testSetAttributeWithEventNameAndScript
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     * @throws \ReflectionException
     * @throws \pvc\html\err\InvalidAttributeException
     * @throws \pvc\html\err\MissingTagAttributesException
     * @covers \pvc\html\tag\abstract\TagVoid::setAttribute
     */
    public function testSetAttributeWithEventNameAndScript(): void
    {
        $eventName = 'onclick';
        $script = 'some javascript';

        $mockEventFactory = $this->createMock(EventFactory::class);
        $this->container->method('get')->with(EventFactory::class)->willReturn($mockEventFactory);

        $mockEvent = $this->createMock(Event::class);

        $mockEventFactory->method('makeEvent')->willReturn($mockEvent);
        $mockEvent->method('getName')->willReturn($eventName);
        $mockEvent->method('getValue')->willReturn($script);

        $this->tag->setAttribute($eventName, $script);
        $expectedResult = [$eventName => $script];
        self::assertEquals($expectedResult, $this->tag->getAttributes());
    }

    /**
     * testSetAttributeThrowsExceptionWithInvalidAttributeName
     * @throws InvalidAttributeException
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     * @throws \ReflectionException
     * @throws \pvc\html\err\MissingTagAttributesException
     * @covers \pvc\html\tag\abstract\TagVoid::setAttribute
     */
    public function testSetAttributeThrowsExceptionWithInvalidAttributeName(): void
    {
        $attributeName = 'foo';
        $value = 'bar';
        self::expectException(InvalidAttributeException::class);
        $this->tag->setAttribute($attributeName, $value);
    }

    /**
     * testGetEventsReturnsNull
     * @throws \pvc\html\err\InvalidEventNameException
     * @covers \pvc\html\tag\abstract\TagVoid::getEventScript()
     * @covers \pvc\html\tag\abstract\TagVoid::getEvents()
     */
    public function testGetEventScriptReturnsNull(): void
    {
        self::assertEmpty($this->tag->getEvents());
        self::assertNull($this->tag->getEventScript('onclick'));
    }

    /**
     * testAddGetEvents
     * @covers \pvc\html\tag\abstract\TagVoid::setEvent()
     * @covers \pvc\html\tag\abstract\TagVoid::getEventScript()
     * @covers \pvc\html\tag\abstract\TagVoid::getEvents()
     */
    public function testAddGetEvents(): void
    {
        $event1Name = 'onclick';
        $event1Script = 'some javascript';
        $event1 = $this->createMock(Event::class);
        $event1->method('getName')->willReturn($event1Name);
        $event1->method('getValue')->willReturn($event1Script);

        $event2Name = 'onchange';
        $event2Script = 'more javascript';
        $event2 = $this->createMock(Event::class);
        $event2->method('getName')->willReturn($event2Name);
        $event2->method('getValue')->willReturn($event2Script);

        $attr1Name = 'href';
        $attr1Value = 'www.microsoft.com';
        $attr1 = $this->createMock(AttributeSingleValueInterface::class);
        $attr1->method('getName')->willReturn($attr1Name);
        $attr1->method('getValue')->willReturn($attr1Value);

        $eventFactory = $this->createMock(EventFactory::class);

        $matcherContainer = $this->exactly(3);
        $this->container
            ->expects($matcherContainer)
            ->method('get')
            ->willReturnCallback(function () use ($matcherContainer, $eventFactory, $attr1) {
                return match ($matcherContainer->getInvocationCount()) {
                    1 => $eventFactory,
                    2 => $eventFactory,
                    3 => $attr1,
                };
            });

        $matcherEventFactory = $this->exactly(2);
        $eventFactory
            ->expects($matcherEventFactory)
            ->method('makeEvent')
            ->willReturnCallback(function () use ($matcherEventFactory, $event1, $event2) {
                return match ($matcherEventFactory->getInvocationCount()) {
                    1 => $event1,
                    2 => $event2,
                };
            });

        $this->tag->setEvent($event1Name, $event1Script);
        $expectedResult = [$event1Name => $event1Script];
        self::assertEquals($expectedResult, $this->tag->getEvents());

        $this->tag->setEvent($event2Name, $event2Script);
        $expectedResult = [$event1Name => $event1Script, $event2Name => $event2Script];
        self::assertEqualsCanonicalizing($expectedResult, $this->tag->getEvents());

        $this->tag->setAttribute($attr1Name, $attr1Value);

        /**
         * verify that attributes do not get returned as events
         */
        self::assertEqualsCanonicalizing($expectedResult, $this->tag->getEvents());

        /**
         * confirm that events do get returned as attributes
         */
        $expectedResult = [$event1Name => $event1Script, $event2Name => $event2Script, $attr1Name => $attr1Value];
        self::assertEqualsCanonicalizing($expectedResult, $this->tag->getAttributes());
    }

    /**
     * testSetEventThrowsExceptionWhenCalledWithExistingAttributeName
     * @throws InvalidEventNameException
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     * @throws \pvc\html\err\InvalidAttributeNameException
     * @throws \pvc\html\err\InvalidEventScriptException
     * @covers \pvc\html\tag\abstract\TagVoid::setEvent()
     */
    public function testSetEventThrowsExceptionWhenCalledWithExistingAttributeName(): void
    {
        $event1Name = 'onclick';
        $event1Script = 'some javascript';
        $event1 = $this->createMock(Event::class);
        $event1->method('getName')->willReturn($event1Name);
        $event1->method('getValue')->willReturn($event1Script);

        $attr1Name = 'href';
        $attr1Value = 'www.microsoft.com';
        $attr1 = $this->createMock(AttributeSingleValueInterface::class);
        $attr1->method('getName')->willReturn($attr1Name);
        $attr1->method('getValue')->willReturn($attr1Value);

        $eventFactory = $this->createMock(EventFactory::class);
        $eventFactory->method('makeEvent')->willReturn($event1);

        $matcherContainer = $this->exactly(2);
        $this->container
            ->expects($matcherContainer)
            ->method('get')
            ->willReturnCallback(function () use ($matcherContainer, $eventFactory, $attr1) {
                return match ($matcherContainer->getInvocationCount()) {
                    1 => $eventFactory,
                    2 => $attr1,
                };
            });

        $this->tag->setEvent($event1Name, $event1Script);
        $this->tag->setAttribute($attr1Name, $attr1Value);
        /**
         * calling setEvent on an existing attribute causes an exception
         */
        $newScript = 'this is more javascript;';
        self::expectException(InvalidEventNameException::class);
        $this->tag->setEvent($attr1Name, $newScript);
    }

    /**
     * testSetEventOverwritesOldScriptWithNewScriptOnExistingEvent
     * @throws InvalidEventNameException
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     * @throws \pvc\html\err\InvalidAttributeNameException
     * @throws \pvc\html\err\InvalidEventScriptException
     * @covers \pvc\html\tag\abstract\TagVoid::setEvent()
     */
    public function testSetEventOverwritesOldScriptWithNewScriptOnExistingEvent(): void
    {
        $event1Name = 'onclick';
        $event1Script = 'some javascript';
        $newScript = 'this is even more javascript';
        $event1 = $this->createMock(Event::class);
        $event1->method('getName')->willReturn($event1Name);

        $eventFactory = $this->createMock(EventFactory::class);
        $eventFactory->method('makeEvent')->willReturn($event1);

        $this->container
            ->expects($this->once())
            ->method('get')
            ->with(EventFactory::class)
            ->willReturn($eventFactory);

        /**
         * this was confusing to write because the normal flow is that setValue would be called twice.  BUT, when
         * 'makeEvent' is called, it returns a mock (because the factory is a mock) and so setValue is not called
         * when the event is created and added to the attribute list.
         */
        $event1->expects($this->once())->method('setValue');
        $this->tag->setEvent($event1Name, $event1Script);
        $this->tag->setEvent($event1Name, $newScript);
    }


    /**
     * testSetGetSingleValueAttributeValue
     * @covers \pvc\html\tag\abstract\TagVoid::__set
     * @covers \pvc\html\tag\abstract\TagVoid::__get
     */
    public function testSetGetSingleValueAttributeValue(): void
    {
        $attribute = $this->createMock(AttributeSingleValueInterface::class);
        $attributeName = 'href';
        $attributeValue = 'bar';
        $this->container->method('get')->with($attributeName)->willReturn($attribute);
        $attribute->expects($this->once())->method('setValue')->with($attributeValue);
        $attribute->expects($this->once())->method('getValue')->willReturn($attributeValue);

        $this->tag->$attributeName = $attributeValue;
        self::assertEquals($attributeValue, $this->tag->href);
    }

    /**
     * testSetGetMultiValueAttributeValues
     * @covers \pvc\html\tag\abstract\TagVoid::setAttribute
     * @covers \pvc\html\tag\abstract\TagVoid::getAttributeValue
     */
    public function testSetGetMultiValueAttributeValues(): void
    {
        $attribute = $this->createMock(AttributeMultiValueInterface::class);
        $attributeName = 'href';
        $attributeValues = ['bar', 'baz', 'quux'];
        $this->container->method('get')->with($attributeName)->willReturn($attribute);
        $attribute->expects($this->once())->method('setValue')->with($attributeValues);
        $attribute->expects($this->once())->method('getValue')->willReturn($attributeValues);
        $this->tag->setAttribute($attributeName, $attributeValues);
        self::assertEqualsCanonicalizing($attributeValues, $this->tag->getAttributeValue($attributeName));
    }

    /**
     * testSetGetAttributeVoidValue
     * @covers \pvc\html\tag\abstract\TagVoid::setAttribute
     * @covers \pvc\html\tag\abstract\TagVoid::getAttributeValue
     */
    public function testSetGetAttributeVoidValue(): void
    {
        $attribute = $this->createMock(AttributeVoidInterface::class);
        $attributeName = 'href';
        $attributeValue = false;
        $this->container->method('get')->with($attributeName)->willReturn($attribute);
        $attribute->method('getName')->willReturn($attributeName);
        $attribute->expects($this->once())->method('setValue')->with($attributeValue);
        $attribute->expects($this->once())->method('getValue')->willReturn($attributeValue);
        $this->tag->setAttribute($attributeName, $attributeValue);
        self::assertEqualsCanonicalizing($attributeValue, $this->tag->getAttributeValue($attributeName));
    }

    /**
     * testSetGetEventScript
     * @covers \pvc\html\tag\abstract\TagVoid::setAttribute
     * @covers \pvc\html\tag\abstract\TagVoid::getAttributeValue
     */
    public function testSetGetEventScript(): void
    {
        $eventName = 'onclick';
        $eventScript = 'some script';
        $event = $this->createMock(Event::class);
        $eventFactory = $this->createMock(EventFactory::class);

        $this->container->method('get')->with(EventFactory::class)->willReturn($eventFactory);
        $eventFactory->method('makeEvent')->with($eventName)->willReturn($event);
        $event->method('getName')->willReturn($eventName);

        $event->expects($this->once())->method('getValue')->willReturn($eventScript);

        $this->tag->setAttribute($eventName, $eventScript);
        self::assertEqualsCanonicalizing($eventScript, $this->tag->getAttributeValue($eventName));
    }

    /**
     * testSetGetCustomDataAttributeWithNoValueTester
     * @throws \pvc\html\err\InvalidAttributeValueException
     * @throws \pvc\html\err\InvalidCustomDataNameException
     * @covers \pvc\html\tag\abstract\TagVoid::setCustomAttribute
     */
    public function testSetGetCustomDataAttributeWithNoValueTester(): void
    {
        $attrName = 'foo';
        $value = 'bar';

        $customDataAttribute = $this->createMock(AttributeSingleValue::class);
        $customDataAttribute->method('getValue')->willReturn($value);

        $customDataAttributeFactory = $this->createMock(CustomDataAttributeFactory::class);

        $this->container
            ->method('get')
            ->with(CustomDataAttributeFactory::class)
            ->willReturn($customDataAttributeFactory);

        $customDataAttributeFactory->method('makeCustomData')->willReturn($customDataAttribute);
        $this->tag->setCustomAttribute($attrName, $value);
        $expectedValue = [$attrName => $value];
        self::assertEquals($expectedValue, $this->tag->getAttributes());
    }

    /**
     * testSetGetCustomDataAttributeWithValueTesterFailsWhenTesterReturnsFalse
     * @throws InvalidAttributeValueException
     * @throws \pvc\html\err\InvalidCustomDataNameException
     * @covers \pvc\html\tag\abstract\TagVoid::setCustomAttribute
     */
    public function testSetCustomDataAttributeWithValueTesterSetsTester(): void
    {
        $attrName = 'foo';
        $value = 'bar';
        $tester = $this->createMock(ValTesterInterface::class);

        $customDataAttribute = $this->createMock(AttributeSingleValue::class);
        $customDataAttribute->expects($this->once())->method('setTester')->with($tester);

        $customDataAttributeFactory = $this->createMock(CustomDataAttributeFactory::class);

        $this->container
            ->method('get')
            ->with(CustomDataAttributeFactory::class)
            ->willReturn($customDataAttributeFactory);

        $customDataAttributeFactory->method('makeCustomData')->willReturn($customDataAttribute);

        $this->tag->setCustomAttribute($attrName, $value, $tester);
    }

    /**
     * testMagicSetterGetter
     * @covers \pvc\html\tag\abstract\TagVoid::__set
     * @covers \pvc\html\tag\abstract\TagVoid::__get
     */
    public function testMagicSetterGetter(): void
    {
        $attribute = $this->createMock(AttributeVoidInterface::class);
        $attributeName = 'href';
        $attributeValue = false;
        $this->container->method('get')->with($attributeName)->willReturn($attribute);
        $attribute->method('getName')->willReturn($attributeName);
        $attribute->expects($this->once())->method('setValue')->with($attributeValue);
        $attribute->expects($this->once())->method('getValue')->willReturn($attributeValue);
        $this->tag->$attributeName = $attributeValue;
        self::assertEqualsCanonicalizing($attributeValue, $this->tag->$attributeName);
    }

    /**
     * testGenerateOpeningTagWithNoAttributesOrEvents
     * @covers \pvc\html\tag\abstract\TagVoid::generateOpeningTag
     */
    public function testGenerateOpeningTagWithNoAttributesOrEvents(): void
    {
        $expectedResult = '<a>';
        self::assertEquals($expectedResult, $this->tag->generateOpeningTag());
    }

    /**
     * testGenerateOpeningTagWithAttributesAndEvents
     * @covers \pvc\html\tag\abstract\TagVoid::generateOpeningTag
     */
    public function testGenerateOpeningTagWithAttributesAndEvents(): void
    {
        $event1Name = 'onclick';
        $event1Script = 'some script';
        $event1 = $this->createMock(Event::class);
        $event1->method('getName')->willReturn($event1Name);
        $event1->method('getValue')->willReturn($event1Script);
        $event1->method('render')->willReturn($event1Name . '=\'' . $event1Script . '\'');

        $event2Name = 'onchange';
        $event2Script = 'more javascript';
        $event2 = $this->createMock(Event::class);
        $event2->method('getName')->willReturn($event2Name);
        $event2->method('getValue')->willReturn($event2Script);
        $event2->method('render')->willReturn($event2Name . '=\'' . $event2Script . '\'');

        $attr1Name = 'href';
        $attr1Value = 'bar';
        $attr1 = $this->createMock(AttributeSingleValueInterface::class);
        $attr1->method('getName')->willReturn($attr1Name);
        $attr1->method('getValue')->willReturn($attr1Value);
        $attr1->method('render')->willReturn($attr1Name . '=\'' . $attr1Value . '\'');

        $eventFactory = $this->createMock(EventFactory::class);

        $matcherContainer = $this->exactly(3);
        $this->container
            ->expects($matcherContainer)
            ->method('get')
            ->willReturnCallback(function () use ($matcherContainer, $eventFactory, $attr1) {
                return match ($matcherContainer->getInvocationCount()) {
                    1 => $eventFactory,
                    2 => $eventFactory,
                    3 => $attr1,
                };
            });

        $matcherEventFactory = $this->exactly(2);
        $eventFactory
            ->expects($matcherEventFactory)
            ->method('makeEvent')
            ->willReturnCallback(function () use ($matcherEventFactory, $event1, $event2) {
                return match ($matcherEventFactory->getInvocationCount()) {
                    1 => $event1,
                    2 => $event2,
                };
            });

        $this->tag->setEvent($event1Name, $event1Script);
        $this->tag->setEvent($event2Name, $event2Script);
        $this->tag->setAttribute($attr1Name, $attr1Value);

        $expectedResult = "<a onclick='some script' onchange='more javascript' href='bar'>";
        self::assertEquals($expectedResult, $this->tag->generateOpeningTag());
    }
}