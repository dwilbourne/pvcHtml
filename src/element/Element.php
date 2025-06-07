<?php

/**
 * @author: Doug Wilbourne (dougwilbourne@gmail.com)
 */

declare(strict_types=1);

namespace pvc\html\element;

use pvc\html\content_model\ContentModel;
use pvc\html\factory\HtmlFactory;
use pvc\interfaces\html\element\ElementInterface;
use pvc\interfaces\html\element\ElementVoidInterface;
use pvc\interfaces\msg\MsgInterface;

/**
 *
 * class Element
 *
 * Handles all elements which have a closing element
 */
class Element extends ElementVoid implements ElementInterface
{
    /**
     * @var array<ElementVoidInterface|ElementInterface|MsgInterface|string>
     */
    protected array $childNodes = [];

    /**
     * @return array<ElementVoidInterface|MsgInterface|string>
     */
    public function getNodes(): array
    {
        return $this->childNodes;
    }

    /**
     * setChild
     * @param class-string|ElementVoidInterface|ElementInterface $element
     * @return ElementVoidInterface|ElementInterface
     */
    public function setChild(string|ElementVoidInterface|ElementInterface $element): void
    {
        if (is_string($element)) {
            $element = $this->htmlFactory->makeElement($element);
        }

        $this->childNodes[] = $element;
    }

    /**
     * setInnerText
     * @param MsgInterface|string $innerText
     */
    public function setInnerText(MsgInterface|string $innerText): void
    {
        $this->childNodes[] = $innerText;
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