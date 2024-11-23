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
use pvc\html\err\InvalidDefinitionIdException;
use pvc\html\err\UnsetAttributeNameException;
use pvc\html\err\UnsetTagNameException;
use pvc\html\tag\TagVoid;
use pvc\interfaces\html\attribute\AttributeCustomDataInterface;
use pvc\interfaces\html\attribute\AttributeInterface;
use pvc\interfaces\html\attribute\AttributeSingleValueInterface;
use pvc\interfaces\html\attribute\AttributeVoidInterface;
use pvc\interfaces\html\attribute\EventInterface;
use pvc\interfaces\html\factory\definitions\DefinitionType;
use pvc\interfaces\html\factory\HtmlFactoryInterface;
use pvc\interfaces\validator\ValTesterInterface;

class TagVoidTest extends TestCase
{
    /**
     * @var string
     */
    protected string $tagDefId;

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
        $this->tagDefId = 'a';
        $this->factory = $this->createMock(HtmlFactoryInterface::class);
        $this->tag = new TagVoid();
        $this->tag->setHtmlFactory($this->factory);
        $this->tag->setDefId($this->tagDefId);
        $this->tag->setName($this->tagDefId);
    }

    /**
     * testSetGetDefId
     * @covers \pvc\html\tag\TagVoid::getDefId()
     * @covers \pvc\html\tag\TagVoid::setDefId()
     */
    public function testSetGetDefId(): void
    {
        self::assertEquals($this->tagDefId, $this->tag->getDefId());
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
        self::assertEquals($this->tagDefId, $this->tag->getName());
    }

    /**
     * testSetGetAllowedAttributes
     * @covers \pvc\html\tag\TagVoid::setAllowedAttributeDefIds
     * @covers \pvc\html\tag\TagVoid::getAllowedAttributeDefIds
     */
    public function testSetGetAllowedAttributes(): void
    {
        /**
         * default is an empty array
         */
        self::assertIsArray($this->tag->getAllowedAttributeDefIds());
        self::assertEmpty($this->tag->getAllowedAttributeDefIds());

        $allowedAttributeDefIds = ['foo', 'bar', 'baz'];
        $this->tag->setAllowedAttributeDefIds($allowedAttributeDefIds);
        self::assertEqualsCanonicalizing($allowedAttributeDefIds, $this->tag->getAllowedAttributeDefIds());
    }

    /**
     * testIsAllowedAttributeSucceedsWithEvent
     * @covers \pvc\html\tag\TagVoid::isAllowedAttribute()
     */
    public function testIsAllowedAttributeSucceedsWithEvent(): void
    {
        $event = $this->createMock(EventInterface::class);
        self::assertTrue($this->tag->isAllowedAttribute($event));
    }

    /**
     * testIsAllowedAttributeSucceedsWithGlobalAttribute
     * @covers \pvc\html\tag\TagVoid::isAllowedAttribute()
     */
    public function testIsAllowedAttributeSucceedsWithGlobalAttribute(): void
    {
        $event = $this->createMock(EventInterface::class);
        $event->method('isGlobal')->willreturn(true);
        self::assertTrue($this->tag->isAllowedAttribute($event));
    }

    /**
     * testIsAllowedAttributeSucceedsIfAttributeIsAllowed
     * @covers \pvc\html\tag\TagVoid::isAllowedAttribute()
     */
    public function testIsAllowedAttributeSucceedsIfAttributeIsAllowed(): void
    {
        $defId = 'foo';
        $attribute = $this->createMock(AttributeInterface::class);
        $attribute->method('getDefId')->willReturn($defId);
        $attribute->method('isGlobal')->willReturn(false);
        $this->tag->setAllowedAttributeDefIds([$defId]);
        self::assertTrue($this->tag->isAllowedAttribute($attribute));
    }

    /**
     * testIsAllowedAttributeSucceedsWithEventName
     * @covers \pvc\html\tag\TagVoid::isAllowedAttribute()
     */
    public function testIsAllowedAttributeSucceedsWithEventName(): void
    {
        $defId = 'foo';
        $this->factory->method('getDefinitionIds')->willReturn([$defId]);
        self::assertTrue($this->tag->isAllowedAttribute($defId));
    }

    /**
     * testIsAllowedAttributeFailsWithUnknownAttributeName
     * @covers \pvc\html\tag\TagVoid::isAllowedAttribute()
     */
    public function testIsAllowedAttributeFailsWithUnknownAttributeName(): void
    {
        $defId = 'foo';
        $this->factory->method('getDefinitionIds')->willReturn(['bar']);
        self::assertFalse($this->tag->isAllowedAttribute($defId));
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
     * @covers \pvc\html\tag\TagVoid::setAttribute()
     * @covers \pvc\html\tag\TagVoid::setCustomData
     * @covers \pvc\html\tag\TagVoid::setEvent()
     * @covers \pvc\html\tag\TagVoid::isAllowedAttribute
     * @covers \pvc\html\tag\TagVoid::getAttribute
     * @covers \pvc\html\tag\TagVoid::removeAttribute
     */
    public function testSetGetRemoveAttribute(): void
    {
        $attributeDefId = 'href';
        $attribute1 = $this->createMock(AttributeVoidInterface::class);
        $attribute1->method('getDefId')->willReturn($attributeDefId);

        $attribute2 = $this->createMock(AttributeSingleValueInterface::class);
        $attribute2->method('getDefId')->willReturn($attributeDefId);

        self::assertNull($this->tag->getAttribute($attributeDefId));

        $this->tag->setAllowedAttributeDefIds([$attributeDefId]);
        self::assertEquals($this->tag, $this->tag->setAttribute($attribute1));
        self::assertEquals($attribute1, $this->tag->getAttribute($attributeDefId));

        /**
         * illustrate that you cannot have two attributes with the same defId: setting the second one overwrites
         * the first
         */

        $this->tag->setAttribute($attribute2);
        $this->assertEquals(1, count($this->tag->getAttributes()));
        self::assertEquals($attribute2, $this->tag->getAttribute($attributeDefId));

        /**
         * remove the attribute
         */
        $this->tag->removeAttribute($attributeDefId);
        self::assertNull($this->tag->getAttribute($attributeDefId));
        $this->assertEquals(0, count($this->tag->getAttributes()));

        $event = $this->createMock(EventInterface::class);
        $event->method('getDefId')->willReturn('onchange');
        $this->tag->setEvent($event);
        self::assertEquals($event, $this->tag->getAttribute($event->getDefId()));
    }

    /**
     * testSetAttributeMakesAttributeIfItDoesNotExist
     * @covers \pvc\html\tag\TagVoid::setAttribute
     * @covers \pvc\html\tag\TagVoid::makeOrGetAttribute()
     */
    public function testSetAttributeMakesAttributeIfItDoesNotExist(): void
    {
        $defId = 'foo';
        $attribute = $this->createMock(AttributeInterface::class);
        $attribute->method('getDefId')->willReturn($defId);
        $this->tag->setAllowedAttributeDefIds([$defId]);
        $this->factory->method('getDefinitionType')->with($defId)->willReturn('Attribute');
        $this->factory->method('makeAttribute')->with($defId)->willReturn($attribute);

        $this->tag->setAttribute($defId);
        self::assertEquals($attribute, $this->tag->getAttribute($attribute->getDefId()));
    }

    /**
     * testSetAttributeThrowsExceptionIfAttributeIsNotAllowed
     * @throws AttributeNotAllowedException
     * @throws InvalidAttributeIdNameException
     * @throws UnsetAttributeNameException
     * @covers \pvc\html\tag\TagVoid::setAttribute
     */
    public function testSetAttributeThrowsExceptionIfAttributeIsNotAllowed(): void
    {
        $defId = 'foo';
        $this->tag->setAllowedAttributeDefIds(['bar']);
        self::expectException(AttributeNotAllowedException::class);
        $this->tag->setAttribute($defId);
    }

    /**
     * testMagicCallMethodToSetAttribute
     * @covers \pvc\html\tag\TagVoid::__call
     * @covers \pvc\html\tag\TagVoid::makeOrGetAttribute
     */
    public function testMagicCallMethodToSetAttribute(): void
    {
        $defId = 'foo';
        $attribute = $this->createMock(AttributeSingleValueInterface::class);
        $attribute->method('getDefId')->willReturn($defId);
        $value = 'bar';
        $attribute->expects($this->once())->method('setValue')->with($value);
        $this->tag->setAllowedAttributeDefIds([$defId]);
        $this->factory->method('getDefinitionType')->with($defId)->willReturn('Attribute');
        $this->factory->method('makeAttribute')->with($defId)->willReturn($attribute);
        $this->tag->$defId($value);
    }

    /**
     * testMakeOrGetAttributeMakesEventIfItDoesNotExist
     * @throws \pvc\html\err\InvalidAttributeIdNameException
     * @covers \pvc\html\tag\TagVoid::__get
     * @covers \pvc\html\tag\TagVoid::makeOrGetAttribute
     */
    public function testMakeOrGetAttributeMakesEventIfItDoesNotExist(): void
    {
        $defId = 'bar';
        $bar = $this->createMock(Event::class);
        $this->factory->method('getDefinitionType')->with($defId)->willReturn('Event');
        $this->factory->expects($this->once())->method('makeEvent')->with($defId)->willReturn($bar);
        $map = [
            [DefinitionType::Attribute, []],
            [DefinitionType::Event, [$defId]],
            ];
        $this->factory->method('getDefinitionIds')->willReturnMap($map);
        self::assertEquals($bar, $this->tag->$defId);
    }

    /**
     * testMakeOrGetAttributeThrowsExceptionIfDoesNotExistAndCannotMake
     * @throws \pvc\html\err\InvalidAttributeIdNameException
     * @covers \pvc\html\tag\TagVoid::makeOrGetAttribute
     */
    public function testMakeOrGetAttributeThrowsExceptionIfNotAllowed(): void
    {
        $defId = 'foo';
        $this->tag->setAllowedAttributeDefIds(['bar']);
        self::expectException(AttributeNotAllowedException::class);
        $this->tag->$defId;
    }

    /**
     * testMakeOrGetAttributeThrowsExceptionIfDoesnotExistAndCannotMake
     * @covers \pvc\html\tag\TagVoid::makeOrGetAttribute
     */
    public function testMakeOrGetAttributeThrowsExceptionIfDoesnotExistAndCannotMake(): void
    {
        $defId = 'foo';
        $this->tag->setAllowedAttributeDefIds(['foo']);
        $this->factory->expects($this->once())->method('getDefinitionType')->with($defId)->willReturn(null);
        self::expectException(InvalidDefinitionIdException::class);
        $this->tag->$defId;
    }

    /**
     * testSetCustomDataMakesAttributeIfPassedAString
     * @covers \pvc\html\tag\TagVoid::setCustomData()
     */
    public function testSetCustomDataMakesAttributeIfPassedAString(): void
    {
        $defId = 'data-foo';
        $value = 'something';
        $attribute = $this->createMock(AttributeCustomDataInterface::class);
        $attribute->method('getDefId')->willReturn($defId);
        $this->factory
            ->expects($this->once())
            ->method('makeCustomData')
            ->with($defId)
            ->willReturn($attribute);
        $this->tag->setCustomData($defId, $value);
        self::assertEquals($attribute, $this->tag->$defId);
    }

    /**
     * testSetCustomDataUpdatesTesterAndValueIfPassedAnAttribute
     * @covers \pvc\html\tag\TagVoid::setCustomData()
     */
    public function testSetCustomDataUpdatesTesterAndValueIfPassedAnAttribute(): void
    {
        $defId = 'data-foo';
        $value = 'something';
        $valTester = $this->createMock(ValTesterInterface::class);
        $attribute = $this->createMock(AttributeCustomDataInterface::class);
        $attribute->method('getDefId')->willReturn($defId);
        $attribute->expects($this->once())->method('setTester')->with($valTester);
        $attribute->expects($this->once())->method('setValue')->with($value);
        $this->tag->setCustomData($attribute, $value, $valTester);
    }


    /**
     * testGetAttributes
     * @covers \pvc\html\tag\TagVoid::getAttributes
     */
    public function testGetAttributes(): void
    {
        $attr1DefId = 'href';
        $attr1 = $this->createStub(AttributeVoidInterface::class);
        $attr1->method('getDefId')->willReturn($attr1DefId);

        $attr2DefId = 'hidden';
        $attr2 = $this->createStub(AttributeVoidInterface::class);
        $attr2->method('getDefId')->willReturn($attr2DefId);

        $event1DefId = 'onclick';
        $event1 = $this->createStub(Event::class);
        $event1->method('getDefId')->willReturn($event1DefId);

        $event2DefId = 'ondragstart';
        $event2 = $this->createStub(Event::class);
        $event2->method('getDefId')->willReturn($event2DefId);

        $this->tag->setAllowedAttributeDefIds(['href', 'hidden']);
        $this->tag->setAttribute($attr1);
        $this->tag->setAttribute($attr2);
        $this->tag->setEvent($event1);
        $this->tag->setEvent($event2);

        /**
         * default behavior is to return both attributes and events
         */
        self::assertEquals(4, count($this->tag->getAttributes()));
        self::assertEquals(4, count($this->tag->getAttributes(TagVoid::ATTRIBUTES | TagVoid::EVENTS)));

        self::assertEquals(2, count($this->tag->getAttributes(TagVoid::ATTRIBUTES)));
        self::assertEquals(2, count($this->tag->getAttributes(TagVoid::EVENTS)));

        $this->tag->removeAttribute($event2DefId);
        self::assertEquals(1, count($this->tag->getAttributes(TagVoid::EVENTS)));
    }


    /**
     * testGenerateOpeningTagWithNoAttributes
     * @covers \pvc\html\tag\TagVoid::generateOpeningTag
     */
    public function testGenerateOpeningTagWithNoAttributes(): void
    {
        $expectedResult = '<a>';
        $this->tag->setName($this->tagDefId);
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
        $attr1->method('getDefId')->willReturn($attr1NameId);
        $attr1->method('getName')->willReturn($attr1NameId);
        $attr1->method('getValue')->willReturn($attr1Value);
        $attr1->method('render')->willReturn($attr1NameId . '=\'' . $attr1Value . '\'');

        $event1NameId = 'onclick';
        $event1Value = 'some javascript';

        $event1 = $this->getMockBuilder(EventInterface::class)
                       ->getMockForAbstractClass();
        $event1->method('getDefId')->willReturn($event1NameId);
        $event1->method('getName')->willReturn($event1NameId);
        $event1->method('getScript')->willReturn($event1Value);
        $event1->method('render')->willReturn($event1NameId . '=\'' . $event1Value . '\'');

        $this->tag->setName($this->tagDefId);

        $this->tag->setAllowedAttributeDefIds(['href']);
        $this->tag->setAttribute($attr1);
        $this->tag->setEvent($event1);

        $expectedResult = "<a href='bar' onclick='some javascript'>";
        self::assertEquals($expectedResult, $this->tag->generateOpeningTag());
    }
}