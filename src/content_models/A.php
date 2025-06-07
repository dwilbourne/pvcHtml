<?php

namespace pvc\html\content_models;

use pvc\html\content_model\ContentCategory;
use pvc\html\content_model\ContentModel;
use pvc\interfaces\html\element\ElementInterface;
use pvc\interfaces\html\element\ElementVoidInterface;

class A extends ContentModel
{
    public function canAddChildElement(
        ElementInterface $parent,
        ElementInterface|ElementVoidInterface $child) : bool
    {
        /**
         * anchor cannot have another anchor inside it
         */
        if ($parent->name === $child->name) return false;

        /**
         * child and its descendants cannot have a tabIndex
         */
        if ($child->contentModel->hasDescendantWithTabIndex($child)) {
            return false;
        }

        /**
         * anchor cannot have a child that has interactive content
         */
        if ($this->hasDescendantWithInteractiveContent($child)) {
            return false;
        }

        return $child->contentModel->hasContentCategory(ContentCategory::Transparent);

    }

    protected function hasDescendantWithTabIndex(ElementInterface|ElementVoidInterface $child): bool
    {
        foreach($child->getAttributes() as $attribute) {
            if ($attribute->name === 'tabindex') {
                return true;
            }
        }
        foreach($child->getNodes() as $node) {
            if ($node instanceof ElementVoidInterface) {
                return $this->hasDescendantWithTabIndex($node);
            }
        }
        return false;
    }

    protected function hasDescendantWithInteractiveContent(ElementInterface|ElementVoidInterface $child): bool
    {
        foreach($child->getNodes() as $node) {
            if ($node instanceof ElementVoidInterface) {
                return $this->isInteractiveContent($node);
            }
        }
        return false;
    }

    protected function isInteractiveContent(ElementInterface|ElementVoidInterface $child): bool
    {
        $categories = $child->contentModel->getContentCategories();
        foreach($categories as $category) {
            if ($category instanceof ContentCategory::Interactive) {
                return true;
            }
        }
        return false;
    }

}