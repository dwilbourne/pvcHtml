<?php

/**
 * @author: Doug Wilbourne (dougwilbourne@gmail.com)
 */

declare (strict_types=1);

namespace pvcTests\html\tag\factory;

use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use pvc\html\tag\abstract\Tag;
use pvc\html\tag\abstract\TagVoid;
use pvc\html\tag\factory\AbstractTagFactory;
use pvc\html\tag\factory\TagFactory;

class TagFactoryTest extends TestCase
{
    protected ContainerInterface $container;
    protected AbstractTagFactory $abstractTagFactory;

    protected TagFactory $factory;

    /**
     * setUp
     * this is not, of course, a unit test per se.  But mocking AbstractTagFactory would merely duplicate the code
     * inside AbstractTagFactory.  So it seems better to run an integration test.
     */
    public function setUp(): void
    {
        $this->container = $this->createMock(ContainerInterface::class);
        $this->abstractTagFactory = new AbstractTagFactory($this->container);
        $this->factory = new TagFactory($this->abstractTagFactory);
    }

    /**
     * testConstruct
     * @covers \pvc\html\tag\factory\TagFactory::__construct
     */
    public function testConstruct(): void
    {
        self::assertInstanceOf(TagFactory::class, $this->factory);
    }

    /**
     * testMakeA
     * @covers \pvc\html\tag\factory\TagFactory::makeA
     */
    public function testMakeA(): void
    {
        $tagName = 'a';
        $tag = $this->factory->makeA();
        self::assertInstanceOf(Tag::class, $tag);
    }

    /**
     * testMakeBase
     * @covers \pvc\html\tag\factory\TagFactory::makeBase
     */
    public function testMakeBase(): void
    {
        $tagName = 'base';
        $tag = $this->factory->makeBase();
        self::assertInstanceOf(TagVoid::class, $tag);
    }

    /**
     * testMakeBody
     * @covers \pvc\html\tag\factory\TagFactory::makeBody
     */
    public function testMakeBody(): void
    {
        $tagName = 'body';
        $tag = $this->factory->makeBody();
        self::assertInstanceOf(Tag::class, $tag);
    }

    /**
     * testMakeDiv
     * @covers \pvc\html\tag\factory\TagFactory::makeDiv
     */
    public function testMakeDiv(): void
    {
        $tagName = 'div';
        $tag = $this->factory->makeDiv();
        self::assertInstanceOf(Tag::class, $tag);
    }

    /**
     * testMakeFooter
     * @covers \pvc\html\tag\factory\TagFactory::makeFooter
     */
    public function testMakeFooter(): void
    {
        $tagName = 'footer';
        $tag = $this->factory->makeFooter();
        self::assertInstanceOf(Tag::class, $tag);
    }

    /**
     * testMakeForm
     * @covers \pvc\html\tag\factory\TagFactory::makeForm
     */
    public function testMakeForm(): void
    {
        $tagName = 'form';
        $tag = $this->factory->makeForm();
        self::assertInstanceOf(Tag::class, $tag);
    }

    /**
     * testMakeHead
     * @covers \pvc\html\tag\factory\TagFactory::makeHead
     */
    public function testMakeHead(): void
    {
        $tagName = 'head';
        $tag = $this->factory->makeHead();
        self::assertInstanceOf(Tag::class, $tag);
    }

    /**
     * testMakeHtml
     * @covers \pvc\html\tag\factory\TagFactory::makeHtml
     */
    public function testMakeHtml(): void
    {
        $tagName = 'html';
        $tag = $this->factory->makeHtml();
        self::assertInstanceOf(Tag::class, $tag);
    }

    /**
     * testMakeImg
     * @covers \pvc\html\tag\factory\TagFactory::makeImg
     */
    public function testMakeImg(): void
    {
        $tagName = 'img';
        $tag = $this->factory->makeImg();
        self::assertInstanceOf(TagVoid::class, $tag);
    }

    /**
     * testMakeLink
     * @covers \pvc\html\tag\factory\TagFactory::makeLink
     */
    public function testMakeLink(): void
    {
        $tagName = 'link';
        $tag = $this->factory->makeLink();
        self::assertInstanceOf(TagVoid::class, $tag);
    }

    /**
     * testMakeSpan
     * @covers \pvc\html\tag\factory\TagFactory::makeSpan
     */
    public function testMakeSpan(): void
    {
        $tagName = 'span';
        $tag = $this->factory->makeSpan();
        self::assertInstanceOf(Tag::class, $tag);
    }

    /**
     * testMakeStyle
     * @covers \pvc\html\tag\factory\TagFactory::makeStyle
     */
    public function testMakeStyle(): void
    {
        $tagName = 'style';
        $tag = $this->factory->makeStyle();
        self::assertInstanceOf(Tag::class, $tag);
    }

    /**
     * testMakeTitle
     * @covers \pvc\html\tag\factory\TagFactory::makeTitle
     */
    public function testMakeTitle(): void
    {
        $tagName = 'title';
        $tag = $this->factory->makeTitle();
        self::assertInstanceOf(Tag::class, $tag);
    }
}
