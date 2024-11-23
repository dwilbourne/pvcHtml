<?php

/**
 * @author Doug Wilbourne (dougwilbourne@gmail.com)
 */

declare(strict_types=1);

namespace pvcTests\html\unit_tests\tag;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use pvc\html\err\ChildElementNotAllowedException;
use pvc\html\tag\Tag;
use pvc\interfaces\html\factory\HtmlFactoryInterface;
use pvc\interfaces\html\tag\TagInterface;
use pvc\interfaces\msg\MsgFactoryInterface;
use pvc\interfaces\msg\MsgInterface;

/**
 * @covers \pvc\html\tag\Tag
 */
class TagTest extends TestCase
{
    /**
     * @var Tag
     */
    protected Tag $tag;

    /**
     * @var string
     */
    protected string $tagDefId;

    protected HtmlFactoryInterface|MockObject $htmlFactory;

    protected MsgFactoryInterface|MockObject $msgFactory;

    protected array $sampleAllowedChildDefIds = ['foo', 'bar', 'baz'];

    public function setUp(): void
    {
        $this->tagDefId = 'foo';
        $this->htmlFactory = $this->createMock(HtmlFactoryInterface::class);
        $this->msgFactory = $this->createMock(MsgFactoryInterface::class);
        $this->tag = new Tag();
        $this->tag->setHtmlFactory($this->htmlFactory);
        $this->tag->setMsgFactory($this->msgFactory);
        $this->tag->setName($this->tagDefId);
    }

    /**
     * testSetGetHtmlFactory
     * @covers \pvc\html\tag\Tag::getHtmlFactory()
     * @covers \pvc\html\tag\Tag::setHtmlFactory()
     */
    public function testSetGetHtmlFactory(): void
    {
        self::assertEquals($this->htmlFactory, $this->tag->getHtmlFactory());
    }

    /**
     * testSetGetMsgFactory
     * @covers \pvc\html\tag\Tag::getMsgFactory
     * @covers \pvc\html\tag\Tag::setMsgFactory
     */
    public function testSetGetMsgFactory(): void
    {
        self::assertEquals($this->msgFactory, $this->tag->getMsgFactory());
    }

    /**
     * testSetGetAllowedChildDefIds
     * @throws ChildElementNotAllowedException
     * @covers \pvc\html\tag\Tag::setAllowedChildDefIds
     * @covers \pvc\html\tag\Tag::getAllowedChildDefIds
     */
    public function testSetGetAllowedChildDefIds(): void
    {
        $this->tag->setAllowedChildDefIds($this->sampleAllowedChildDefIds);
        self::assertEqualsCanonicalizing($this->sampleAllowedChildDefIds, $this->tag->getAllowedChildDefIds());
    }

    /**
     * testIsAllowedChildDefIdReturnsTrueIfAllowedChildDefIdsIsEmpty
     * @covers \pvc\html\tag\Tag::isAllowedChildDefId()
     * note that it being allowed is not the same thing as being able to make it
     */
    public function testIsAllowedChildDefIdReturnsTrueIfAllowedChildDefIdsIsEmpty(): void
    {
        $defId = 'foo';
        self::assertTrue($this->tag->isAllowedChildDefId($defId));
    }

    /**
     * testSetChildThrowsExceptionWhenChildIsNotAllowed
     * @throws ChildElementNotAllowedException
     * @covers \pvc\html\tag\Tag::setChild
     * @covers \pvc\html\tag\Tag::isAllowedChildDefId
     */
    public function testSetChildThrowsExceptionWhenChildIsNotAllowed(): void
    {
        $disallowedDefId = 'tr';
        $child = $this->createMock(TagInterface::class);
        $child->method('getDefId')->willReturn($disallowedDefId);
        $this->tag->setAllowedChildDefIds($this->sampleAllowedChildDefIds);
        self::expectException(ChildElementNotAllowedException::class);
        $this->tag->setChild($child);
    }

    /**
     * testSetGetChild
     * @throws ChildElementNotAllowedException
     * @covers \pvc\html\tag\Tag::setChild
     * @covers \pvc\html\tag\Tag::getChild()
     * @covers \pvc\html\tag\Tag::isAllowedChildDefId
     */
    public function testSetGetChild(): void
    {
        $defId = $childKey = 'foo';
        $subtag = $this->createMock(TagInterface::class);
        $subtag->method('getDefId')->willReturn($defId);
        $this->tag->setAllowedChildDefIds($this->sampleAllowedChildDefIds);
        $this->tag->setChild($subtag, $childKey);
        self::assertEquals($subtag, $this->tag->getChild($childKey));
    }

    /**
     * testGetChildReturnsNullIfChildKeyDoesNotExist
     * @covers \pvc\html\tag\Tag::getChild
     */
    public function testGetChildReturnsNullIfChildKeyDoesNotExist(): void
    {
        self::assertNull($this->tag->getChild('form'));
    }

    /**
     * testSetChildMakesNewChildIfPassedAStringArgument
     * @throws ChildElementNotAllowedException
     * @covers \pvc\html\tag\Tag::setChild()
     * @covers \pvc\html\tag\Tag::getChild()
     */
    public function testSetChildMakesNewChildIfPassedAStringArgument(): void
    {
        $defId = $childKey ='foo';
        $this->tag->setAllowedChildDefIds([$defId]);

        $child = $this->createMock(Tag::class);
        $child->method('getDefId')->willReturn($defId);
        $this->htmlFactory->method('makeElement')->with($defId)->willReturn($child);

        $this->tag->setChild($defId, $childKey);
        self::assertEquals($child, $this->tag->getChild($childKey));
    }

    /**
     * testGeneratingChildKeys
     * @covers \pvc\html\tag\Tag::generateChildKey()
     */
    public function testGeneratingChildKeys(): void
    {
        $defId = 'foo';
        $this->tag->setAllowedChildDefIds([$defId]);

        $child1 = $this->createMock(Tag::class);
        $child2 = $this->createMock(Tag::class);
        $child3 = $this->createMock(Tag::class);

        $child1->method('getDefId')->willReturn($defId);
        $child2->method('getDefId')->willReturn($defId);
        $child3->method('getDefId')->willReturn($defId);

        $child1->method('getName')->willReturn($defId);
        $child2->method('getName')->willReturn($defId);
        $child3->method('getName')->willReturn($defId);

        $this->tag->setChild($child1);
        $this->tag->setChild($child2);
        $this->tag->setChild($child3);

        self::assertSame($child1, $this->tag->getChild('foo0'));
        self::assertSame($child2, $this->tag->getChild('foo1'));
        self::assertSame($child3, $this->tag->getChild('foo2'));
    }

    /**
     * testAddGetInnerHtml
     * @covers \pvc\html\tag\Tag::setInnerText
     * @covers \pvc\html\tag\Tag::getInnerText
     */
    public function testAddGetInnerHtml(): void
    {
        self::assertEmpty($this->tag->getInnerText());

        $msg = $this->createMock(MsgInterface::class);
        $this->tag->setInnerText($msg);
        self::assertEquals($msg, $this->tag->getInnerText());

        /**
         * one overwrites the other.  If you need to intermix msg objects and text, put them inside tags / child
         * elements
         */
        $text = 'this is some text';
        $this->tag->setInnerText($text);
        self::assertEquals($text, $this->tag->getInnerText());
    }

    /**
     * testGenerateClosingTag
     * @covers \pvc\html\tag\Tag::generateClosingTag
     */
    public function testGenerateClosingTag(): void
    {
        $expectedResult = '</' . $this->tagDefId . '>';
        self::assertEquals($expectedResult, $this->tag->generateClosingTag());
    }
}
