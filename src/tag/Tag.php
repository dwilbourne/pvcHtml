<?php

/**
 * @author: Doug Wilbourne (dougwilbourne@gmail.com)
 */

declare(strict_types=1);

namespace pvc\html\abstract\tag;

use pvc\html\abstract\err\InvalidInnerTextException;
use pvc\html\abstract\err\InvalidSubTagException;
use pvc\interfaces\html\tag\TagInterface;
use pvc\interfaces\html\tag\TagVoidInterface;
use pvc\interfaces\msg\MsgInterface;

/**
 *
 * class Tag
 *
 * Handles all tags which have a closing tag
 */
class Tag extends TagVoid implements TagInterface
{
    /**
     * @var array<string>
     * an empty array means that any tag is allowed as a subtag
     */
    protected array $allowedSubTags = [];

    /**
     * @var array<TagVoidInterface|MsgInterface|string>
     */
    protected array $innerHtml = [];

    /**
     * getAllowedSubTags
     * @return array<string>
     */
    public function getAllowedSubTags(): array
    {
        return $this->allowedSubTags;
    }

    /**
     * setAllowedSubTags
     * @param array<string> $subTagNames
     */
    public function setAllowedSubTags(array $subTagNames): void
    {
        $this->allowedSubTags = $subTagNames;
    }

    /**
     * addInnerHTML
     * @param TagVoidInterface|MsgInterface|string $innerHtml
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
     */
    protected function addInnerText(MsgInterface|string $innerText): void
    {
        $this->innerHtml[] = $innerText;
    }

    /**
     * addSubtag
     * @param TagVoidInterface $tag
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
     * @param TagVoidInterface $subTag
     * @return bool
     */
    protected function canAddSubTag(TagVoidInterface $subTag): bool
    {
        if (empty($this->getAllowedSubTags())) {
            return true;
        }

        $subTagName = $subTag->getName();

        /**
         * The subtag must be valid.
         */
        if (!in_array($subTagName, $this->getAllowedSubTags())) {
            return false;
        }

        return true;
    }

    /**
     * getSubTags
     * @return array<TagVoidInterface>
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
     * @return TagVoidInterface|null
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
     * @return array<TagVoidInterface|MsgInterface|string>
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