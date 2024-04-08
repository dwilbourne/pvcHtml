<?php

/**
 * @author Doug Wilbourne (dougwilbourne@gmail.com)
 */

declare(strict_types=1);

namespace pvcTests\html\tag\abstract;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use pvc\html\attribute\factory\AttributeFactory;
use pvc\html\tag\abstract\Tag;
use pvc\interfaces\html\tag\TagInterface;
use pvc\interfaces\html\tag\TagVoidInterface;
use pvc\interfaces\msg\MsgInterface;

/**
 * @covers \pvc\html\tag\abstract\Tag
 */
class TagTest extends TestCase
{

    protected AttributeFactory $attributeFactory;
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
        $this->attributeFactory = $this->createMock(AttributeFactory::class);
        $this->tagName = 'div';
        $this->tag = new Tag($this->attributeFactory);
        $this->tag->setName($this->tagName);
        $this->testMsg = $this->createMock(MsgInterface::class);
        $this->mockInnerTagVoid = $this->createMock(TagVoidInterface::class);
        $this->mockInnerTag = $this->createMock(TagInterface::class);
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