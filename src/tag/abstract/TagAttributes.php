<?php

/**
 * @author: Doug Wilbourne (dougwilbourne@gmail.com)
 */
declare(strict_types=1);

namespace pvc\html\tag\abstract;

/**
 * Class TagAttributes
 */
class TagAttributes
{
    /**
     * @var array<string, array<string>>
     */
    protected static array $tagAttributes = [
        'a' => ['download', 'href', 'hreflang', 'media', 'ping', 'referrerpolicy', 'rel', 'target', 'type'],
        'base' => ['href', 'target'],
        'body' => [],
        'div' => [],
        'footer' => [],
        'form' => [
            'acceptcharset',
            'action',
            'autocomplete',
            'enctype',
            'method',
            'name',
            'novalidate',
            'rel',
            'target'
        ],
        'head' => [],
        'html' => ['xmlns'],
        'img' => [
            'alt',
            'crossorigin',
            'height',
            'ismap',
            'loading',
            'longdesc',
            'referrerpolicy',
            'sizes',
            'src',
            'srcset',
            'usemap',
            'width'
        ],
        'link' => ['crossorigin', 'href', 'hreflang', 'media', 'referrerpolicy', 'rel', 'sizes', 'title', 'type'],
        'span' => [],
        'style' => ['media', 'type'],
        'title' => [],
    ];

    /**
     * @var array<string>
     */
    protected static array $globalAttributes = [
        'accesskey',
        'class',
        'contenteditable',
        'dir',
        'draggable',
        'hidden',
        'id',
        'lang',
        'spellcheck',
        'style',
        'tabindex',
        'title',
        'translate',
    ];

    /**
     * @var string[]
     */
    protected static array $eventList = [
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
     * isValidAttribute
     * @param string $tagName
     * @param string $attributeName
     * @return bool
     */
    public static function isValidAttribute(string $tagName, string $attributeName): bool
    {
        $validAttributes = array_merge(self::$globalAttributes, self::$tagAttributes[$tagName]);
        return in_array($attributeName, $validAttributes);
    }

    /**
     * isValidEvent
     * @param string $eventName
     * @return bool
     */
    public static function isValidEvent(string $eventName): bool
    {
        return in_array($eventName, self::$eventList);
    }
}