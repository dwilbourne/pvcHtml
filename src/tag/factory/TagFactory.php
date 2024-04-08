<?php

/**
 * @author: Doug Wilbourne (dougwilbourne@gmail.com)
 */

declare(strict_types=1);

namespace pvc\html\tag\factory;

use pvc\html\attribute\factory\AttributeFactory;
use pvc\html\config\HtmlConfig;
use pvc\html\err\InvalidTagException;
use pvc\html\tag\abstract\Tag;
use pvc\html\tag\abstract\TagVoid;

/**
 * Class TagFactory
 */
class TagFactory
{
    /**
     * @var AttributeFactory
     */
    protected AttributeFactory $attributeFactory;

    public function __construct(AttributeFactory $attributeFactory)
    {
        $this->attributeFactory = $attributeFactory;
    }

    /**
     * makeTag
     * @param string $tagName
     * @return Tag|TagVoid
     */
    public function makeTag(string $tagName): Tag|TagVoid
    {
        if (!HtmlConfig::isValidTagName($tagName)) {
            throw new InvalidTagException($tagName);
        }
        $tag = (
        HtmlConfig::isTagVoid($tagName) ?
            new TagVoid($this->attributeFactory) :
            new Tag($this->attributeFactory)
        );

        return $tag;
    }
}