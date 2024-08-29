<?php

/**
 * @author Doug Wilbourne (dougwilbourne@gmail.com)
 */

declare(strict_types=1);

namespace pvcTests\html\tag;


use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use pvc\html\attribute\AttributeCustomData;
use pvc\html\attribute\AttributeVoid;
use pvc\html\attribute\Event;
use pvc\html\err\InvalidAttributeNameException;
use pvc\html\err\InvalidTagException;
use pvc\html\err\UnsetAttributeNameException;
use pvc\html\err\UnsetTagNameException;
use pvc\html\tag\TagVoid;
use pvc\interfaces\html\attribute\AttributeFactoryInterface;
use pvc\interfaces\html\attribute\AttributeInterface;
use pvc\interfaces\html\config\HtmlConfigInterface;
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

    protected HtmlConfigInterface|MockObject $htmlConfig;

    protected AttributeFactoryInterface|MockObject $attributeFactory;

    /**
     * setUp
     */
    public function setUp(): void
    {
        $this->tagName = 'a';
        $this->htmlConfig = $this->createMock(HtmlConfigInterface::class);
        $this->attributeFactory = $this->createMock(AttributeFactoryInterface::class);
        $this->tag = new TagVoid($this->htmlConfig, $this->attributeFactory);
    }

    /**
     * testConstruct
     * @covers \pvc\html\tag\TagVoid::__construct
     * @covers \pvc\html\tag\TagVoid::setHtmlConfig
     * @covers \pvc\html\tag\TagVoid::getHtmlConfig
     * @covers \pvc\html\tag\TagVoid::setAttributeFactory
     * @covers \pvc\html\tag\TagVoid::getAttributeFactory
     */
    public function testConstruct(): void
    {
        self::assertInstanceOf(TagVoid::class, $this->tag);
        self::assertEquals($this->htmlConfig, $this->tag->getHtmlConfig());
        self::assertEquals($this->attributeFactory, $this->tag->getAttributeFactory());
    }

    /**
     * testSetTagNameThrowsExceptionWithInvalidTagName
     * @covers \pvc\html\tag\TagVoid::setName
     */
    public function testSetTagNameThrowsExceptionWithInvalidTagName(): void
    {
        $badTagName = 'foo';
        $this->htmlConfig->method('isValidTagName')->with($badTagName)->willReturn(false);
        self::expectException(InvalidTagException::class);
        $this->tag->setName($badTagName);
    }

    /**
     * testSetGetTagName
     * @covers \pvc\html\tag\TagVoid::getName
     * @covers \pvc\html\tag\TagVoid::setName
     */
    public function testSetGetTagName(): void
    {
        /**
         * default behavior returns an empty string if name has not been sedt
         */
        self::assertEquals('', $this->tag->getName());
        $this->htmlConfig->method('isValidTagName')->with($this->tagName)->willReturn(true);
        $this->tag->setName($this->tagName);
        self::assertEquals($this->tagName, $this->tag->getName());
    }

    /**
     * testSetAllowedAttributesThrowsExceptionWithNonStringAttributeName
     * @throws InvalidAttributeNameException
     * @covers \pvc\html\tag\TagVoid::setAllowedAttributes
     */
    public function testSetAllowedAttributesThrowsExceptionWithNonStringAttributeName(): void
    {
        $allowedAttributes = ['foo', 'bar', 9];
        self::expectException(InvalidAttributeNameException::class);
        $this->tag->setAllowedAttributes($allowedAttributes);
    }

    /**
     * testSetGetAllowedAttributes
     * @covers \pvc\html\tag\TagVoid::setAllowedAttributes
     * @covers \pvc\html\tag\TagVoid::getAllowedAttributes
     */
    public function testSetGetAllowedAttributes(): void
    {
        /**
         * default is an empty array
         */
        self::assertIsArray($this->tag->getAllowedAttributes());
        self::assertEmpty($this->tag->getAllowedAttributes());

        $allowedAttributes = ['foo', 'bar', 'baz'];
        $this->tag->setAllowedAttributes($allowedAttributes);
        self::assertEqualsCanonicalizing($allowedAttributes, $this->tag->getAllowedAttributes());
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
     * testSetAttributeThrowsExceptionWhenAttributeNameNotSet
     * @throws UnsetAttributeNameException
     * @covers \pvc\html\tag\TagVoid::setAttribute
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
     * @covers \pvc\html\tag\TagVoid::setAttribute
     * @covers \pvc\html\tag\TagVoid::getAttribute
     * @covers \pvc\html\tag\TagVoid::removeAttribute
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
     * @throws InvalidAttributeNameException
     * @covers \pvc\html\tag\TagVoid::setCustomData
     */
    public function testSetGetCustomDataAttribute(): void
    {
        $name = 'foo';
        $dataName = 'data-' . $name;
        $value = 'bar';

        $valTester = $this->createMock(ValTesterInterface::class);
        $valTester->method('testValue')->willReturn(true);

        $attribute = $this->createMock(AttributeCustomData::class);

        $attribute->expects($this->exactly(2))->method('setValue')->with($value);
        $attribute->method('getName')->willReturn($dataName);
        $attribute->method('getTester')->willReturn($valTester);

        /**
         * illustrates that a new attribute is created
         */
        self::assertEmpty($this->tag->getAttributes());
        $this->attributeFactory->expects($this->once())->method('makeCustomData')->willReturn($attribute);
        $this->tag->setCustomData($name, $value, $valTester);
        self::assertEquals(1, count($this->tag->getAttributes()));
        self::assertEquals($valTester, $this->tag->getAttribute($dataName)->getTester());

        /**
         * illustrates that an existing attribute is updated because the tagFactory is only called once and setValue
         * is called twice and the count of attributes remains at 1
         */
        $this->tag->setCustomData($name, $value, $valTester);
        self::assertEquals(1, count($this->tag->getAttributes()));
        self::assertEquals($attribute, $this->tag->getAttribute($dataName));
    }

    /**
     * testSetGetRemoveEvent
     * @covers \pvc\html\tag\TagVoid::getAttributes
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
     * @covers \pvc\html\tag\TagVoid::__set
     */
    public function testMagicSetterThrowsExceptionWithInvalidAttributeEventName(): void
    {
        self::expectException(InvalidAttributeNameException::class);
        $this->tag->foo = 'bar';
    }

    /**
     * testMagicSetterGetter
     * @covers \pvc\html\tag\TagVoid::__get
     * @covers \pvc\html\tag\TagVoid::__set
     */
    public function testMagicSetterGetter(): void
    {
        $name = 'hidden';
        $value = true;
        $attribute = $this->createMock(AttributeVoid::class);
        $attribute->method('getName')->willReturn($name);
        $attribute->method('getValue')->willReturn($value);

        /**
         * first time is when the attribute is set, the second is when it is updated, showing that a
         * new one is not created.
         */
        $this->htmlConfig->method('isValidAttributeName')->with($name)->willReturn(true);
        $this->attributeFactory->expects($this->once())->method('makeAttribute')->willReturn($attribute);
        $attribute->expects($this->exactly(2))->method('setValue');

        /**
         * makes the attribute and sets the value
         */
        $this->tag->$name = $value;
        self::assertEquals($value, $this->tag->$name);

        /**
         * updates the value of an existing attribute
         */
        $newValue = false;
        $this->tag->$name = $newValue;
    }

    /**
     * testMagicGetterReturnsNullWithUnsetAttributeName
     * @covers \pvc\html\tag\TagVoid::__get
     */
    public function testMagicGetterThrowsExceptionWithUnsetAttributeName(): void
    {
        self::assertNull($this->tag->foo);
    }

    /**
     * testGenerateOpeningTagWithNoTagName
     * @throws UnsetTagNameException
     * @covers \pvc\html\tag\TagVoid::generateOpeningTag
     */
    public function testGenerateOpeningTagWithNoTagName(): void
    {
        self::expectException(UnsetTagNameException::class);
        $this->tag->generateOpeningTag();
    }

    /**
     * testGenerateOpeningTagWithNoAttributes
     * @covers \pvc\html\tag\TagVoid::generateOpeningTag
     */
    public function testGenerateOpeningTagWithNoAttributes(): void
    {
        $expectedResult = '<a>';
        $this->htmlConfig->method('isValidTagName')->with($this->tagName)->willReturn(true);
        $this->tag->setName($this->tagName);
        self::assertEquals($expectedResult, $this->tag->generateOpeningTag());
    }

    /**
     * testGenerateOpeningTagWithAttributes
     * @covers \pvc\html\tag\TagVoid::generateOpeningTag
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

        $this->htmlConfig->method('isValidTagName')->with($this->tagName)->willReturn(true);
        $this->tag->setName($this->tagName);
        $this->tag->setAttribute($attr1);
        $this->tag->setAttribute($event1);

        $expectedResult = "<a href='bar' onclick='some javascript'>";
        self::assertEquals($expectedResult, $this->tag->generateOpeningTag());
    }
}