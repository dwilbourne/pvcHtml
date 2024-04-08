<?php

/**
 * @author Doug Wilbourne (dougwilbourne@gmail.com)
 */

declare(strict_types=1);

namespace pvcTests\html\tag\abstract;


use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use pvc\html\attribute\factory\AttributeFactory;
use pvc\html\err\InvalidAttributeException;
use pvc\html\tag\abstract\TagVoid;
use pvc\interfaces\html\attribute\AttributeCustomDataInterface;
use pvc\interfaces\html\attribute\AttributeInterface;
use pvc\interfaces\html\attribute\AttributeVoidInterface;
use pvc\interfaces\html\attribute\EventInterface;
use pvc\interfaces\validator\ValTesterInterface;

class TagVoidTest extends TestCase
{
    /**
     * @var string
     */
    protected string $tagName;

    /**
     * @var TagVoid
     */
    protected TagVoid $tag;

    protected AttributeFactory|MockObject $attributeFactory;

    /**
     * setUp
     */
    public function setUp(): void
    {
        $this->tagName = 'a';
        $this->attributeFactory = $this->createMock(AttributeFactory::class);
        $this->tag = new TagVoid($this->attributeFactory);
        $this->tag->setName($this->tagName);
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
     * @covers \pvc\html\tag\abstract\TagVoid::getName
     * @covers \pvc\html\tag\abstract\TagVoid::setName
     */
    public function testSetGetTagName(): void
    {
        self::assertEquals($this->tagName, $this->tag->getName());
    }

    /**
     * testGetAttributeReturnsNull
     * @covers \pvc\html\tag\abstract\TagVoid::getAttributes()
     * @covers \pvc\html\tag\abstract\TagVoid::getAttribute()
     * @covers \pvc\html\tag\abstract\TagVoid::getEvents()
     * @covers \pvc\html\tag\abstract\TagVoid::getEvent()
     */
    public function testGetAttributeReturnsNull(): void
    {
        self::assertEmpty($this->tag->getAttributes());
        self::assertNull($this->tag->getAttribute('href'));
        self::assertEmpty($this->tag->getEvents());
        self::assertNull($this->tag->getEvent('onclick'));
    }

    /**
     * testSetAttribute
     * @throws InvalidAttributeException
     * @covers \pvc\html\tag\abstract\TagVoid::setAttribute
     * @covers \pvc\html\tag\abstract\TagVoid::getAttribute
     * @covers \pvc\html\tag\abstract\TagVoid::getAttributes
     */
    public function testSetAttribute(): void
    {
        $attrName = 'href';
        $attrValue = 'www.microsoft.com';

        $attribute = $this->createMock(AttributeInterface::class);
        $this->attributeFactory
            ->expects($this->once())
            ->method('makeAttribute')
            ->with($attrName)
            ->willReturn($attribute);

        $valTester = $this->createMock(ValTesterInterface::class);
        $valTester->method('testValue')->willReturn(true);

        $attribute->method('getTester')->willReturn($valTester);
        $attribute->method('getName')->willReturn($attrName);

        /**
         * setAttribute is called twice below: first time to show a new attribute can be made and the second
         * time to show that an existing attribute can be set
         */
        $attribute->expects($this->exactly(2))->method('setValue')->with($attrValue);

        /**
         * illustrates that a new attribute is created
         */
        self::assertEmpty($this->tag->getAttributes());
        $this->tag->setAttribute($attrName, $attrValue);
        self::assertEquals(1, count($this->tag->getAttributes()));
        self::assertEquals($attribute, $this->tag->getAttribute($attrName));

        /**
         * illustrates that an existing attribute is updated because the tagFactory is only called once and setValue
         * is called twice and the count of attributes remains at 1
         */
        $this->tag->setAttribute($attrName, $attrValue);
        self::assertEquals(1, count($this->tag->getAttributes()));
        self::assertEquals($attribute, $this->tag->getAttribute($attrName));
    }

    /**
     * testSetGetCustomDataAttribute
     * @throws InvalidAttributeException
     * @throws \pvc\html\err\InvalidAttributeNameException
     * @covers \pvc\html\tag\abstract\TagVoid::setCustomDataAttribute()
     */
    public function testSetGetCustomDataAttribute(): void
    {
        $name = 'foo';
        $value = 'bar';

        $valTester = $this->createMock(ValTesterInterface::class);
        $valTester->method('testValue')->willReturn(true);

        $attribute = $this->createMock(AttributeCustomDataInterface::class);
        $this->attributeFactory
            ->expects($this->once())
            ->method('makeCustomDataAttribute')
            ->with($name)
            ->willReturn($attribute);

        $attribute->expects($this->exactly(2))->method('setValue')->with($value);
        $attribute->method('getName')->willReturn($name);
        $attribute->method('getTester')->willReturn($valTester);

        /**
         * illustrates that a new attribute is created
         */
        self::assertEmpty($this->tag->getAttributes());
        $this->tag->setCustomDataAttribute($name, $value, $valTester);
        self::assertEquals(1, count($this->tag->getAttributes()));
        self::assertEquals($valTester, $this->tag->getAttribute($name)->getTester());

        /**
         * illustrates that an existing attribute is updated because the tagFactory is only called once and setValue
         * is called twice and the count of attributes remains at 1
         */
        $this->tag->setCustomDataAttribute($name, $value, $valTester);
        self::assertEquals(1, count($this->tag->getAttributes()));
        self::assertEquals($attribute, $this->tag->getAttribute($name));
    }

    /**
     * testSetGetEvent
     * @covers \pvc\html\tag\abstract\TagVoid::setEvent()
     * @covers \pvc\html\tag\abstract\TagVoid::getEvent()
     */
    public function testSetGetEvent(): void
    {
        $name = 'onclick';
        $script = 'some javascript';

        $event = $this->createMock(EventInterface::class);

        $this->attributeFactory
            ->expects($this->once())
            ->method('makeEvent')
            ->with($name)
            ->willReturn($event);

        $event->expects($this->exactly(2))->method('setValue')->with($script);
        $event->method('getName')->willReturn($name);

        /**
         * this was confusing to write because the normal flow is that setValue would be called twice.  BUT, when
         * 'makeEvent' is called, it returns a mock (because the tagFactory is a mock) and so setValue is not called
         * when the event is created and added to the attribute list.
         */
        self::assertEmpty($this->tag->getEvents());
        $this->tag->setEvent($name, $script);
        self::assertEquals(1, count($this->tag->getEvents()));
        self::assertEquals($event, $this->tag->getEvent($name));

        $this->tag->setEvent($name, $script);
    }

    /**
     * testMagicSetterGetter
     * @covers \pvc\html\tag\abstract\TagVoid::__set
     * @covers \pvc\html\tag\abstract\TagVoid::__get
     */
    public function testMagicSetterGetter(): void
    {
        $name = 'hidden';
        $value = false;

        $attribute = $this->createMock(AttributeVoidInterface::class);
        $this->attributeFactory
            ->expects($this->once())
            ->method('makeAttribute')
            ->with($name)
            ->willReturn($attribute);

        $attribute->method('getName')->willReturn($name);
        $attribute->expects($this->once())->method('setValue')->with($value);
        $attribute->expects($this->once())->method('getValue')->willReturn($value);

        $this->tag->$name = $value;
        self::assertEquals($value, $this->tag->$name);
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
        $event1 = $this->createMock(AttributeInterface::class);
        //$event1->method('getName')->willReturn($event1Name);
        //$event1->method('getValue')->willReturn($event1Script);
        $event1->method('render')->willReturn($event1Name . '=\'' . $event1Script . '\'');

        $event2Name = 'onchange';
        $event2Script = 'more javascript';
        $event2 = $this->createMock(AttributeInterface::class);
        // $event2->method('getName')->willReturn($event2Name);
        //$event2->method('getValue')->willReturn($event2Script);
        $event2->method('render')->willReturn($event2Name . '=\'' . $event2Script . '\'');

        $attr1Name = 'href';
        $attr1Value = 'bar';
        $attr1 = $this->createMock(AttributeInterface::class);
        //$attr1->method('getName')->willReturn($attr1Name);
        //$attr1->method('getValue')->willReturn($attr1Value);
        $attr1->method('render')->willReturn($attr1Name . '=\'' . $attr1Value . '\'');

        $matcher = $this->exactly(3);
        $this->attributeFactory
            ->expects($matcher)
            ->method('makeAttribute')
            ->willReturnCallback(function () use ($matcher, $event1, $event2, $attr1) {
                return match ($matcher->getInvocationCount()) {
                    1 => $event1,
                    2 => $event2,
                    3 => $attr1,
                };
            });

        $this->tag->setAttribute($event1Name, $event1Script);
        $this->tag->setAttribute($event2Name, $event2Script);
        $this->tag->setAttribute($attr1Name, $attr1Value);

        $expectedResult = "<a onclick='some script' onchange='more javascript' href='bar'>";
        self::assertEquals($expectedResult, $this->tag->generateOpeningTag());
    }
}