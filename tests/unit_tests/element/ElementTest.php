<?php

/**
 * @author Doug Wilbourne (dougwilbourne@gmail.com)
 */

declare(strict_types=1);

namespace pvcTests\html\unit_tests\element;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use pvc\html\element\Element;
use pvc\html\err\ChildElementNotAllowedException;
use pvc\interfaces\html\builder\HtmlBuilderInterface;
use pvc\interfaces\html\element\ElementInterface;
use pvc\interfaces\msg\MsgFactoryInterface;
use pvc\interfaces\msg\MsgInterface;

/**
 * @covers \pvc\html\element\Element
 */
class ElementTest extends TestCase
{
    /**
     * @var Element
     */
    protected Element $element;

    /**
     * @var string
     */
    protected string $elementDefId;

    protected HtmlBuilderInterface|MockObject $htmlBuilder;

    protected MsgFactoryInterface|MockObject $msgFactory;

    protected array $sampleAllowedChildDefIds = ['foo', 'bar', 'baz'];

    public function setUp(): void
    {
        $this->elementDefId = 'foo';
        $this->htmlBuilder = $this->createMock(HtmlBuilderInterface::class);
        $this->msgFactory = $this->createMock(MsgFactoryInterface::class);
        $this->element = new Element();
        $this->element->setHtmlBuilder($this->htmlBuilder);
        $this->element->setMsgFactory($this->msgFactory);
        $this->element->setName($this->elementDefId);
        $this->element->setDefId($this->elementDefId);
    }

    /**
     * testSetGetHtmlFactory
     * @covers \pvc\html\element\Element::getHtmlBuilder()
     * @covers \pvc\html\element\Element::setHtmlBuilder()
     */
    public function testSetGetHtmlFactory(): void
    {
        self::assertEquals($this->htmlBuilder, $this->element->getHtmlBuilder());
    }

    /**
     * testSetGetMsgFactory
     * @covers \pvc\html\element\Element::getMsgFactory
     * @covers \pvc\html\element\Element::setMsgFactory
     */
    public function testSetGetMsgFactory(): void
    {
        self::assertEquals($this->msgFactory, $this->element->getMsgFactory());
    }

    /**
     * testSetGetAllowedChildDefIds
     * @throws ChildElementNotAllowedException
     * @covers \pvc\html\element\Element::setAllowedChildDefIds
     * @covers \pvc\html\element\Element::getAllowedChildDefIds
     */
    public function testSetGetAllowedChildDefIds(): void
    {
        $this->element->setAllowedChildDefIds($this->sampleAllowedChildDefIds);
        self::assertEqualsCanonicalizing($this->sampleAllowedChildDefIds, $this->element->getAllowedChildDefIds());
    }

    /**
     * testIsAllowedChildDefIdReturnsTrueIfAllowedChildDefIdsIsEmpty
     * @covers \pvc\html\element\Element::isAllowedChildDefId()
     * note that it being allowed is not the same thing as being able to make it
     */
    public function testIsAllowedChildDefIdReturnsTrueIfAllowedChildDefIdsIsEmpty(): void
    {
        $defId = 'foo';
        self::assertTrue($this->element->isAllowedChildDefId($defId));
    }

    /**
     * testSetChildThrowsExceptionWhenChildIsNotAllowed
     * @throws ChildElementNotAllowedException
     * @covers \pvc\html\element\Element::setChild
     * @covers \pvc\html\element\Element::isAllowedChildDefId
     */
    public function testSetChildThrowsExceptionWhenChildIsNotAllowed(): void
    {
        $disallowedDefId = 'tr';
        $child = $this->createMock(ElementInterface::class);
        $child->method('getDefId')->willReturn($disallowedDefId);
        $this->element->setAllowedChildDefIds($this->sampleAllowedChildDefIds);
        self::expectException(ChildElementNotAllowedException::class);
        $this->element->setChild($child);
    }

    /**
     * testSetGetChild
     * @throws ChildElementNotAllowedException
     * @covers \pvc\html\element\Element::setChild
     * @covers \pvc\html\element\Element::getChild()
     * @covers \pvc\html\element\Element::isAllowedChildDefId
     */
    public function testSetGetChild(): void
    {
        $defId = $childKey = 'foo';
        $subtag = $this->createMock(ElementInterface::class);
        $subtag->method('getDefId')->willReturn($defId);
        $this->element->setAllowedChildDefIds($this->sampleAllowedChildDefIds);
        $this->element->setChild($subtag, $childKey);
        self::assertEquals($subtag, $this->element->getChild($childKey));
    }

    /**
     * testGetChildReturnsNullIfChildKeyDoesNotExist
     * @covers \pvc\html\element\Element::getChild
     */
    public function testGetChildReturnsNullIfChildKeyDoesNotExist(): void
    {
        self::assertNull($this->element->getChild('form'));
    }

    /**
     * testSetChildMakesNewChildIfPassedAStringArgument
     * @throws ChildElementNotAllowedException
     * @covers \pvc\html\element\Element::setChild()
     * @covers \pvc\html\element\Element::getChild()
     */
    public function testSetChildMakesNewChildIfPassedAStringArgument(): void
    {
        $defId = $childKey ='foo';
        $this->element->setAllowedChildDefIds([$defId]);

        $child = $this->createMock(Element::class);
        $child->method('getDefId')->willReturn($defId);
        $this->htmlBuilder->method('makeElement')->with($defId)->willReturn($child);

        $this->element->setChild($defId, $childKey);
        self::assertEquals($child, $this->element->getChild($childKey));
    }

    /**
     * testGeneratingChildKeys
     * @covers \pvc\html\element\Element::generateChildKey()
     */
    public function testGeneratingChildKeys(): void
    {
        $defId = 'foo';
        $this->element->setAllowedChildDefIds([$defId]);

        $child1 = $this->createMock(Element::class);
        $child2 = $this->createMock(Element::class);
        $child3 = $this->createMock(Element::class);

        $child1->method('getDefId')->willReturn($defId);
        $child2->method('getDefId')->willReturn($defId);
        $child3->method('getDefId')->willReturn($defId);

        $child1->method('getName')->willReturn($defId);
        $child2->method('getName')->willReturn($defId);
        $child3->method('getName')->willReturn($defId);

        $this->element->setChild($child1);
        $this->element->setChild($child2);
        $this->element->setChild($child3);

        self::assertSame($child1, $this->element->getChild('foo0'));
        self::assertSame($child2, $this->element->getChild('foo1'));
        self::assertSame($child3, $this->element->getChild('foo2'));
    }

    /**
     * testAddGetInnerHtml
     * @covers \pvc\html\element\Element::setInnerText
     * @covers \pvc\html\element\Element::getInnerText
     */
    public function testAddGetInnerHtml(): void
    {
        self::assertEmpty($this->element->getInnerText());

        $msg = $this->createMock(MsgInterface::class);
        $this->element->setInnerText($msg);
        self::assertEquals($msg, $this->element->getInnerText());

        /**
         * one overwrites the other.  If you need to intermix msg objects and text, put them inside tags / child
         * elements
         */
        $text = 'this is some text';
        $this->element->setInnerText($text);
        self::assertEquals($text, $this->element->getInnerText());
    }

    /**
     * testGenerateClosingElement
     * @covers \pvc\html\element\Element::generateClosingElement
     */
    public function testGenerateClosingElement(): void
    {
        $expectedResult = '</' . $this->elementDefId . '>';
        self::assertEquals($expectedResult, $this->element->generateClosingTag());
    }
}
