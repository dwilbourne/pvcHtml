<?php

/**
 * @author: Doug Wilbourne (dougwilbourne@gmail.com)
 */

declare (strict_types=1);

namespace pvcTests\html\tag\factory;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use pvc\html\attribute\factory\AttributeFactory;
use pvc\html\err\InvalidTagException;
use pvc\html\tag\abstract\Tag;
use pvc\html\tag\abstract\TagVoid;
use pvc\html\tag\factory\TagFactory;

class TagFactoryTest extends TestCase
{
    protected AttributeFactory|MockObject $attributeFactory;

    protected TagFactory $tagFactory;

    public function setUp(): void
    {
        $this->attributeFactory = $this->createMock(AttributeFactory::class);
        $this->tagFactory = new TagFactory($this->attributeFactory);
    }

    /**
     * testConstruct
     * @covers \pvc\html\tag\factory\TagFactory::__construct
     */
    public function testConstruct(): void
    {
        self::assertInstanceOf(TagFactory::class, $this->tagFactory);
    }

    /**
     * testTagFactoryThrowsExceptionWithInvalidTagName
     * @throws InvalidTagException
     * @covers \pvc\html\tag\factory\TagFactory::makeTag
     */
    public function testTagFactoryThrowsExceptionWithInvalidTagName(): void
    {
        self::expectException(InvalidTagException::class);
        $this->tagFactory->makeTag('foo');
    }

    /**
     * testTagFactoryMakesVoidTag
     * @throws InvalidTagException
     * @covers \pvc\html\tag\factory\TagFactory::makeTag
     */
    public function testTagFactoryMakesVoidTag(): void
    {
        $voidTagName = 'base';
        self::assertInstanceOf(TagVoid::class, $this->tagFactory->makeTag($voidTagName));
    }

    /**
     * testTagFactoryMakesRegularTag
     * @throws InvalidTagException
     * @covers \pvc\html\tag\factory\TagFactory::makeTag
     */
    public function testTagFactoryMakesRegularTag(): void
    {
        $tagName = 'div';
        self::assertInstanceOf(Tag::class, $this->tagFactory->makeTag($tagName));
    }
}
