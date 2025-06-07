<?php

namespace pvc\html\content_model;

use pvc\html\element\Element;
use pvc\interfaces\html\element\ElementInterface;
use pvc\interfaces\html\element\ElementVoidInterface;
use pvc\interfaces\msg\MsgInterface;

abstract class ContentModel
{
    /**
     * @var array<ContentCategory>
     */
    protected array $contentCategories;

    abstract public function canAddChildElement(
        ElementInterface $parent,
        ElementInterface|ElementVoidInterface $child) : bool;


    public function canAddTextNode(string|MsgInterface $text) : bool
    {
        return true;
    }

    public function getContentCategories() : array
    {
        return $this->contentCategories;
    }

    public function hasContentCategory(ContentCategory $category) : bool
    {
        return in_array($category, $this->contentCategories);
    }
}