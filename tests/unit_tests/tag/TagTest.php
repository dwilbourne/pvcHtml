<?php

/**
 * @author Doug Wilbourne (dougwilbourne@gmail.com)
 */

declare(strict_types=1);

namespace pvcTests\html\unit_tests\tag;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use pvc\html\err\AmbiguousMethodCallException;
use pvc\html\err\InvalidMethodCallException;
use pvc\html\err\InvalidSubTagException;
use pvc\html\tag\Tag;
use pvc\interfaces\html\attribute\AttributeInterface;
use pvc\interfaces\html\factory\HtmlFactoryInterface;
use pvc\interfaces\html\tag\TagInterface;
use pvc\interfaces\html\tag\TagVoidInterface;
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
    protected string $tagName;

    protected HtmlFactoryInterface|MockObject $factory;

    protected array $sampleAllowedSubtags = ['foo', 'bar', 'baz'];

    protected MsgInterface $testMsg;

    protected TagVoidInterface|MockObject $mockInnerTagVoid;

    protected TagInterface|MockObject $mockInnerTag;

    public function setUp(): void
    {
        $this->tagName = 'foo';
        $this->factory = $this->createMock(HtmlFactoryInterface::class);
        $this->tag = new Tag();
        $this->tag->setHtmlFactory($this->factory);
        $this->tag->setName($this->tagName);
        $this->testMsg = $this->createMock(MsgInterface::class);
        $this->mockInnerTagVoid = $this->createMock(TagVoidInterface::class);
        $this->mockInnerTag = $this->createMock(TagInterface::class);
    }

    /**
     * testSetGetAllowedSubtags
     * @throws InvalidSubTagException
     * @covers \pvc\html\tag\Tag::setAllowedSubTags
     * @covers \pvc\html\tag\Tag::getAllowedSubTags
     */
    public function testSetGetAllowedSubtags(): void
    {
        $this->tag->setAllowedSubTags($this->sampleAllowedSubtags);
        self::assertEqualsCanonicalizing($this->sampleAllowedSubtags, $this->tag->getAllowedSubTags());
    }

    /**
     * testAddSubTagObjectThrowsExceptionWhenSubTagNotAllowed
     * @throws InvalidSubTagException
     * @covers \pvc\html\tag\Tag::addSubTagObject
     * @covers \pvc\html\tag\Tag::canAddSubTag
     */
    public function testAddSubTagObjectThrowsExceptionWhenSubTagNotAllowed(): void
    {
        $subtag = $this->createMock(TagInterface::class);
        $subtag->method('getName')->willReturn('tr');
        $this->tag->setAllowedSubTags($this->sampleAllowedSubtags);

        self::expectException(InvalidSubTagException::class);

        $this->tag->addSubTagObject($subtag);
    }

    /**
     * testAddSubTagObject
     * @throws InvalidSubTagException
     * @covers \pvc\html\tag\Tag::addSubTagObject
     * @covers \pvc\html\tag\Tag::canAddSubTag
     */
    public function testAddSubTagObject(): void
    {
        $subtag = $this->createMock(TagInterface::class);
        $subtag->method('getName')->willReturn('foo');
        $this->tag->setAllowedSubTags($this->sampleAllowedSubtags);
        $this->tag->addSubTagObject($subtag);
        self::assertEquals($subtag, $this->tag->getSubTag($subtag->getName()));
    }

    /**
     * testGetSubTagReturnsNullIfSubtagDoesNotExist
     * @covers \pvc\html\tag\Tag::getSubTag
     */
    public function testGetSubTagReturnsNullIfSubtagDoesNotExist(): void
    {
        self::assertNull($this->tag->getSubTag('form'));
    }

    /**
     * testGetSubTagReturnsFirstInstanceOfSubtag
     * @throws InvalidSubTagException
     * @covers \pvc\html\tag\Tag::getSubTag
     */
    public function testGetSubTagReturnsFirstInstanceOfSubtag(): void
    {
        $tagName = 'div';

        $subtag1 = $this->createMock(TagInterface::class);
        $subtag1->method('getName')->willReturn($tagName);

        $subtag2 = $this->createMock(TagInterface::class);
        $subtag2->method('getName')->willReturn($tagName);

        $this->tag->addSubTagObject($subtag1);
        $this->tag->addSubTagObject($subtag2);

        self::assertEquals($subtag1, $this->tag->getSubTag($tagName));
    }

    /**
     * testAddSubTag
     * @throws InvalidSubTagException
     * @covers \pvc\html\tag\Tag::addSubTag
     */
    public function testAddSubTagWithFluentSetter(): void
    {
        $fooName = 'foo';
        $foo = $this->createMock(TagInterface::class);
        $foo->method('getName')->willReturn($fooName);
        $this->factory->expects($this->once())->method('makeElement')->with($fooName)->willReturn($foo);
        self::assertEquals($foo, $this->tag->addSubTag($fooName));
    }

    /**
     * testMagicCallThrowsExceptionWithAmbiguousName
     * @covers \pvc\html\tag\Tag::__call
     */
    public function testMagicCallThrowsExceptionWithAmbiguousName(): void
    {
        $fooName = 'foo';
        $this->factory->method('isAmbiguousName')->with($fooName)->willReturn(true);
        self::expectException(AmbiguousMethodCallException::class);
        $this->tag->$fooName();
    }

    /**
     * testMagicCallCreatesGetsElement
     * @covers \pvc\html\tag\Tag::__call
     */
    public function testMagicCallCreatesGetsElement(): void
    {
        $fooName = 'foo';
        $this->factory->method('isAmbiguousName')->with($fooName)->willReturn(false);
        $foo = $this->createMock(TagInterface::class);
        $foo->method('getName')->willReturn($fooName);
        $this->factory->expects($this->once())->method('canMakeElement')->with($fooName)->willReturn(true);
        $this->factory->expects($this->once())->method('makeElement')->with($fooName)->willReturn($foo);
        $this->tag->$fooName();
        self::assertEquals($foo, $this->tag->getSubTag($fooName));
    }

    /**
     * testMagicCallCreatesGetsAttributeId
     * @covers \pvc\html\tag\Tag::__call
     */
    public function testMagicCallCreatesGetsAttributeId(): void
    {
        $fooNameId = 'foo';
        $this->factory->method('isAmbiguousName')->with($fooNameId)->willReturn(false);
        $foo = $this->createMock(AttributeInterface::class);
        $foo->method('getId')->willReturn($fooNameId);
        $foo->method('getName')->willReturn($fooNameId);
        $foo->method('isGlobal')->willReturn(true);
        $this->factory->expects($this->once())->method('canMakeElement')->with($fooNameId)->willReturn(false);
        /**
         * called twice: once in Tag::__call and then again in TagVoid::makeOrGetAttribute
         */
        $this->factory->expects($this->exactly(2))->method('canMakeAttribute')->with($fooNameId)->willReturn(true);
        $this->factory->expects($this->once())->method('makeAttribute')->with($fooNameId)->willReturn($foo);
        $this->tag->$fooNameId();
        self::assertEquals($foo, $this->tag->getAttribute($fooNameId));
    }

    /**
     * testMagicCallThrowsExceptionIfNeitherAnAttributeNorATag
     * @covers \pvc\html\tag\Tag::__call
     */
    public function testMagicCallThrowsExceptionIfNeitherAnAttributeNorATag(): void
    {
        $fooName = 'foo';
        $this->factory->method('isAmbiguousName')->with($fooName)->willReturn(false);
        $this->factory->expects($this->once())->method('canMakeElement')->with($fooName)->willReturn(false);
        $this->factory->expects($this->once())->method('canMakeAttribute')->with($fooName)->willReturn(false);
        self::expectException(InvalidMethodCallException::class);
        $this->tag->$fooName();
    }

    /**
     * testAddGetInnerHtml
     * @returns Tag
     * @covers \pvc\html\tag\Tag::addMsg
     * @covers \pvc\html\tag\Tag::addText
     * @covers \pvc\html\tag\Tag::addSubTagObject
     * @covers \pvc\html\tag\Tag::getInnerHtml
     */
    public function testAddGetInnerHtml(): void
    {
        self::assertIsArray($this->tag->getInnerHtml());
        self::assertEmpty($this->tag->getInnerHtml());

        $expectedResult = [$this->testMsg];
        $this->tag->addMsg($this->testMsg);
        self::assertEquals($expectedResult, $this->tag->getInnerHtml());

        $text = 'this is some text';
        $expectedResult = [$this->testMsg, $text];
        $this->tag->addText($text);
        self::assertEquals($expectedResult, $this->tag->getInnerHtml());

        $expectedResult = [$this->testMsg, $text, $this->mockInnerTagVoid];
        $this->tag->addSubTagObject($this->mockInnerTagVoid);
        self::assertEqualsCanonicalizing($expectedResult, $this->tag->getInnerHtml());

        $expectedResult = [$this->testMsg, $text, $this->mockInnerTagVoid, $this->mockInnerTag];
        $this->tag->addSubTagObject($this->mockInnerTag);
        self::assertEqualsCanonicalizing($expectedResult, $this->tag->getInnerHtml());
    }

    /**
     * testGenerateClosingTag
     * @covers \pvc\html\tag\Tag::generateClosingTag
     */
    public function testGenerateClosingTag(): void
    {
        $expectedResult = '</' . $this->tagName . '>';
        self::assertEquals($expectedResult, $this->tag->generateClosingTag());
    }
}
