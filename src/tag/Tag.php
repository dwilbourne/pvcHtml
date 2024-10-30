<?php

/**
 * @author: Doug Wilbourne (dougwilbourne@gmail.com)
 */

declare(strict_types=1);

namespace pvc\html\tag;

use pvc\html\err\AmbiguousMethodCallException;
use pvc\html\err\InvalidInnerTextException;
use pvc\html\err\InvalidMethodCallException;
use pvc\html\err\InvalidSubTagException;
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
     * canAddSubTag
     * @param TagVoidInterface $subTag
     * @return bool
     */
    protected function canAddSubTag(TagVoidInterface $subTag): bool
    {
        /**
         * empty allowedSubTag array means you can put any tag in there, which is wrong, but gives us some slack
         * for the moment in determining what subtags are allowed inside each tag.
         */
        if (empty($this->getAllowedSubTags())) {
            return true;
        }

        /**
         * The subtag must be valid.
         */
        if (!in_array($subTag->getName(), $this->getAllowedSubTags())) {
            return false;
        }

        return true;
    }

    /**
     * addSubTagObject
     * @param TagVoidInterface $tag
     * @throws InvalidSubTagException
     */
    public function addSubTagObject(TagVoidInterface $tag): TagVoidInterface
    {
        if (!$this->canAddSubTag($tag)) {
            throw new InvalidSubTagException($tag->getName());
        }
        $this->innerHtml[] = $tag;
        return $tag;
    }

    /**
     * addSubTag
     * @param string $tagName
     * @return TagVoidInterface
     * @throws InvalidSubTagException
     */
    public function addSubTag(string $tagName): TagVoidInterface
    {
        $newTag = $this->factory->makeElement($tagName);
        $this->addSubTagObject($newTag);
        return $newTag;
    }

    /**
     * __call
     * @param string $name
     * @param array<string> $arguments
     * @return mixed
     * @throws AmbiguousMethodCallException
     * @throws InvalidMethodCallException
     * @throws InvalidSubTagException
     * @throws \pvc\html\err\UnsetAttributeNameException
     * @throws \pvc\html\err\InvalidAttributeIdNameException
     */
    public function __call(string $name, array $arguments): mixed
    {
        if ($this->factory->isAmbiguousName($name)) {
            throw new AmbiguousMethodCallException($name);
        }

        if ($this->factory->canMakeElement($name)) {
            $subtagObject = $this->factory->makeElement($name);
            return $this->addSubTagObject($subtagObject);
        }

        if ($this->factory->canMakeAttribute($name)) {
            return $this->setAttribute($name, ...$arguments);
        }
        /**
         * not a method and not something we know how to make....
         */
        throw new InvalidMethodCallException($name);
    }


    /**
     * getSubTag
     * @param string $subTagName
     * @return TagVoidInterface|null
     * returns the first subtag whose tag id equals the supplied argument
     */
    public function getSubTag(string $subTagName): TagVoidInterface|null
    {
        $filter = function($x) { return $x instanceOf TagVoidInterface; };
        $subTags = array_filter($this->getSubTags(), $filter);
        foreach ($subTags as $subTag) {
            if ($subTagName == $subTag->getName()) {
                return $subTag;
            }
        }
        return null;
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
     * addMsg
     * @param MsgInterface $msg
     */
    public function addMsg(MsgInterface $msg): void
    {
        $this->innerHtml[] = $msg;
    }

    /**
     * addText
     * @param string $text
     */
    public function addText(string $text): void
    {
        $this->innerHtml[] = $text;
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