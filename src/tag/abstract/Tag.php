<?php

/**
 * @author: Doug Wilbourne (dougwilbourne@gmail.com)
 */

declare(strict_types=1);

namespace pvc\html\tag\abstract;

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
     */
    public function addInnerHTML(TagVoidInterface|TagInterface|MsgInterface|string $innerHtml): void
    {
        $this->innerHtml[] = $innerHtml;
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