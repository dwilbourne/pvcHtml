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
use pvc\interfaces\html\attribute\AttributeFactoryInterface;
use pvc\interfaces\html\tag\TagVoidInterface;
use pvc\interfaces\html\tag\TagInterface;
use pvc\interfaces\msg\MsgInterface;

/**
 * @covers \pvc\html\abstract\tag\Tag
 */
class TagTest extends TestCase
{
    protected AttributeFactoryInterface|MockObject $attributeFactory;
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
        $this->attributeFactory = $this->createMock(AttributeFactoryInterface::class);
        $this->tagName = 'foo';
        $this->tag = new Tag($this->attributeFactory);
        $this->tag->setName($this->tagName);
        $this->testMsg = $this->createMock(MsgInterface::class);
        $this->mockInnerTagVoid = $this->createMock(TagVoidInterface::class);
        $this->mockInnerTag = $this->createMock(TagInterface::class);
    }

    /**
     * testSetAllowedSubtagsThrowsExceptionWhenSubTagIsnotAString
     * @throws InvalidSubTagException
     * @covers \pvc\html\abstract\tag\Tag::setAllowedSubTags
     */
    public function testSetAllowedSubtagsThrowsExceptionWhenSubTagIsnotAString(): void
    {
        $sampleAllowedSubtags = ['foo', 'bar', 9];
        self::expectException(InvalidSubTagException::class);
        $this->tag->setAllowedSubTags($sampleAllowedSubtags);
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
     * testTagThrowsExceptionWhenAddingInnerTextWhereNotAllowed
     * @throws InvalidSubTagException
     * @covers \pvc\html\abstract\tag\Tag::addInnerHTML
     */

    /**
     * testAddSubTagThrowsExceptionWhenSubTagNotAllowed
     * @throws InvalidSubTagException
     * @covers \pvc\html\abstract\tag\Tag::addSubtag
     * @covers \pvc\html\abstract\tag\Tag::canAddSubTag
     */
    public function testAddSubTagThrowsExceptionWhenSubTagNotAllowed(): void
    {
        $subtag = $this->createMock(TagInterface::class);
        $subtag->method('getName')->willReturn('tr');
        $this->tag->setAllowedSubTags($this->sampleAllowedSubtags);

        self::expectException(InvalidSubTagException::class);

        $this->tag->addInnerHTML($subtag);
    }

    /**
     * testAddSubTag
     * @throws InvalidSubTagException
     * @covers \pvc\html\abstract\tag\Tag::addSubtag
     * @covers \pvc\html\abstract\tag\Tag::canAddSubTag
     */
    public function testAddSubTag(): void
    {
        $subtag = $this->createMock(TagInterface::class);
        $subtag->method('getName')->willReturn('foo');
        $this->tag->setAllowedSubTags($this->sampleAllowedSubtags);
        $this->tag->addInnerHTML($subtag);
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

        $this->tag->addInnerHTML($subtag1);
        $this->tag->addInnerHTML($subtag2);

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
        $this->tag->addInnerHTML($this->testMsg);
        self::assertEquals($expectedResult, $this->tag->getInnerHtml());

        $expectedResult = [$this->testMsg, $this->mockInnerTagVoid];
        $this->tag->addInnerHTML($this->mockInnerTagVoid);
        self::assertEqualsCanonicalizing($expectedResult, $this->tag->getInnerHtml());

        $expectedResult = [$this->testMsg, $this->mockInnerTagVoid, $this->mockInnerTag];
        $this->tag->addInnerHTML($this->mockInnerTag);
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
