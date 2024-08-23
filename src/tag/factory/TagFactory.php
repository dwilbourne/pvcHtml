<?php

/**
 * @author: Doug Wilbourne (dougwilbourne@gmail.com)
 */

declare(strict_types=1);

namespace pvc\html\tag\factory;

use Psr\Container\ContainerInterface;
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
     * @var ContainerInterface
     */
    protected ContainerInterface $attributeFactory;

    public function __construct(ContainerInterface $attributeFactory)
    {
        $this->attributeFactory = $attributeFactory;
    }

    /**
     * makeTag
     * @param string $tagName
     * @return TagVoid|Tag
     */
    public function makeTag(string $tagName): TagVoid|Tag
    {
        /**
         * make sure tag name is valid
         */
        if (!HtmlConfig::isValidTagName($tagName)) {
            throw new InvalidTagException($tagName);
        }

        /**
         * make the tag and set the name
         */
        $tag = (HtmlConfig::isTagVoid($tagName) ? new TagVoid($this->attributeFactory) : new Tag($this->attributeFactory));
        $tag->setName($tagName);

        /**
         * make the required attributes
         */
        foreach (HtmlConfig::getRequiredAttributes($tag->getName()) as $attributeName => $attributeValue) {
            $tag->$attributeName = $attributeValue;
        }

        return $tag;
    }

}