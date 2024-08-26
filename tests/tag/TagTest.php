<?php

/**
 * @author Doug Wilbourne (dougwilbourne@gmail.com)
 */

declare(strict_types=1);

namespace pvcTests\html\tag;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use pvc\html\err\InvalidInnerTextException;
use pvc\html\err\InvalidSubTagException;
use pvc\html\tag\Tag;
use pvc\interfaces\html\attribute\AttributeFactoryInterface;
use pvc\interfaces\html\config\HtmlConfigInterface;
use pvc\interfaces\html\tag\TagInterface;
use pvc\interfaces\html\tag\TagVoidInterface;
use pvc\interfaces\msg\MsgInterface;

/**
 * @covers Tag
 */
class TagTest extends TestCase
{

    protected HtmlConfigInterface|MockObject $htmlConfig;

    protected AttributeFactoryInterface|MockObject $attributeFactory;
    /**
     * @var Tag
     */
    protected Tag $tag;

    /**
     * @var string
     */
    protected string $tagName;

    protected MsgInterface $testMsg;

    protected TagVoidInterface|MockObject $mockInnerTagVoid;

    protected TagInterface|MockObject $mockInnerTag;

    public function setUp(): void
    {
        $this->htmlConfig = $this->createMock(HtmlConfigInterface::class);
        $this->attributeFactory = $this->createMock(AttributeFactoryInterface::class);
        $this->tagName = 'foo';
        $this->htmlConfig->method('isValidTagName')->with($this->tagName)->willReturn(true);
        $this->tag = new Tag($this->htmlConfig, $this->attributeFactory);
        $this->tag->setName($this->tagName);
        $this->testMsg = $this->createMock(MsgInterface::class);
        $this->mockInnerTagVoid = $this->createMock(TagVoidInterface::class);
        $this->mockInnerTag = $this->createMock(TagInterface::class);
    }

    /**
     * testTagThrowsExceptionWhenAddingInnerTextWhereNotAllowed
     * @throws InvalidSubTagException
     * @covers Tag::addInnerHTML
     */
    public function testTagThrowsExceptionWhenAddingInnerTextWhereNotAllowed(): void
    {
        self::expectException(InvalidInnerTextException::class);
        $this->tag->setName($this->tagName);
        $this->htmlConfig->method('innerTextNotAllowed')->willReturn(true);
        $this->tag->addInnerHTML('some text');
    }

    /**
     * testAddSubTagThrowsExceptionWhenSubTagNotAllowed
     * @throws InvalidSubTagException
     * @covers Tag::addSubtag
     * @covers Tag::canAddSubTag
     */
    public function testAddSubTagThrowsExceptionWhenSubTagNotAllowed(): void
    {
        $subtag = $this->createMock(TagInterface::class);
        $subtag->method('getName')->willReturn('tr');

        $this->htmlConfig
            ->method('isValidSubTag')
            ->with($subtag->getName(), $this->tag->getName())
            ->willReturn(false);

        self::expectException(InvalidSubTagException::class);

        $this->tag->addInnerHTML($subtag);
    }

    /**
     * testCannotAddBlockTagAsSubTagOfInlineTag
     * @throws InvalidSubTagException
     * @covers Tag::canAddSubTag
     */
    public function testCannotAddBlockTagAsSubTagOfInlineTag(): void
    {
        $this->htmlConfig->method('isInlineTag')->with($this->tag->getName())->willReturn(true);

        $subtag = $this->createMock(TagInterface::class);
        $subTagName = 'bar';
        $subtag->method('getName')->willReturn($subTagName);
        $this->htmlConfig->method('isValidSubTag')->with($subTagName, $this->tag->getName())->willReturn(true);
        $this->htmlConfig->method('isBlockTag')->with($subTagName)->willReturn(true);

        self::expectException(InvalidSubTagException::class);

        $this->tag->addInnerHTML($subtag);
    }

    /**
     * testGetSubTagReturnsNullIfSubtagDoesNotExist
     * @covers Tag::getSubTag
     */
    public function testGetSubTagReturnsNullIfSubtagDoesNotExist(): void
    {
        self::assertNull($this->tag->getSubTag('form'));
    }

    /**
     * testGetSubTagReturnsFirstInstanceOfSubtag
     * @throws InvalidSubTagException
     * @covers Tag::getSubTag
     */
    public function testGetSubTagReturnsFirstInstanceOfSubtag(): void
    {
        $tagName = 'div';

        $this->htmlConfig->method('isValidSubTag')->with($tagName)->willReturn(true);
        $this->htmlConfig->method('isBlockTag')->with($tagName)->willReturn(false);

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
     * @covers Tag::addInnerHTML
     * @covers Tag::getInnerHtml
     */
    public function testAddGetInnerHtml(): void
    {
        self::assertIsArray($this->tag->getInnerHtml());
        self::assertEmpty($this->tag->getInnerHtml());

        $expectedResult = [$this->testMsg];
        $this->tag->addInnerHTML($this->testMsg);
        self::assertEquals($expectedResult, $this->tag->getInnerHtml());

        $expectedResult = [$this->testMsg, $this->mockInnerTagVoid];
        $this->htmlConfig->method('isValidSubTag')->with($this->mockInnerTag->getName())->willReturn(true);
        $this->tag->addInnerHTML($this->mockInnerTagVoid);
        self::assertEqualsCanonicalizing($expectedResult, $this->tag->getInnerHtml());

        $expectedResult = [$this->testMsg, $this->mockInnerTagVoid, $this->mockInnerTag];
        $this->tag->addInnerHTML($this->mockInnerTag);
        self::assertEqualsCanonicalizing($expectedResult, $this->tag->getInnerHtml());
    }

    /**
     * testGenerateClosingTag
     * @covers Tag::generateClosingTag
     */
    public function testGenerateClosingTag(): void
    {
        $expectedResult = '</' . $this->tagName . '>';
        self::assertEquals($expectedResult, $this->tag->generateClosingTag());
    }
}
