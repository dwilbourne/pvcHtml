<?php

/**
 * @author: Doug Wilbourne (dougwilbourne@gmail.com)
 */

declare(strict_types=1);

namespace pvc\html\tag;

use pvc\html\err\ChildElementNotAllowedException;
use pvc\interfaces\html\factory\definitions\AbstractDefinitionFactoryInterface;
use pvc\interfaces\html\tag\TagInterface;
use pvc\interfaces\html\tag\TagVoidInterface;
use pvc\interfaces\msg\MsgFactoryInterface;
use pvc\interfaces\msg\MsgInterface;

/**
 *
 * class Tag
 * @template Definition of AbstractDefinitionFactoryInterface
 * @extends TagVoid<Definition>
 * @implements TagInterface<Definition>
 *
 * Handles all elements which have a closing tag
 */
class Tag extends TagVoid implements TagInterface
{
    /**
     * @var array<string>
     * an empty array means that any tag is allowed as a subtag
     */
    protected array $allowedChildDefIds = [];

    /**
     * @var array<TagVoidInterface<Definition>|MsgInterface|string>
     */
    protected array $childElements = [];

    /**
     * @var MsgFactoryInterface
     */
    protected MsgFactoryInterface $msgFactory;

    /**
     * getMsgFactory
     * @return MsgFactoryInterface
     */
    public function getMsgFactory(): MsgFactoryInterface
    {
        return $this->msgFactory;
    }

    /**
     * setMsgFactory
     * @param MsgFactoryInterface $msgFactory
     */
    public function setMsgFactory(MsgFactoryInterface $msgFactory): void
    {
        $this->msgFactory = $msgFactory;
    }

    /**
     * getAllowedChildDefIds
     * @return array<string>
     */
    public function getAllowedChildDefIds(): array
    {
        return $this->allowedChildDefIds;
    }

    /**
     * setAllowedSubTags
     * @param array<string> $defIds
     */
    public function setAllowedChildDefIds(array $defIds): void
    {
        $this->allowedChildDefIds = $defIds;
    }

    /**
     * isAllowedChildElement
     * @param string $defId
     * @return bool
     */
    public function isAllowedChildDefId(string $defId): bool
    {
        /**
         * empty allowedSubTag array means you can put any tag in there, which is wrong, but gives us some slack
         * for the moment in determining what child elements are allowed inside each tag.  In other words,
         * the definitions inside the container are not yet complete
         */
        if (empty($this->getAllowedChildDefIds())) {
            return true;
        }

        /**
         * The child element must be allowed.
         */
        if (!in_array($defId, $this->getAllowedChildDefIds())) {
            return false;
        }

        return true;
    }

    /**
     * setChild
     * @param string|TagVoidInterface<Definition> $element
     * @param string|null $key
     * @return TagVoidInterface<Definition>
     * @throws ChildElementNotAllowedException
     */
    public function setChild(string|TagVoidInterface $element, string $key = null): TagVoidInterface
    {
        if ($element instanceof TagVoidInterface) {
            $defId = $element->getDefId();
        } else {
            $defId = $element;
        }

        if (!$this->isAllowedChildDefId($defId)) {
            throw new ChildElementNotAllowedException($defId);
        }

        if (is_string($element)) {
            $element = $this->getHtmlFactory()->makeElement($defId);
        }

        $this->childElements[$key ?: $this->generateChildKey($element)] = $element;
        return $element;
    }

    /**
     * generateChildKey
     * @param TagVoidInterface<Definition> $childElement
     * @return string
     *
     * This method looks at all existing children that are the same type of element (e.g. the name attribute) and
     * generates a mnemonic key which has an excellent chance of being unique among the children, though it is not
     * guaranteed.
     *
     * Let's say you add three div children to the current element.  If you do not supply the keys manually, then this
     * method will name them div0, div1, and div2.
     *
     * Of course, it is possible to do something that does not make sense
     * and that produces a potentially unwanted behavior.  Let's say you add a div child element to the current element
     * and manually assign a key of div1.  Then you add a div child element and do not specify the id.  This algorithm
     * will generate an id of div1, and when this new element is set you will overwrite the existing element that has
     * a key of div1.
     */
    protected function generateChildKey(TagVoidInterface $childElement): string
    {
        /**
         * tag type is something like 'form' or 'div' etc...
         */
        $tagType = $childElement->getName();
        $callBack = function(TagVoidInterface $tag) use ($tagType) {
            return ($tag->getName() == $tagType);
        };
        $childElementsOfSameType = $this->getChildren($callBack);
        $uniqueSuffix = count($childElementsOfSameType);
        return $tagType . $uniqueSuffix;
    }

    /**
     * getChild
     * @param string $key
     * @return TagVoidInterface<Definition>|MsgInterface|string|null
     */
    public function getChild(string $key): TagVoidInterface|MsgInterface|string|null
    {
        return $this->childElements[$key] ?? null;
    }

    /**
     * getChildren
     * @param callable|null $filter
     * @return array<TagVoidInterface<Definition>|MsgInterface|string>
     */
    public function getChildren(callable $filter = null): array
    {
        return $filter? array_filter($this->childElements, $filter) : $this->childElements;
    }

    /**
     * getInnerText
     * @return MsgInterface|string|null
     */
    public function getInnerText(): MsgInterface|string|null
    {
        $result = $this->childElements['innerText'] ?? null;
        if ($result) {
            assert($result instanceof MsgInterface || is_string($result));
        }
        return $result;
    }

    /**
     * setInnerText
     * @param MsgInterface|string $innerText
     */
    public function setInnerText(MsgInterface|string $innerText): void
    {
        $this->childElements['innerText'] = $innerText;
    }

    /**
     * no magic set / get for tags, use set/getChild.  The magic methods __set
     * and __get are defined in the TagVoid class and are reserved for attributes.  Since this class extends
     * TagVoid, any magic getter would create a Liskov problem because the return value would be a superset of
     * TagVoid's __get method.  Also, the setter would be highly confusing to read because it would be uncertain
     * whether you are trying to get an existing child element by its id or trying to create a new one.  KISS.....
     */

    /**
     * generateClosingTag
     * @return string
     */
    public function generateClosingTag(): string
    {
        return '</' . $this->name . '>';
    }
}