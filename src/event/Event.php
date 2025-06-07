<?php

/**
 * @author: Doug Wilbourne (dougwilbourne@gmail.com)
 */
declare(strict_types=1);

namespace pvc\html\event;

use pvc\html\attribute\AttributeSingleValue;
use pvc\interfaces\html\attribute\EventInterface;

/**
 * Class Event
 */
class Event extends AttributeSingleValue implements EventInterface
{
    private array $eventNames = [
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
        'onwheel',
    ];

    /**
     * isValidAttributeName
     * @param string $name
     * @return bool
     * need to override the testing of the attribute id in the AttributeVoid class because javascript
     * event names are lower case, alphabetic only.  This is different from the restrictions placed on all other
     * attribute names.
     */
    protected function isValidAttributeIdName(string $name): bool
    {
        $pattern = '/^[a-z]*$/';
        return (bool) preg_match($pattern, $name);
    }

    /**
     * setScript
     * @param string $script
     */
    public function setScript(string $script): void
    {
        $this->setValue($script);
    }

    /**
     * getScript
     * @return ?string
     */
    public function getScript(): ?string
    {
        return $this->getValue() ?? null;
    }

    /**
     * render
     * @return string
     */
    public function render(): string
    {
        if ($script = $this->getScript()) {
            return $this->getName() . "='" . $script . "'";
        }
        return '';
    }
}