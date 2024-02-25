<?php

/**
 * @author: Doug Wilbourne (dougwilbourne@gmail.com)
 */
declare(strict_types=1);

namespace pvc\html\tag\factory;

use Psr\Container\ContainerInterface;
use pvc\html\tag\abstract\Tag;
use pvc\html\tag\abstract\TagVoid;

/**
 * Class AbstractTagFactory
 */
class AbstractTagFactory
{
    /**
     * @var ContainerInterface
     */
    protected ContainerInterface $container;

    /**
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * makeTagVoid
     * @param string $tagName
     * @return TagVoid
     */
    public function makeTagVoid(string $tagName): TagVoid
    {
        return new TagVoid($this->container);
    }

    /**
     * makeTag
     * @param string $tagName
     * @return Tag
     */
    public function makeTag(string $tagName): Tag
    {
        return new Tag($this->container);
    }
}