<?php

/**
 * @author: Doug Wilbourne (dougwilbourne@gmail.com)
 */

declare(strict_types=1);

namespace pvc\html\tag\abstract;

use pvc\html\config\HtmlConfig;
use pvc\html\err\InnerTextNotAllowedException;
use pvc\html\err\InvalidSubTagException;
use pvc\interfaces\html\tag\TagInterface;
use pvc\interfaces\html\tag\TagVoidInterface;
use pvc\interfaces\msg\MsgInterface;

/**
 *
 * class Tag.  Handles all tags which have a closing tag
 *
 */
class Tag extends TagVoid
{
    /**
     * @var array<TagVoidInterface|MsgInterface|string>
     */
    protected array $innerHtml = [];

    /**
     * addInnerHTML
     * @param TagVoidInterface|TagInterface|MsgInterface|string $innerHtml
     * @throws InvalidSubTagException
     */
    public function addInnerHTML(TagVoidInterface|TagInterface|MsgInterface|string $innerHtml): void
    {
        /**
         * It is possible to embed html into a text string and add that as innerHtml.  For example, you can add
         * something like '<div>I am a cow</div><div>I am a horse</div>'.
         *
         * This code does *not* check for the potential presence of tags embedded in text
         */
        if ($innerHtml instanceof MsgInterface || is_string($innerHtml)) {
            $this->addInnerText($innerHtml);
        } else {
            $this->addSubtag($innerHtml);
        }
    }

    protected function addInnerText(MsgInterface|string $innerText): void
    {
        if (HtmlConfig::innerTextNotAllowed($this->getName())) {
            throw new InnerTextNotAllowedException($this->getName());
        }
        $this->innerHtml[] = $innerText;
    }

    protected function addSubtag(TagVoidInterface|TagInterface $tag): void
    {
        if (!$this->canAddSubTag($tag)) {
            throw new InvalidSubTagException($tag->getName());
        }
        $this->innerHtml[] = $tag;
    }

    protected function canAddSubTag(TagVoidInterface|TagInterface $subTag): bool
    {
        $parentTagName = $this->getName();
        $subTagName = $subTag->getName();

        /**
         * The subtag must be valid.
         */
        if (!HtmlConfig::isValidSubtag($subTagName, $parentTagName)) {
            return false;
        }

        /**
         * cannot add a block element inside an inline element
         */
        if (HtmlConfig::isInlineElement($parentTagName) && HtmlConfig::isBlockElement($subTagName)) {
            return false;
        }

        return true;
    }

    /**
     * getSubTags
     * @return array<TagVoidInterface|TagInterface>
     */
    public function getSubTags(): array
    {
        return array_filter($this->getInnerHtml(), function ($x) {
            return $x instanceof TagVoidInterface;
        });
    }

    /**
     * getSubTag
     * @param string $subTagName
     * @return TagVoidInterface|TagInterface|null
     * returns the first subtag whose tag name equals the supplied argument
     */
    public function getSubTag(string $subTagName): TagVoidInterface|TagInterface|null
    {
        $subTags = $this->getSubTags();
        foreach ($subTags as $subTag) {
            if ($subTagName == $subTag->getName()) {
                return $subTag;
            }
        }
        return null;
    }

    /**
     * getInnerHtml
     * @return array<TagVoidInterface|TagInterface|MsgInterface|string>
     */
    public function getInnerHtml(): array
    {
        return $this->innerHtml;
    }

    /**
     * generateClosingTag
     * @return string
     */
    public function generateClosingTag(): string
    {
        return '</' . $this->name . '>';
    }
}