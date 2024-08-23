<?php

/**
 * @author Doug Wilbourne (dougwilbourne@gmail.com)
 */

declare(strict_types=1);

namespace pvcTests\html\tag\abstract;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use pvc\html\err\InnerTextNotAllowedException;
use pvc\html\err\InvalidSubTagException;
use pvc\html\tag\abstract\Tag;
use pvc\interfaces\html\tag\TagInterface;
use pvc\interfaces\html\tag\TagVoidInterface;
use pvc\interfaces\msg\MsgInterface;

/**
 * @covers \pvc\html\tag\abstract\Tag
 */
class TagTest extends TestCase
{

    protected ContainerInterface $attributeFactory;
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
        $this->attributeFactory = $this->createMock(ContainerInterface::class);
        $this->tagName = 'div';
        $this->tag = new Tag($this->attributeFactory);
        $this->tag->setName($this->tagName);
        $this->testMsg = $this->createMock(MsgInterface::class);
        $this->mockInnerTagVoid = $this->createMock(TagVoidInterface::class);
        $this->mockInnerTag = $this->createMock(TagInterface::class);
    }

    /**
     * testTagThrowsExceptionWhenAddingInnerTextWhereNotAllowed
     * @throws \pvc\html\err\InvalidSubTagException
     * @covers \pvc\html\tag\abstract\Tag::addInnerHTML
     */
    public function testTagThrowsExceptionWhenAddingInnerTextWhereNotAllowed(): void
    {
        self::expectException(InnerTextNotAllowedException::class);
        $this->tag->setName('html');
        $this->tag->addInnerHTML('some text');
    }

    /**
     * testAddSubTagThrowsExceptionWhenSubTagNotAllowed
     * @throws InvalidSubTagException
     * @covers \pvc\html\tag\abstract\Tag::addSubtag
     * @covers \pvc\html\tag\abstract\Tag::canAddSubTag
     */
    public function testAddSubTagThrowsExceptionWhenSubTagNotAllowed(): void
    {
        $subtag = $this->createMock(TagInterface::class);
        $subtag->method('getName')->willReturn('tr');

        self::expectException(InvalidSubTagException::class);
        $this->tag->setName('html');
        $this->tag->addInnerHTML($subtag);
    }

    /**
     * testCannotAddBlockTagAsSubTagOfInlineTag
     * @throws InvalidSubTagException
     * @covers \pvc\html\tag\abstract\Tag::canAddSubTag
     */
    public function testCannotAddBlockTagAsSubTagOfInlineTag(): void
    {
        $this->tag->setName('span');
        $subtag = $this->createMock(TagInterface::class);
        $subtag->method('getName')->willReturn('div');

        self::expectException(InvalidSubTagException::class);
        $this->tag->addInnerHTML($subtag);
    }

    /**
     * testGetSubTagReturnsNullIfSubtagDoesNotExist
     * @covers \pvc\html\tag\abstract\Tag::getSubTag
     */
    public function testGetSubTagReturnsNullIfSubtagDoesNotExist(): void
    {
        $this->tag->setName('div');
        self::assertNull($this->tag->getSubTag('form'));
    }

    /**
     * testGetSubTagReturnsFirstInstanceOfSubtag
     * @throws InvalidSubTagException
     * @covers \pvc\html\tag\abstract\Tag::getSubTag
     */
    public function testGetSubTagReturnsFirstInstanceOfSubtag(): void
    {
        $tagName = 'div';
        $this->tag->setName('div');

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
     * @returns \pvc\html\tag\abstract\Tag
     * @covers \pvc\html\tag\abstract\Tag::addInnerHTML
     * @covers \pvc\html\tag\abstract\Tag::getInnerHtml
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
     * @covers \pvc\html\tag\abstract\Tag::generateClosingTag
     */
    public function testGenerateClosingTag(): void
    {
        $expectedResult = '</div>';
        self::assertEquals($expectedResult, $this->tag->generateClosingTag());
    }

}