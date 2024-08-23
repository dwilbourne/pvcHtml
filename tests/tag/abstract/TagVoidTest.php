<?php

/**
 * @author Doug Wilbourne (dougwilbourne@gmail.com)
 */

declare(strict_types=1);

namespace pvcTests\html\tag\abstract;


use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use pvc\html\attribute\abstract\AttributeCustomData;
use pvc\html\attribute\abstract\AttributeVoid;
use pvc\html\attribute\abstract\Event;
use pvc\html\err\InvalidAttributeEventNameException;
use pvc\html\err\UnsetAttributeNameException;
use pvc\html\err\UnsetTagNameException;
use pvc\html\tag\abstract\TagVoid;
use pvc\interfaces\html\attribute\AttributeInterface;
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

    protected ContainerInterface|MockObject $attributeFactory;

    /**
     * setUp
     */
    public function setUp(): void
    {
        $this->tagName = 'a';
        $this->attributeFactory = $this->createMock(ContainerInterface::class);
        $this->tag = new TagVoid($this->attributeFactory);
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
        self::assertEquals('', $this->tag->getName());
        $this->tag->setName($this->tagName);
        self::assertEquals($this->tagName, $this->tag->getName());
    }

    /**
     * testGetAttributeReturnsNullWhenAttributeDoesNotExist
     * @covers \pvc\html\tag\abstract\TagVoid::getAttribute()
     */
    public function testGetAttributeReturnsNullWhenAttributeDoesNotExist(): void
    {
        self::assertNull($this->tag->getAttribute('href'));
    }

    /**
     * setGetAttributesReturnsEmptyArrayWhenTagHasNoAttributes
     * @covers \pvc\html\tag\abstract\TagVoid::getAttributes()
     */
    public function setGetAttributesReturnsEmptyArrayWhenTagHasNoAttributes(): void
    {
        self::assertIsArray($this->tag->getAttributes());
        self::assertEmpty($this->tag->getAttributes());
    }

    /**
     * testSetAttributeThrowsExceptionWhenAttributeNameNotSet
     * @throws UnsetAttributeNameException
     * @covers \pvc\html\tag\abstract\TagVoid::setAttribute
     */
    public function testSetAttributeThrowsExceptionWhenAttributeNameNotSet(): void
    {
        $attribute1 = $this->createMock(AttributeInterface::class);
        $attribute1->method('getName')->willReturn('');
        self::expectException(UnsetAttributeNameException::class);
        $this->tag->setAttribute($attribute1);
    }

    /**
     * testSetGetRemoveAttribute
     * @covers \pvc\html\tag\abstract\TagVoid::setAttribute
     * @covers \pvc\html\tag\abstract\TagVoid::getAttribute
     * @covers \pvc\html\tag\abstract\TagVoid::removeAttribute
     */
    public function testSetGetRemoveAttribute(): void
    {
        $attrName = 'href';

        $attribute1 = $this->createMock(AttributeInterface::class);
        $attribute1->method('getName')->willReturn($attrName);

        $attribute2 = $this->createMock(AttributeInterface::class);
        $attribute2->method('getName')->willReturn($attrName);

        self::assertNull($this->tag->getAttribute($attrName));
        $this->tag->setAttribute($attribute1);
        self::assertEquals($attribute1, $this->tag->getAttribute($attrName));

        /**
         * illustrate that you cannot have two attributes with the same name: setting the second one overwrites
         * the first
         */

        $this->tag->setAttribute($attribute2);
        $this->assertEquals(1, count($this->tag->getAttributes()));
        self::assertEquals($attribute2, $this->tag->getAttribute($attrName));

        /**
         * remove the attribute
         */
        $this->tag->removeAttribute($attrName);
        self::assertNull($this->tag->getAttribute($attrName));
        $this->assertEquals(0, count($this->tag->getAttributes()));
    }

    /**
     * testSetGetCustomDataAttribute
     * @throws \pvc\html\err\InvalidAttributeEventNameException
     * @covers \pvc\html\tag\abstract\TagVoid::setCustomData()
     */
    public function testSetGetCustomDataAttribute(): void
    {
        $name = 'foo';
        $value = 'bar';

        $valTester = $this->createMock(ValTesterInterface::class);
        $valTester->method('testValue')->willReturn(true);

        $attribute = $this->createMock(AttributeCustomData::class);
        $this->attributeFactory
            ->expects($this->once())
            ->method('get')
            ->with('customData')
            ->willReturn($attribute);

        $attribute->expects($this->exactly(2))->method('setValue')->with($value);
        $attribute->method('getName')->willReturn($name);
        $attribute->method('getTester')->willReturn($valTester);

        /**
         * illustrates that a new attribute is created
         */
        self::assertEmpty($this->tag->getAttributes());
        $this->tag->setCustomData($name, $value, $valTester);
        self::assertEquals(1, count($this->tag->getAttributes()));
        self::assertEquals($valTester, $this->tag->getAttribute($name)->getTester());

        /**
         * illustrates that an existing attribute is updated because the tagFactory is only called once and setValue
         * is called twice and the count of attributes remains at 1
         */
        $this->tag->setCustomData($name, $value, $valTester);
        self::assertEquals(1, count($this->tag->getAttributes()));
        self::assertEquals($attribute, $this->tag->getAttribute($name));
    }

    /**
     * testSetGetRemoveEvent
     * @covers \pvc\html\tag\abstract\TagVoid::getAttributes
     */
    public function testGetAttributes(): void
    {
        $attr1Name = 'href';
        $attr1 = $this->createStub(AttributeInterface::class);
        $attr1->method('getName')->willReturn($attr1Name);

        $attr2Name = 'hidden';
        $attr2 = $this->createStub(AttributeInterface::class);
        $attr2->method('getName')->willReturn($attr2Name);

        $event1Name = 'onclick';
        $event1 = $this->createStub(Event::class);
        $event1->method('getName')->willReturn($event1Name);

        $event2Name = 'ondragstart';
        $event2 = $this->createStub(Event::class);
        $event2->method('getName')->willReturn($event2Name);

        $this->tag->setAttribute($attr1);
        $this->tag->setAttribute($attr2);
        $this->tag->setAttribute($event1);
        $this->tag->setAttribute($event2);

        /**
         * default behavior is to return both attributes and events
         */
        self::assertEquals(4, count($this->tag->getAttributes()));
        self::assertEquals(4, count($this->tag->getAttributes(TagVoid::ATTRIBUTES | TagVoid::EVENTS)));

        self::assertEquals(2, count($this->tag->getAttributes(TagVoid::ATTRIBUTES)));
        self::assertEquals(2, count($this->tag->getAttributes(TagVoid::EVENTS)));

        $this->tag->removeAttribute($event2Name);
        self::assertEquals(1, count($this->tag->getAttributes(TagVoid::EVENTS)));
    }

    /**
     * testMagicSetterThrowsExceptionWithInvalidAttributeEventName
     * @covers \pvc\html\tag\abstract\TagVoid::__set
     */
    public function testMagicSetterThrowsExceptionWithInvalidAttributeEventName(): void
    {
        self::expectException(InvalidAttributeEventNameException::class);
        $this->tag->foo = 'bar';
    }

    /**
     * testMagicSetterGetter
     * @covers \pvc\html\tag\abstract\TagVoid::__get
     * @covers \pvc\html\tag\abstract\TagVoid::__set
     */
    public function testMagicSetterGetter(): void
    {
        $name = 'hidden';
        $value = true;
        $attribute = $this->createMock(AttributeVoid::class);
        $attribute->method('getName')->willReturn($name);
        $attribute->method('getValue')->willReturn($value);

        $this->attributeFactory
            ->expects($this->once())
            ->method('get')
            ->willReturn($attribute);

        /**
         * firest time is when the attribute is set, teh second is when it is updated, showing that a
         * new one is not created.
         */
        $attribute->expects($this->exactly(2))->method('setValue');
        $this->tag->$name = $value;
        self::assertEquals($value, $this->tag->$name);
        $this->tag->$name = false;
    }

    /**
     * testMagicGetterReturnsNullWithUnsetAttributeName
     * @covers \pvc\html\tag\abstract\TagVoid::__get
     */
    public function testMagicGetterThrowsExceptionWithUnsetAttributeName(): void
    {
        self::assertNull($this->tag->foo);
    }

    /**
     * testGenerateOpeningTagWithNoTagName
     * @throws UnsetTagNameException
     * @covers \pvc\html\tag\abstract\TagVoid::generateOpeningTag
     */
    public function testGenerateOpeningTagWithNoTagName(): void
    {
        self::expectException(UnsetTagNameException::class);
        $this->tag->generateOpeningTag();
    }

    /**
     * testGenerateOpeningTagWithNoAttributes
     * @covers \pvc\html\tag\abstract\TagVoid::generateOpeningTag
     */
    public function testGenerateOpeningTagWithNoAttributes(): void
    {
        $expectedResult = '<a>';
        $this->tag->setName($this->tagName);
        self::assertEquals($expectedResult, $this->tag->generateOpeningTag());
    }

    /**
     * testGenerateOpeningTagWithAttributes
     * @covers \pvc\html\tag\abstract\TagVoid::generateOpeningTag
     */
    public function testGenerateOpeningTagWithAttributes(): void
    {
        $attr1Name = 'href';
        $attr1Value = 'bar';

        $attr1 = $this->getMockBuilder(AttributeInterface::class)
                      ->getMockForAbstractClass();
        $attr1->method('getName')->willReturn($attr1Name);
        $attr1->method('getValue')->willReturn($attr1Value);
        $attr1->method('render')->willReturn($attr1Name . '=\'' . $attr1Value . '\'');

        $event1Name = 'onclick';
        $event1Value = 'some javascript';

        $event1 = $this->getMockBuilder(AttributeInterface::class)
                       ->getMockForAbstractClass();
        $event1->method('getName')->willReturn($event1Name);
        $event1->method('getValue')->willReturn($event1Value);
        $event1->method('render')->willReturn($event1Name . '=\'' . $event1Value . '\'');


        $this->tag->setName($this->tagName);
        $this->tag->setAttribute($attr1);
        $this->tag->setAttribute($event1);

        $expectedResult = "<a href='bar' onclick='some javascript'>";
        self::assertEquals($expectedResult, $this->tag->generateOpeningTag());
    }
}