<?php

/**
 * @author: Doug Wilbourne (dougwilbourne@gmail.com)
 */

declare(strict_types=1);

namespace pvc\html\tag;

use pvc\html\err\InvalidInnerTextException;
use pvc\html\err\InvalidSubTagException;
use pvc\interfaces\html\tag\TagVoidInterface;
use pvc\interfaces\msg\MsgInterface;

/**
 *
 * class Tag.  Handles all tags which have a closing tag
 * @template ValueType
 * @template ValTesterType
 * @extends TagVoid<ValueType, ValTesterType>
 */
class Tag extends TagVoid
{
    /**
     * @var array<TagVoidInterface<ValueType, ValTesterType>|MsgInterface|string>
     */
    protected array $innerHtml = [];

    /**
     * addInnerHTML
     * @param TagVoidInterface<ValueType, ValTesterType>|MsgInterface|string $innerHtml
     * @throws InvalidSubTagException
     */
    public function addInnerHTML(TagVoidInterface|MsgInterface|string $innerHtml): void
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

    /**
     * addInnerText
     * @param MsgInterface|string $innerText
     * @throws InvalidInnerTextException
     */
    protected function addInnerText(MsgInterface|string $innerText): void
    {
        if ($this->htmlConfig->innerTextNotAllowed($this->getName())) {
            throw new InvalidInnerTextException($this->getName());
        }
        $this->innerHtml[] = $innerText;
    }

    /**
     * addSubtag
     * @param TagVoidInterface<ValueType, ValTesterType> $tag
     * @throws InvalidSubTagException
     */
    protected function addSubtag(TagVoidInterface $tag): void
    {
        if (!$this->canAddSubTag($tag)) {
            throw new InvalidSubTagException($tag->getName());
        }
        $this->innerHtml[] = $tag;
    }

    /**
     * canAddSubTag
     * @param TagVoidInterface<ValueType, ValTesterType> $subTag
     * @return bool
     */
    protected function canAddSubTag(TagVoidInterface $subTag): bool
    {
        $parentTagName = $this->getName();
        $subTagName = $subTag->getName();

        /**
         * The subtag must be valid.
         */
        if (!$this->getHtmlConfig()->isValidSubTag($subTagName, $parentTagName)) {
            return false;
        }

        /**
         * cannot add a block element inside an inline element
         */
        if ($this->getHtmlConfig()->isInlineTag($parentTagName) && $this->getHtmlConfig()->isBlockTag($subTagName)) {
            return false;
        }

        return true;
    }

    /**
     * getSubTags
     * @return array<TagVoidInterface<ValueType, ValTesterType>>
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
     * @return TagVoidInterface<ValueType, ValTesterType>|null
     * returns the first subtag whose tag name equals the supplied argument
     */
    public function getSubTag(string $subTagName): TagVoidInterface|null
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
     * @return array<TagVoidInterface<ValueType, ValTesterType>|MsgInterface|string>
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