<?php

/**
 * @author: Doug Wilbourne (dougwilbourne@gmail.com)
 */

declare (strict_types=1);

namespace pvcTests\html\tag\factory;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use pvc\html\err\InvalidTagException;
use pvc\html\tag\abstract\Tag;
use pvc\html\tag\abstract\TagVoid;
use pvc\html\tag\factory\TagFactory;
use pvc\interfaces\html\attribute\AttributeInterface;

class TagFactoryTest extends TestCase
{
    protected ContainerInterface|MockObject $attributeFactory;

    protected TagFactory $tagFactory;

    public function setUp(): void
    {
        $this->attributeFactory = $this->createMock(ContainerInterface::class);
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

    /**
     * testTagFactoryMakesRequiredAttributes
     * @throws InvalidTagException
     * @covers \pvc\html\tag\factory\TagFactory::makeTag
     */
    public function testTagFactoryMakesRequiredAttributesAndSubTags(): void
    {
        $tagName = 'html';

        /**
         * the html tag has one required attribute - the lang attribute, whose default value is 'en'
         */
        $expectedAttributeName = 'lang';
        $expectedAttributeValue = 'en';

        $attribute = $this->createMock(AttributeInterface::class);
        $attribute->method('getName')->willReturn($expectedAttributeName);
        $attribute->method('getValue')->willReturn($expectedAttributeValue);

        $this->attributeFactory
            ->expects($this->once())
            ->method('get')
            ->willReturn($attribute);

        $tag = $this->tagFactory->makeTag($tagName);

        self::assertEquals(1, count($tag->getAttributes()));
        $attribute = $tag->getAttribute($expectedAttributeName);
        self::assertInstanceOf(AttributeInterface::class, $attribute);
        self::assertEquals($expectedAttributeValue, $tag->$expectedAttributeName);
    }
}
