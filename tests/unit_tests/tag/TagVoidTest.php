<?php

/**
 * @author Doug Wilbourne (dougwilbourne@gmail.com)
 */

declare(strict_types=1);

namespace pvcTests\html\unit_tests\tag;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use pvc\html\attribute\Event;
use pvc\html\err\AttributeNotAllowedException;
use pvc\html\err\InvalidAttributeIdNameException;
use pvc\html\err\UnsetAttributeNameException;
use pvc\html\err\UnsetTagNameException;
use pvc\html\tag\TagVoid;
use pvc\interfaces\html\attribute\AttributeInterface;
use pvc\interfaces\html\attribute\AttributeSingleValueInterface;
use pvc\interfaces\html\attribute\AttributeVoidInterface;
use pvc\interfaces\html\attribute\EventInterface;
use pvc\interfaces\html\factory\HtmlFactoryInterface;

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

    protected HtmlFactoryInterface|MockObject $factory;

    /**
     * setUp
     */
    public function setUp(): void
    {
        $this->tagName = 'a';
        $this->factory = $this->createMock(HtmlFactoryInterface::class);
        $this->tag = new TagVoid();
        $this->tag->setHtmlFactory($this->factory);
        $this->tag->setName($this->tagName);
    }

    /**
     * testSetGetFactory
     * @covers \pvc\html\tag\TagVoid::setHtmlFactory
     * @covers \pvc\html\tag\TagVoid::getHtmlFactory
     */
    public function testSetGetFactory(): void
    {
        self::assertEquals($this->factory, $this->tag->getHtmlFactory());
    }

    /**
     * testSetGetTagName
     * @covers \pvc\html\tag\TagVoid::getName
     * @covers \pvc\html\tag\TagVoid::setName
     */
    public function testSetGetTagName(): void
    {
        /**
         * default behavior returns an empty string if id has not been set
         */
        self::assertEquals($this->tagName, $this->tag->getName());
    }

    /**
     * testSetGetAllowedAttributes
     * @covers \pvc\html\tag\TagVoid::setAllowedAttributeIds
     * @covers \pvc\html\tag\TagVoid::getAllowedAttributeIds
     */
    public function testSetGetAllowedAttributes(): void
    {
        /**
         * default is an empty array
         */
        self::assertIsArray($this->tag->getAllowedAttributeIds());
        self::assertEmpty($this->tag->getAllowedAttributeIds());

        $allowedAttributes = ['foo', 'bar', 'baz'];
        $this->tag->setAllowedAttributeIds($allowedAttributes);
        self::assertEqualsCanonicalizing($allowedAttributes, $this->tag->getAllowedAttributeIds());
    }

    /**
     * testGetAttributeReturnsNullWhenAttributeDoesNotExist
     * @covers \pvc\html\tag\TagVoid::getAttribute
     */
    public function testGetAttributeReturnsNullWhenAttributeDoesNotExist(): void
    {
        self::assertNull($this->tag->getAttribute('href'));
    }

    /**
     * setGetAttributesReturnsEmptyArrayWhenTagHasNoAttributes
     * @covers \pvc\html\tag\TagVoid::getAttributes
     */
    public function testSetGetAttributesReturnsEmptyArrayWhenTagHasNoAttributes(): void
    {
        self::assertIsArray($this->tag->getAttributes());
        self::assertEmpty($this->tag->getAttributes());
    }

    /**
     * testSetGetRemoveAttribute
     * @covers \pvc\html\tag\TagVoid::setAttributeObject
     * @covers \pvc\html\tag\TagVoid::isAllowedAttribute
     * @covers \pvc\html\tag\TagVoid::getAttribute
     * @covers \pvc\html\tag\TagVoid::removeAttribute
     */
    public function testSetGetRemoveAttribute(): void
    {
        $attributeId = 'href';
        $attribute1 = $this->createMock(AttributeVoidInterface::class);
        $attribute1->method('getId')->willReturn($attributeId);

        $attribute2 = $this->createMock(AttributeVoidInterface::class);
        $attribute2->method('getId')->willReturn($attributeId);

        self::assertNull($this->tag->getAttribute($attributeId));

        /**
         * demonstrate fluent setter and getter
         */
        $this->tag->setAllowedAttributeIds([$attributeId]);
        self::assertEquals($this->tag, $this->tag->setAttributeObject($attribute1));
        self::assertEquals($attribute1, $this->tag->getAttribute($attributeId));

        /**
         * illustrate that you cannot have two attributes with the same id: setting the second one overwrites
         * the first
         */

        $this->tag->setAttributeObject($attribute2);
        $this->assertEquals(1, count($this->tag->getAttributes()));
        self::assertEquals($attribute2, $this->tag->getAttribute($attributeId));

        /**
         * remove the attribute
         */
        $this->tag->removeAttribute($attributeId);
        self::assertNull($this->tag->getAttribute($attributeId));
        $this->assertEquals(0, count($this->tag->getAttributes()));

        $event = $this->createMock(EventInterface::class);
        $event->method('getId')->willReturn('onchange');
        $this->tag->setAttributeObject($event);
        self::assertEquals($event, $this->tag->getAttribute($event->getId()));
    }

    /**
     * testSetAttributeFailsWithDisallowedAttributeName
     * @throws UnsetAttributeNameException
     * @covers \pvc\html\tag\TagVoid::setAttributeObject
     */
    public function testSetAttributeFailsWithDisallowedAttributeName(): void
    {
        $attrName = 'shape';
        $attribute = $this->createMock(AttributeVoidInterface::class);
        $attribute->method('getName')->willReturn($attrName);
        $attribute->method('isGlobal')->willReturn(false);
        self::expectException(AttributeNotAllowedException::class);
        $this->tag->setAttributeObject($attribute);
    }

    /**
     * testSetAttributeWithMultiValuedAttribute
     * @covers \pvc\html\tag\TagVoid::setAttribute
     */
    public function testSetAttribute(): void
    {
        $attrName = 'foo';
        $attribute = $this->createMock(AttributeInterface::class);
        $attribute->method('getName')->willReturn($attrName);
        $attribute->method('isGlobal')->willReturn(true);
        $value = ['bar', 'baz'];
        $attribute->expects($this->once())->method('setValue')->with($value);

        $this->factory->expects($this->once())->method('canMakeAttribute')->with($attrName)->willreturn(true);
        $this->factory->method('makeAttribute')->with($attrName)->willReturn($attribute);
        $this->tag->$attrName($value);
    }

    /**
     * testMakeOrGetAttributeGetsAttributesEventsIfTheyExist
     * @throws UnsetAttributeNameException
     * @throws \pvc\html\err\InvalidAttributeIdNameException
     * @covers \pvc\html\tag\TagVoid::makeOrGetAttribute
     */
    public function testMakeOrGetAttributeGetsAttributesEventsIfTheyExist(): void
    {
        $fooId = 'foo';
        $foo = $this->createMock(AttributeSingleValueInterface::class);
        $foo->method('getId')->willReturn($fooId);
        $foo->method('isGlobal')->willReturn(true);
        $this->tag->setAttributeObject($foo);

        $barId = 'bar';
        $bar = $this->createMock(Event::class);
        $bar->method('getId')->willReturn($barId);
        $this->tag->setAttributeObject($bar);

        $this->tag->setAttributeObject($foo);
        $this->tag->setAttributeObject($bar);

        self::assertEquals($foo, $this->tag->makeOrGetAttribute($fooId));
        self::assertEquals($bar, $this->tag->makeOrGetAttribute($barId));
    }

    /**
     * testMakeOrGetAttributeMakesAttributeEventIfTheyDoNotExist
     * @throws \pvc\html\err\InvalidAttributeIdNameException
     * @covers \pvc\html\tag\TagVoid::makeOrGetAttribute
     */
    public function testMakeOrGetAttributeMakesAttributeIfItDoesNotExist(): void
    {
        $fooName = 'foo';
        $foo = $this->createMock(AttributeSingleValueInterface::class);

        $this->factory->expects($this->once())->method('canMakeAttribute')->with($fooName)->willReturn(true);
        $this->factory->expects($this->once())->method('makeAttribute')->with($fooName)->willReturn($foo);

        self::assertEquals($foo, $this->tag->makeOrGetAttribute($fooName));
    }

    /**
     * testMakeOrGetAttributeMakesEventIfItDoesNotExist
     * @throws \pvc\html\err\InvalidAttributeIdNameException
     * @covers \pvc\html\tag\TagVoid::makeOrGetAttribute
     */
    public function testMakeOrGetAttributeMakesEventIfItDoesNotExist(): void
    {
        $barName = 'bar';
        $bar = $this->createMock(Event::class);

        $this->factory->expects($this->once())->method('canMakeEvent')->with($barName)->willReturn(true);
        $this->factory->expects($this->once())->method('makeEvent')->with($barName)->willReturn($bar);

        self::assertEquals($bar, $this->tag->makeOrGetAttribute($barName));
    }

    /**
     * testMakeOrGetAttributeThrowsExceptionIfDoesNotExistAndCannotMake
     * @throws \pvc\html\err\InvalidAttributeIdNameException
     * @covers \pvc\html\tag\TagVoid::makeOrGetAttribute
     */
    public function testMakeOrGetAttributeThrowsExceptionIfDoesNotExistAndCannotMake(): void
    {
        $fooName = 'foo';
        $this->factory->expects($this->once())->method('canMakeAttribute')->with($fooName)->willReturn(false);
        self::expectException(InvalidAttributeIdNameException::class);
        $this->tag->makeOrGetAttribute($fooName);
    }

    /**
     * testMagicCallMethodToSetAttribute
     * @covers \pvc\html\tag\TagVoid::__call
     * @covers \pvc\html\tag\TagVoid::makeOrGetAttribute
     * @covers \pvc\html\tag\TagVoid::setAttribute
     */
    public function testMagicCallMethodToSetAttribute(): void
    {
        $attrName = 'foo';
        $attribute = $this->createMock(AttributeSingleValueInterface::class);
        $attribute->method('getName')->willReturn($attrName);
        $attribute->method('isGlobal')->willReturn(true);
        $value = 'bar';
        $attribute->expects($this->once())->method('setValue')->with($value);

        $this->factory->expects($this->once())->method('canMakeAttribute')->with($attrName)->willreturn(true);
        $this->factory->method('makeAttribute')->with($attrName)->willReturn($attribute);
        $this->tag->$attrName($value);
    }

    /**
     * testGetAttributes
     * @covers \pvc\html\tag\TagVoid::getAttributes
     */
    public function testGetAttributes(): void
    {
        $attr1Name = 'href';
        $attr1 = $this->createStub(AttributeVoidInterface::class);
        $attr1->method('getId')->willReturn($attr1Name);

        $attr2Name = 'hidden';
        $attr2 = $this->createStub(AttributeVoidInterface::class);
        $attr2->method('getId')->willReturn($attr2Name);

        $event1Name = 'onclick';
        $event1 = $this->createStub(Event::class);
        $event1->method('getId')->willReturn($event1Name);

        $event2Name = 'ondragstart';
        $event2 = $this->createStub(Event::class);
        $event2->method('getId')->willReturn($event2Name);

        $this->tag->setAllowedAttributeIds(['href', 'hidden']);
        $this->tag->setAttributeObject($attr1);
        $this->tag->setAttributeObject($attr2);
        $this->tag->setAttributeObject($event1);
        $this->tag->setAttributeObject($event2);

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
     * testGenerateOpeningTagWithNoAttributes
     * @covers \pvc\html\tag\TagVoid::generateOpeningTag
     */
    public function testGenerateOpeningTagWithNoAttributes(): void
    {
        $expectedResult = '<a>';
        $this->tag->setName($this->tagName);
        self::assertEquals($expectedResult, $this->tag->generateOpeningTag());
    }

    /**
     * testGenerateOpeningTagWithAttributes
     * @covers \pvc\html\tag\TagVoid::generateOpeningTag
     */
    public function testGenerateOpeningTagWithAttributes(): void
    {
        $attr1NameId = 'href';
        $attr1Value = 'bar';

        $attr1 = $this->getMockBuilder(AttributeSingleValueInterface::class)
                      ->getMockForAbstractClass();
        $attr1->method('getId')->willReturn($attr1NameId);
        $attr1->method('getName')->willReturn($attr1NameId);
        $attr1->method('getValue')->willReturn($attr1Value);
        $attr1->method('render')->willReturn($attr1NameId . '=\'' . $attr1Value . '\'');

        $event1NameId = 'onclick';
        $event1Value = 'some javascript';

        $event1 = $this->getMockBuilder(EventInterface::class)
                       ->getMockForAbstractClass();
        $event1->method('getId')->willReturn($event1NameId);
        $event1->method('getName')->willReturn($event1NameId);
        $event1->method('getScript')->willReturn($event1Value);
        $event1->method('render')->willReturn($event1NameId . '=\'' . $event1Value . '\'');

        $this->tag->setName($this->tagName);

        $this->tag->setAllowedAttributeIds(['href']);
        $this->tag->setAttributeObject($attr1);
        $this->tag->setAttributeObject($event1);

        $expectedResult = "<a href='bar' onclick='some javascript'>";
        self::assertEquals($expectedResult, $this->tag->generateOpeningTag());
    }
}