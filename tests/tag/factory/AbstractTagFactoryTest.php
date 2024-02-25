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

class AbstractTagFactoryTest extends TestCase
{
    protected ContainerInterface $container;

    protected AbstractTagFactory $factory;

    public function setUp(): void
    {
        $this->container = $this->createMock(ContainerInterface::class);
        $this->factory = new AbstractTagFactory($this->container);
    }

    /**
     * testConstruct
     * @covers \pvc\html\tag\factory\AbstractTagFactory::__construct
     */
    public function testConstruct(): void
    {
        self::assertInstanceOf(AbstractTagFactory::class, $this->factory);
    }

    /**
     * testMakeTagVoid
     * @covers \pvc\html\tag\factory\AbstractTagFactory::makeTagVoid
     */
    public function testMakeTagVoid(): void
    {
        $tagName = 'div';
        $tag = $this->factory->makeTagVoid($tagName);
        self::assertInstanceOf(TagVoid::class, $tag);
    }

    /**
     * testMakeTag
     * @covers \pvc\html\tag\factory\AbstractTagFactory::makeTag
     */
    public function testMakeTag(): void
    {
        $tagName = 'div';
        $tag = $this->factory->makeTag($tagName);
        self::assertInstanceOf(Tag::class, $tag);
    }
}
