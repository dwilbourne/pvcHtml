<?php

/**
 * @author: Doug Wilbourne (dougwilbourne@gmail.com)
 */

declare(strict_types=1);

namespace pvc\html\element;

use Error;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use pvc\html\attribute\GlobalAttributes;
use pvc\html\attributes\AccesskeyTrait;
use pvc\html\attributes\ClassTrait;
use pvc\html\attributes\ContenteditableTrait;
use pvc\html\attributes\DirTrait;
use pvc\html\attributes\DraggableTrait;
use pvc\html\attributes\EnterkeyhintTrait;
use pvc\html\attributes\HiddenTrait;
use pvc\html\attributes\IdTrait;
use pvc\html\attributes\InertTrait;
use pvc\html\attributes\InputmodeTrait;
use pvc\html\attributes\LangTrait;
use pvc\html\attributes\PopoverTrait;
use pvc\html\attributes\SpellcheckTrait;
use pvc\html\attributes\TabindexTrait;
use pvc\html\attributes\TranslateTrait;
use pvc\html\content_model\ContentModel;
use pvc\html\err\GetDataTypeException;
use pvc\html\err\InvalidAttributeException;
use pvc\html\factory\HtmlFactory;
use pvc\htmlbuilder\definitions\types\AttributeValueDataType;
use pvc\interfaces\html\attribute\AttributeInterface;
use pvc\interfaces\html\element\ElementVoidInterface;
use pvc\html\events\OnabortTrait;
use pvc\html\events\OnauxclickTrait;
use pvc\html\events\OnbeforeinputTrait;
use pvc\html\events\OnbeforematchTrait;
use pvc\html\events\OnbeforetoggleTrait;
use pvc\html\events\OnblurTrait;
use pvc\html\events\OncancelTrait;
use pvc\html\events\OncanplayTrait;
use pvc\html\events\OncanplaythroughTrait;
use pvc\html\events\OnchangeTrait;
use pvc\html\events\OnclickTrait;
use pvc\html\events\OncloseTrait;
use pvc\html\events\OncontextlostTrait;
use pvc\html\events\OncontextmenuTrait;
use pvc\html\events\OncontextrestoredTrait;
use pvc\html\events\OncopyTrait;
use pvc\html\events\OncuechangeTrait;
use pvc\html\events\OncutTrait;
use pvc\html\events\OndblclickTrait;
use pvc\html\events\OndragTrait;
use pvc\html\events\OndragendTrait;
use pvc\html\events\OndragenterTrait;
use pvc\html\events\OndragleaveTrait;
use pvc\html\events\OndragoverTrait;
use pvc\html\events\OndragstartTrait;
use pvc\html\events\OndropTrait;
use pvc\html\events\OndurationchangeTrait;
use pvc\html\events\OnemptiedTrait;
use pvc\html\events\OnendedTrait;
use pvc\html\events\OnerrorTrait;
use pvc\html\events\OnfocusTrait;
use pvc\html\events\OnformdataTrait;
use pvc\html\events\OninputTrait;
use pvc\html\events\OninvalidTrait;
use pvc\html\events\OnkeydownTrait;
use pvc\html\events\OnkeypressTrait;
use pvc\html\events\OnkeyupTrait;
use pvc\html\events\OnloadTrait;
use pvc\html\events\OnloadeddataTrait;
use pvc\html\events\OnloadedmetadataTrait;
use pvc\html\events\OnloadstartTrait;
use pvc\html\events\OnmousedownTrait;
use pvc\html\events\OnmouseenterTrait;
use pvc\html\events\OnmouseleaveTrait;
use pvc\html\events\OnmousemoveTrait;
use pvc\html\events\OnmouseoutTrait;
use pvc\html\events\OnmouseoverTrait;
use pvc\html\events\OnmouseupTrait;
use pvc\html\events\OnpasteTrait;
use pvc\html\events\OnpauseTrait;
use pvc\html\events\OnplayTrait;
use pvc\html\events\OnplayingTrait;
use pvc\html\events\OnprogressTrait;
use pvc\html\events\OnratechangeTrait;
use pvc\html\events\OnresetTrait;
use pvc\html\events\OnresizeTrait;
use pvc\html\events\OnscrollTrait;
use pvc\html\events\OnscrollendTrait;
use pvc\html\events\OnsecuritypolicyviolationTrait;
use pvc\html\events\OnseekedTrait;
use pvc\html\events\OnseekingTrait;
use pvc\html\events\OnselectTrait;
use pvc\html\events\OnslotchangeTrait;
use pvc\html\events\OnstalledTrait;
use pvc\html\events\OnsubmitTrait;
use pvc\html\events\OnsuspendTrait;
use pvc\html\events\OntimeupdateTrait;
use pvc\html\events\OntoggleTrait;
use pvc\html\events\OnvolumechangeTrait;
use pvc\html\events\OnwaitingTrait;
use pvc\html\events\OnwebkitanimationendTrait;
use pvc\html\events\OnwebkitanimationiterationTrait;
use pvc\html\events\OnwebkitanimationstartTrait;
use pvc\html\events\OnwebkittransitionendTrait;
use pvc\html\events\OnwheelTrait;
use pvc\interfaces\validator\ValTesterInterface;

/**
 * class ElementVoid
 *
 * Base class for all html elements in the pvc framework.  This class produces and
 * handles html5 compliant tags / code.
 *
 * This class is for "voidElements" or "empty" tags, which are tags that do not have closing tags (e.g. "<br>" or "<col>")
 */
class ElementVoid implements ElementVoidInterface
{
    /**
     * @var string
     */
    protected(set) string $name;

    /**
     * @var HtmlFactory
     */
    protected HtmlFactory $htmlFactory;

    /**
     * @var array<string>
     */
    protected array $allowedAttributes = [];

    /**
     * @var array<string, AttributeInterface>
     */
    protected array $attributes = [];

    /**
     * @var array<string> 
     */
    protected array $allowedEvents = [
        'onabort',
        'onauxclick',
        'onbeforeinput',
        'onbeforematch',
        'onbeforetoggle',
        'onblur',
        'oncancel',
        'oncanplay',
        'oncanplaythrough',
        'onchange',
        'onclick',
        'onclose',
        'oncontextlost',
        'oncontextmenu',
        'oncontextrestored',
        'oncopy',
        'oncuechange',
        'oncut',
        'ondblclick',
        'ondrag',
        'ondragend',
        'ondragenter',
        'ondragleave',
        'ondragover',
        'ondragstart',
        'ondrop',
        'ondurationchange',
        'onemptied',
        'onended',
        'onerror',
        'onfocus',
        'onformdata',
        'oninput',
        'oninvalid',
        'onkeydown',
        'onkeypress',
        'onkeyup',
        'onload',
        'onloadeddata',
        'onloadedmetadata',
        'onloadstart',
        'onmousedown',
        'onmouseenter',
        'onmouseleave',
        'onmousemove',
        'onmouseout',
        'onmouseover',
        'onmouseup',
        'onpaste',
        'onpause',
        'onplay',
        'onplaying',
        'onprogress',
        'onratechange',
        'onreset',
        'onresize',
        'onscroll',
        'onscrollend',
        'onsecuritypolicyviolation',
        'onseeked',
        'onseeking',
        'onselect',
        'onslotchange',
        'onstalled',
        'onsubmit',
        'onsuspend',
        'ontimeupdate',
        'ontoggle',
        'onvolumechange',
        'onwaiting',
        'onwebkitanimationend',
        'onwebkitanimationiteration',
        'onwebkitanimationstart',
        'onwebkittransitionend',
        'onwheel'
    ];
    
    /**
     * @param  string  $name
     * @param array<string> $allowedAttributes
     * @param  HtmlFactory  $htmlFactory
     */
    public function __construct(
        string $name,
        array $allowedAttributes,
        HtmlFactory $htmlFactory,
        protected(set) ContentModel $contentModel
    )
    {
        $this->name = $name;
        foreach($allowedAttributes as $attribute) {
            $this->allowedAttributes[] = $attribute;
        }
        $this->htmlFactory = $htmlFactory;
    }

    /**
     * @param  string  $attribute
     *
     * @return bool
     */
    private function isAllowedAttribute(string $attribute): bool
    {
        return in_array($attribute, array_merge(GlobalAttributes::attributeNames(), $this->allowedAttributes, $this->allowedEvents));
    }
    
    private function getAttribute(string $attribute): ?AttributeInterface
    {
        return $this->attributes[$attribute] ?? null;
    }

    /**
     * @param  string  $name
     * @param string|int|bool ...$values
     *
     * @return ElementVoidInterface
     * @throws InvalidAttributeException
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function setAttribute(string $name, ...$values): ElementVoidInterface
    {
        if (!$this->isAllowedAttribute($name)) {
            throw new InvalidAttributeException($name);
        }
        if (!$attribute = $this->getAttribute($name)) {
            $attribute = $this->htmlFactory->makeAttribute($name);
            $this->attributes[$name] = $attribute;
        }
        $attribute->setValue(...$values);
        return $this;
    }

    /**
     * @inheritDoc
     * @throws GetDataTypeException
     */
    public function addCustomData(
        string $name,
        ?string $valueType = null,
        ?bool $caseSensitive = null,
        ?ValTesterInterface $tester = null,
    ): void
    {
        /**
         * ensure customData with the same name does not already exist
         */
        if ($this->getAttribute($name)) {
            /**
             * throw a warning
             */
            trigger_error('Attribute ' . $name . ' is already defined', E_USER_WARNING);
        }

        /**
         * convert $valueType to an enum if it is not null
         */

        /**
         * coalesce valueType to a string because AttributeValueDataType::tryFrom method does not accept null
         */
        $valueType = $valueType ?? '';
        $type = AttributeValueDataType::tryFrom($valueType);
        if (!empty($valueType) && is_null($type)) {
            throw new GetDataTypeException($valueType);
        }

        $attribute = $this->htmlFactory->makeCustomData(
            $name,
            $type,
            $caseSensitive,
            $tester,
        );
        $this->attributes[$name] = $attribute;
    }

    /**
     * removeAttribute
     *
     * @param string  $name
     */
    public function removeAttribute(string $name): void
    {
        unset($this->attributes[$name]);
    }

    /**
     * @return array<AttributeInterface>
     */
    public function getAttributes(): array
    {
        return $this->attributes;
    }

    /**
     * @return array
     * void elements cannot have child nodes, but this stub makes it easier
     * to traverse a DOM tree without having to differentiate between void
     * elements and containing elements
     */
    public function getNodes(): array
    {
        return [];
    }

    /**
     * generateOpeningTag
     *
     * @return string
     */
    public function generateOpeningTag(): string
    {
        $z = '<' . $this->name;

        $callback = function (AttributeInterface $attribute) {
            return $attribute->render();
        };

        $attributes = implode(' ', array_map($callback, $this->attributes));

        $z .= (strlen($attributes) > 0) ? ' ' . $attributes : '';
        $z .= '>';
        return $z;
    }

    /**
     * global attributes
     */
    use AccesskeyTrait;
    use ClassTrait;
    use ContenteditableTrait;
    use DirTrait;
    use DraggableTrait;
    use EnterkeyhintTrait;
    use HiddenTrait;
    use IdTrait;
    use InertTrait;
    use InputmodeTrait;
    use LangTrait;
    use PopoverTrait;
    use SpellcheckTrait;
    use TabindexTrait;
    use TranslateTrait;

    /**
     * events
     */
    use OnabortTrait;
    use OnauxclickTrait;
    use OnbeforeinputTrait;
    use OnbeforematchTrait;
    use OnbeforetoggleTrait;
    use OnblurTrait;
    use OncancelTrait;
    use OncanplayTrait;
    use OncanplaythroughTrait;
    use OnchangeTrait;
    use OnclickTrait;
    use OncloseTrait;
    use OncontextlostTrait;
    use OncontextmenuTrait;
    use OncontextrestoredTrait;
    use OncopyTrait;
    use OncuechangeTrait;
    use OncutTrait;
    use OndblclickTrait;
    use OndragTrait;
    use OndragendTrait;
    use OndragenterTrait;
    use OndragleaveTrait;
    use OndragoverTrait;
    use OndragstartTrait;
    use OndropTrait;
    use OndurationchangeTrait;
    use OnemptiedTrait;
    use OnendedTrait;
    use OnerrorTrait;
    use OnfocusTrait;
    use OnformdataTrait;
    use OninputTrait;
    use OninvalidTrait;
    use OnkeydownTrait;
    use OnkeypressTrait;
    use OnkeyupTrait;
    use OnloadTrait;
    use OnloadeddataTrait;
    use OnloadedmetadataTrait;
    use OnloadstartTrait;
    use OnmousedownTrait;
    use OnmouseenterTrait;
    use OnmouseleaveTrait;
    use OnmousemoveTrait;
    use OnmouseoutTrait;
    use OnmouseoverTrait;
    use OnmouseupTrait;
    use OnpasteTrait;
    use OnpauseTrait;
    use OnplayTrait;
    use OnplayingTrait;
    use OnprogressTrait;
    use OnratechangeTrait;
    use OnresetTrait;
    use OnresizeTrait;
    use OnscrollTrait;
    use OnscrollendTrait;
    use OnsecuritypolicyviolationTrait;
    use OnseekedTrait;
    use OnseekingTrait;
    use OnselectTrait;
    use OnslotchangeTrait;
    use OnstalledTrait;
    use OnsubmitTrait;
    use OnsuspendTrait;
    use OntimeupdateTrait;
    use OntoggleTrait;
    use OnvolumechangeTrait;
    use OnwaitingTrait;
    use OnwebkitanimationendTrait;
    use OnwebkitanimationiterationTrait;
    use OnwebkitanimationstartTrait;
    use OnwebkittransitionendTrait;
    use OnwheelTrait;
}
