<?php

/**
 * @author Doug Wilbourne (dougwilbourne@gmail.com)
 */

declare(strict_types=1);

namespace pvcTests\html\unit_tests\element;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use pvc\html\attribute\Event;
use pvc\html\element\ElementVoid;
use pvc\html\err\AttributeNotAllowedException;
use pvc\html\err\InvalidAttributeIdNameException;
use pvc\html\err\InvalidDefinitionIdException;
use pvc\interfaces\html\attribute\AttributeCustomDataInterface;
use pvc\interfaces\html\attribute\AttributeInterface;
use pvc\interfaces\html\attribute\AttributeSingleValueInterface;
use pvc\interfaces\html\attribute\AttributeVoidInterface;
use pvc\interfaces\html\attribute\EventInterface;
use pvc\interfaces\html\builder\definitions\DefinitionType;
use pvc\interfaces\html\builder\HtmlBuilderInterface;
use pvc\interfaces\validator\ValTesterInterface;

class ElementVoidTest extends TestCase
{
    /**
     * @var string
     */
    protected string $elementDefId;

    /**
     * @var ElementVoid
     */
    protected ElementVoid $element;

    protected HtmlBuilderInterface|MockObject $htmlBuilder;

    /**
     * setUp
     */
    public function setUp(): void
    {
        $this->elementDefId = 'col';
        $this->htmlBuilder = $this->createMock(HtmlBuilderInterface::class);
        $this->element = new ElementVoid();
        $this->element->setHtmlBuilder($this->htmlBuilder);
        $this->element->setDefId($this->elementDefId);
        $this->element->setName($this->elementDefId);
    }

    /**
     * testSetGetDefId
     * @covers \pvc\html\element\ElementVoid::getDefId()
     * @covers \pvc\html\element\ElementVoid::setDefId()
     */
    public function testSetGetDefId(): void
    {
        self::assertEquals($this->elementDefId, $this->element->getDefId());
    }

    /**
     * testSetGetFactory
     * @covers \pvc\html\element\ElementVoid::setHtmlBuilder
     * @covers \pvc\html\element\ElementVoid::getHtmlBuilder
     */
    public function testSetGetFactory(): void
    {
        self::assertEquals($this->htmlBuilder, $this->element->getHtmlBuilder());
    }

    /**
     * testSetGetTagName
     * @covers \pvc\html\element\ElementVoid::getName
     * @covers \pvc\html\element\ElementVoid::setName
     */
    public function testSetGetTagName(): void
    {
        /**
         * default behavior returns an empty string if id has not been set
         */
        self::assertEquals($this->elementDefId, $this->element->getName());
    }

    /**
     * testSetGetAllowedAttributes
     * @covers \pvc\html\element\ElementVoid::setAllowedAttributeDefIds
     * @covers \pvc\html\element\ElementVoid::getAllowedAttributeDefIds
     */
    public function testSetGetAllowedAttributes(): void
    {
        /**
         * default is an empty array
         */
        self::assertIsArray($this->element->getAllowedAttributeDefIds());
        self::assertEmpty($this->element->getAllowedAttributeDefIds());

        $allowedAttributeDefIds = ['foo', 'bar', 'baz'];
        $this->element->setAllowedAttributeDefIds($allowedAttributeDefIds);
        self::assertEqualsCanonicalizing($allowedAttributeDefIds, $this->element->getAllowedAttributeDefIds());
    }

    /**
     * testIsAllowedAttributeSucceedsWithEvent
     * @covers \pvc\html\element\ElementVoid::isAllowedAttribute()
     */
    public function testIsAllowedAttributeSucceedsWithEvent(): void
    {
        $defId = 'onclick';
        $event = $this->createMock(EventInterface::class);
        $event->method('getDefId')->willReturn($defId);
        $this->htmlBuilder->method('getDefinitionType')->with($defId)->willReturn(DefinitionType::Event);
        self::assertTrue($this->element->isAllowedAttribute($event));
    }

    /**
     * testIsAllowedAttributeSucceedsWithGlobalAttribute
     * @covers \pvc\html\element\ElementVoid::isAllowedAttribute()
     */
    public function testIsAllowedAttributeSucceedsWithGlobalAttribute(): void
    {
        $attribute = $this->createMock(AttributeInterface::class);
        $attribute->method('getDefId')->willreturn('class');
        self::assertTrue($this->element->isAllowedAttribute($attribute));
    }

    /**
     * testIsAllowedAttributeSucceedsIfAttributeIsAllowed
     * @covers \pvc\html\element\ElementVoid::isAllowedAttribute()
     */
    public function testIsAllowedAttributeSucceedsIfAttributeIsAllowed(): void
    {
        $defId = 'foo';
        $attribute = $this->createMock(AttributeInterface::class);
        $attribute->method('getDefId')->willReturn($defId);
        $this->element->setAllowedAttributeDefIds([$defId]);
        self::assertTrue($this->element->isAllowedAttribute($attribute));
    }

    /**
     * testIsAllowedAttributeFailsWithUnknownAttributeName
     * @covers \pvc\html\element\ElementVoid::isAllowedAttribute()
     */
    public function testIsAllowedAttributeFailsWithUnknownAttributeName(): void
    {
        $defId = 'foo';
        $this->htmlBuilder->method('getDefinitionIds')->willReturn(['bar']);
        self::assertFalse($this->element->isAllowedAttribute($defId));
    }

    /**
     * testGetAttributeReturnsNullWhenAttributeDoesNotExist
     * @covers \pvc\html\element\ElementVoid::getAttribute
     */
    public function testGetAttributeReturnsNullWhenAttributeDoesNotExist(): void
    {
        self::assertNull($this->element->getAttribute('href'));
    }

    /**
     * setGetAttributesReturnsEmptyArrayWhenTagHasNoAttributes
     * @covers \pvc\html\element\ElementVoid::getAttributes
     */
    public function testSetGetAttributesReturnsEmptyArrayWhenTagHasNoAttributes(): void
    {
        self::assertIsArray($this->element->getAttributes());
        self::assertEmpty($this->element->getAttributes());
    }

    /**
     * testSetGetRemoveAttribute
     * @covers \pvc\html\element\ElementVoid::setAttribute()
     * @covers \pvc\html\element\ElementVoid::setCustomData
     * @covers \pvc\html\element\ElementVoid::setEvent()
     * @covers \pvc\html\element\ElementVoid::isAllowedAttribute
     * @covers \pvc\html\element\ElementVoid::getAttribute
     * @covers \pvc\html\element\ElementVoid::removeAttribute
     */
    public function testSetGetRemoveAttribute(): void
    {
        $attributeDefId = 'href';
        $attribute1 = $this->createMock(AttributeVoidInterface::class);
        $attribute1->method('getDefId')->willReturn($attributeDefId);

        $attribute2 = $this->createMock(AttributeSingleValueInterface::class);
        $attribute2->method('getDefId')->willReturn($attributeDefId);

        $this->htmlBuilder->method('getDefinitionType')->with($attributeDefId)->willReturn(DefinitionType::Attribute);

        self::assertNull($this->element->getAttribute($attributeDefId));

        $this->element->setAllowedAttributeDefIds([$attributeDefId]);
        self::assertEquals($this->element, $this->element->setAttribute($attribute1));
        self::assertEquals($attribute1, $this->element->getAttribute($attributeDefId));

        /**
         * illustrate that you cannot have two attributes with the same defId: setting the second one overwrites
         * the first
         */

        $this->element->setAttribute($attribute2);
        $this->assertEquals(1, count($this->element->getAttributes()));
        self::assertEquals($attribute2, $this->element->getAttribute($attributeDefId));

        /**
         * remove the attribute
         */
        $this->element->removeAttribute($attributeDefId);
        self::assertNull($this->element->getAttribute($attributeDefId));
        $this->assertEquals(0, count($this->element->getAttributes()));

        $event = $this->createMock(EventInterface::class);
        $event->method('getDefId')->willReturn('onchange');
        $this->element->setEvent($event);
        self::assertEquals($event, $this->element->getAttribute($event->getDefId()));
    }

    /**
     * testSetAttributeMakesAttributeIfItDoesNotExist
     * @covers \pvc\html\element\ElementVoid::setAttribute
     * @covers \pvc\html\element\ElementVoid::makeOrGetAttribute()
     */
    public function testSetAttributeMakesAttributeIfItDoesNotExist(): void
    {
        $defId = 'foo';
        $attribute = $this->createMock(AttributeInterface::class);
        $attribute->method('getDefId')->willReturn($defId);
        $this->element->setAllowedAttributeDefIds([$defId]);
        $this->htmlBuilder->method('getDefinitionType')->with($defId)->willReturn(DefinitionType::Attribute);
        $this->htmlBuilder->method('makeAttribute')->with($defId)->willReturn($attribute);

        $this->element->setAttribute($defId);
        $actualResult = $this->element->getAttribute($attribute->getDefId());
        self::assertEquals($attribute, $actualResult);
    }

    /**
     * testSetAttributeThrowsExceptionIfAttributeIsNotAllowed
     * @throws AttributeNotAllowedException
     * @throws InvalidAttributeIdNameException
     * @covers \pvc\html\element\ElementVoid::setAttribute
     */
    public function testSetAttributeThrowsExceptionIfAttributeIsNotAllowed(): void
    {
        $defId = 'foo';
        $this->element->setAllowedAttributeDefIds(['bar']);
        self::expectException(AttributeNotAllowedException::class);
        $this->element->setAttribute($defId);
    }

    /**
     * testMagicCallMethodToSetAttribute
     * @covers \pvc\html\element\ElementVoid::__call
     * @covers \pvc\html\element\ElementVoid::makeOrGetAttribute
     */
    public function testMagicCallMethodToSetAttribute(): void
    {
        $defId = 'foo';
        $attribute = $this->createMock(AttributeSingleValueInterface::class);
        $attribute->method('getDefId')->willReturn($defId);
        $value = 'bar';
        $attribute->expects($this->once())->method('setValue')->with($value);
        $this->element->setAllowedAttributeDefIds([$defId]);
        $this->htmlBuilder->method('getDefinitionType')->with($defId)->willReturn(DefinitionType::Attribute);
        $this->htmlBuilder->method('makeAttribute')->with($defId)->willReturn($attribute);
        $this->element->$defId($value);
    }

    /**
     * testMakeOrGetAttributeMakesEventIfItDoesNotExist
     * @throws InvalidAttributeIdNameException
     * @covers \pvc\html\element\ElementVoid::__get
     * @covers \pvc\html\element\ElementVoid::makeOrGetAttribute
     */
    public function testMakeOrGetAttributeMakesEventIfItDoesNotExist(): void
    {
        $defId = 'bar';
        $bar = $this->createMock(Event::class);
        $this->htmlBuilder->method('getDefinitionType')->with($defId)->willReturn(DefinitionType::Event);
        $this->htmlBuilder->expects($this->once())->method('makeEvent')->with($defId)->willReturn($bar);
        $map = [
            [DefinitionType::Attribute, []],
            [DefinitionType::Event, [$defId]],
            ];
        $this->htmlBuilder->method('getDefinitionIds')->willReturnMap($map);
        self::assertEquals($bar, $this->element->$defId);
    }

    /**
     * testMakeOrGetAttributeThrowsExceptionIfDoesNotExistAndCannotMake
     * @throws InvalidAttributeIdNameException
     * @covers \pvc\html\element\ElementVoid::makeOrGetAttribute
     */
    public function testMakeOrGetAttributeThrowsExceptionIfNotAllowed(): void
    {
        $defId = 'foo';
        $this->element->setAllowedAttributeDefIds(['bar']);
        self::expectException(AttributeNotAllowedException::class);
        $this->element->$defId;
    }

    /**
     * testMakeOrGetAttributeThrowsExceptionIfDoesnotExistAndCannotMake
     * @covers \pvc\html\element\ElementVoid::makeOrGetAttribute
     */
    public function testMakeOrGetAttributeThrowsExceptionIfDoesnotExistAndCannotMake(): void
    {
        $defId = 'foo';
        $this->element->setAllowedAttributeDefIds(['foo']);
        $this->htmlBuilder->expects($this->once())->method('getDefinitionType')->with($defId)->willReturn(null);
        self::expectException(InvalidDefinitionIdException::class);
        $this->element->$defId;
    }

    /**
     * testSetCustomDataMakesAttributeIfPassedAString
     * @covers \pvc\html\element\ElementVoid::setCustomData()
     */
    public function testSetCustomDataMakesAttributeIfPassedAString(): void
    {
        $defId = 'data-foo';
        $value = 'something';
        $attribute = $this->createMock(AttributeCustomDataInterface::class);
        $attribute->method('getDefId')->willReturn($defId);
        $this->htmlBuilder
            ->expects($this->once())
            ->method('makeCustomData')
            ->with($defId)
            ->willReturn($attribute);
        $this->element->setCustomData($defId, $value);
        self::assertEquals($attribute, $this->element->$defId);
    }

    /**
     * testSetCustomDataUpdatesTesterAndValueIfPassedAnAttribute
     * @covers \pvc\html\element\ElementVoid::setCustomData()
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
        $this->element->setCustomData($attribute, $value, $valTester);
    }


    /**
     * testGetAttributes
     * @covers \pvc\html\element\ElementVoid::getAttributes
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

        $valueMap = [
            [$attr1DefId, DefinitionType::Attribute],
            [$attr2DefId, DefinitionType::Attribute],
            [$event1DefId, DefinitionType::Event],
            [$event2DefId, DefinitionType::Event],
        ];
        $this->htmlBuilder->method('getDefinitionType')->willReturnMap($valueMap);

        $this->element->setAllowedAttributeDefIds(['href', 'hidden']);
        $this->element->setAttribute($attr1);
        $this->element->setAttribute($attr2);
        $this->element->setEvent($event1);
        $this->element->setEvent($event2);

        /**
         * default behavior is to return both attributes and events
         */
        self::assertEquals(4, count($this->element->getAttributes()));
        self::assertEquals(4, count($this->element->getAttributes(ElementVoid::ATTRIBUTES | ElementVoid::EVENTS)));

        self::assertEquals(2, count($this->element->getAttributes(ElementVoid::ATTRIBUTES)));
        self::assertEquals(2, count($this->element->getAttributes(ElementVoid::EVENTS)));

        $this->element->removeAttribute($event2DefId);
        self::assertEquals(1, count($this->element->getAttributes(ElementVoid::EVENTS)));
    }


    /**
     * testGenerateOpeningTagWithNoAttributes
     * @covers \pvc\html\element\ElementVoid::generateOpeningTag
     */
    public function testGenerateOpeningTagWithNoAttributes(): void
    {
        $expectedResult = '<' . $this->elementDefId . '>';
        $this->element->setName($this->elementDefId);
        self::assertEquals($expectedResult, $this->element->generateOpeningTag());
    }

    /**
     * testGenerateOpeningTagWithAttributes
     * @covers \pvc\html\element\ElementVoid::generateOpeningTag
     */
    public function testGenerateOpeningTagWithAttributes(): void
    {
        $attr1DefId = 'href';
        $attr1Value = 'bar';

        $attr1 = $this->getMockBuilder(AttributeSingleValueInterface::class)
                      ->getMockForAbstractClass();
        $attr1->method('getDefId')->willReturn($attr1DefId);
        $attr1->method('getName')->willReturn($attr1DefId);
        $attr1->method('getValue')->willReturn($attr1Value);
        $attr1->method('render')->willReturn($attr1DefId . '=\'' . $attr1Value . '\'');

        $event1DefId = 'onclick';
        $event1Value = 'some javascript';

        $event1 = $this->getMockBuilder(EventInterface::class)
                       ->getMockForAbstractClass();
        $event1->method('getDefId')->willReturn($event1DefId);
        $event1->method('getName')->willReturn($event1DefId);
        $event1->method('getScript')->willReturn($event1Value);
        $event1->method('render')->willReturn($event1DefId . '=\'' . $event1Value . '\'');

        $valueMap = [
            [$attr1DefId, DefinitionType::Attribute],
            [$event1DefId, DefinitionType::Event],
        ];
        $this->htmlBuilder->method('getDefinitionType')->willReturnMap($valueMap);


        $this->element->setName($this->elementDefId);

        $this->element->setAllowedAttributeDefIds(['href']);
        $this->element->setAttribute($attr1);
        $this->element->setEvent($event1);

        $expectedResult = '<' . $this->elementDefId . " href='bar' onclick='some javascript'>";
        self::assertEquals($expectedResult, $this->element->generateOpeningTag());
    }
}