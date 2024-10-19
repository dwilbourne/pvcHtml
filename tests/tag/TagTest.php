<?php

/**
 * @author Doug Wilbourne (dougwilbourne@gmail.com)
 */

declare(strict_types=1);

namespace pvcTests\html\abstract\tag;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use pvc\html\abstract\err\InvalidSubTagException;
use pvc\html\abstract\tag\Tag;
use pvc\interfaces\html\tag\TagVoidInterface;
use pvc\interfaces\html\tag\TagInterface;
use pvc\interfaces\msg\MsgInterface;

/**
 * @covers \pvc\html\abstract\tag\Tag
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

    protected array $sampleAllowedSubtags = ['foo', 'bar', 'baz'];

    protected MsgInterface $testMsg;

    protected TagVoidInterface|MockObject $mockInnerTagVoid;

    protected TagInterface|MockObject $mockInnerTag;

    public function setUp(): void
    {
        $this->tagName = 'foo';
        $this->tag = new Tag();
        $this->tag->setName($this->tagName);
        $this->testMsg = $this->createMock(MsgInterface::class);
        $this->mockInnerTagVoid = $this->createMock(TagVoidInterface::class);
        $this->mockInnerTag = $this->createMock(TagInterface::class);
    }

    /**
     * testSetGetAllowedSubtags
     * @throws InvalidSubTagException
     * @covers \pvc\html\abstract\tag\Tag::setAllowedSubTags
     * @covers \pvc\html\abstract\tag\Tag::getAllowedSubTags
     */
    public function testSetGetAllowedSubtags(): void
    {
        $this->tag->setAllowedSubTags($this->sampleAllowedSubtags);
        self::assertEqualsCanonicalizing($this->sampleAllowedSubtags, $this->tag->getAllowedSubTags());
    }

    /**
     * testAddSubTagThrowsExceptionWhenSubTagNotAllowed
     * @throws InvalidSubTagException
     * @covers \pvc\html\abstract\tag\Tag::addSubTagObject
     * @covers \pvc\html\abstract\tag\Tag::canAddSubTag
     */
    public function testAddSubTagThrowsExceptionWhenSubTagNotAllowed(): void
    {
        $subtag = $this->createMock(TagInterface::class);
        $subtag->method('getName')->willReturn('tr');
        $this->tag->setAllowedSubTags($this->sampleAllowedSubtags);

        self::expectException(InvalidSubTagException::class);

        $this->tag->addSubTagObject($subtag);
    }

    /**
     * testAddSubTag
     * @throws InvalidSubTagException
     * @covers \pvc\html\abstract\tag\Tag::addSubTagObject
     * @covers \pvc\html\abstract\tag\Tag::canAddSubTag
     */
    public function testAddSubTag(): void
    {
        $subtag = $this->createMock(TagInterface::class);
        $subtag->method('getName')->willReturn('foo');
        $this->tag->setAllowedSubTags($this->sampleAllowedSubtags);
        $this->tag->addSubTagObject($subtag);
        self::assertEquals($subtag, $this->tag->getSubTag($subtag->getName()));
    }

    /**
     * testGetSubTagReturnsNullIfSubtagDoesNotExist
     * @covers \pvc\html\abstract\tag\Tag::getSubTag
     */
    public function testGetSubTagReturnsNullIfSubtagDoesNotExist(): void
    {
        self::assertNull($this->tag->getSubTag('form'));
    }

    /**
     * testGetSubTagReturnsFirstInstanceOfSubtag
     * @throws InvalidSubTagException
     * @covers \pvc\html\abstract\tag\Tag::getSubTag
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
     * testAddGetInnerHtml
     * @returns Tag
     * @covers \pvc\html\abstract\tag\Tag::addInnerHTML
     * @covers \pvc\html\abstract\tag\Tag::getInnerHtml
     */
    public function testAddGetInnerHtml(): void
    {
        self::assertIsArray($this->tag->getInnerHtml());
        self::assertEmpty($this->tag->getInnerHtml());

        $expectedResult = [$this->testMsg];
        $this->tag->addMsg($this->testMsg);
        self::assertEquals($expectedResult, $this->tag->getInnerHtml());

        $expectedResult = [$this->testMsg, $this->mockInnerTagVoid];
        $this->tag->addSubTagObject($this->mockInnerTagVoid);
        self::assertEqualsCanonicalizing($expectedResult, $this->tag->getInnerHtml());

        $expectedResult = [$this->testMsg, $this->mockInnerTagVoid, $this->mockInnerTag];
        $this->tag->addSubTagObject($this->mockInnerTag);
        self::assertEqualsCanonicalizing($expectedResult, $this->tag->getInnerHtml());
    }

    /**
     * testGenerateClosingTag
     * @covers \pvc\html\abstract\tag\Tag::generateClosingTag
     */
    public function testGenerateClosingTag(): void
    {
        $expectedResult = '</' . $this->tagName . '>';
        self::assertEquals($expectedResult, $this->tag->generateClosingTag());
    }
}
