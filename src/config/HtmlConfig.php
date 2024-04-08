<?php

/**
 * @author: Doug Wilbourne (dougwilbourne@gmail.com)
 */

declare(strict_types=1);

namespace pvc\html\config;

use pvc\html\attribute\abstract\AttributeMultiValue;
use pvc\html\attribute\abstract\AttributeSingleValue;
use pvc\html\attribute\abstract\AttributeVoid;
use pvc\html\attribute\abstract\Event;

/**
 * Class HtmlConfig
 */
class HtmlConfig
{
    /**
     * @var array<string, string>
     * global attributes in alphabetical order
     */
    protected static array $globalAttributes = [
        'accesskey' => AttributeSingleValue::class,
        'class' => AttributeMultiValue::class,
        'contenteditable' => AttributeSingleValue::class,
        'dir' => AttributeSingleValue::class,
        'draggable' => AttributeSingleValue::class,
        'hidden' => AttributeVoid::class,
        'id' => AttributeSingleValue::class,
        'lang' => AttributeSingleValue::class,
        'spellcheck' => AttributeSingleValue::class,
        'style' => AttributeSingleValue::class,
        'tabindex' => AttributeSingleValue::class,
        'title' => AttributeSingleValue::class,
        'translate' => AttributeSingleValue::class,
    ];

    /**
     * @var array<string>
     * void attributes in alphabetical order
     */
    protected static array $voidAttributes = [
        'allowfullscreen',
        'async',
        'autofocus',
        'autoplay',
        'checked',
        'controls',
        'default',
        'defer',
        'disabled',
        'formnovalidate',
        'inert',
        'ismap',
        'itemscope',
        'loop',
        'multiple',
        'muted',
        'nomodule',
        'novalidate',
        'open',
        'playsinline',
        'readonly',
        'required',
        'reversed',
        'selected',
    ];


    /**
     * single value attributes in alphabetical order
     */
    /**
     * @var array<string>
     */
    protected static array $singleValueAttributes = [
        'alt',
        'crossorigin',
        'download',
        'href',
        'hreflang',
        'media',
        'ping',
        'referrerpolicy',
        'rel',
        'sizes',
        'target',
        'type',
        'xmlns',
    ];

    /**
     * @var array<string>
     * multi value attributes in alphabetical order
     */
    protected static array $multiValueAttributes = [
        //TODO: find a definitive list of these elements that 'accept more than once value'
        'rel',
        'rev',
    ];

    /**
     * @var array<string>
     */
    protected static array $events = [
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
     * @var array<string>
     * This array is the list of void tags that the library knows how to build.  If a tag name is valid and it does
     * not appear in this list, then the code assumes it is a 'regular' html construct with a closing tag.
     */
    protected static array $voids = [
        'base',
        'img',
        'link',
    ];

    /**
     * @var array<string, array<string>>
     * This array is used both to both validate tag names and attribute names.  Thus, even if a tag has no attributes
     * beyond the global attributes, you still need an entry with an empty array.
     */
    protected static array $tagAttributes = [
        'base' => ['href', 'target'],
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
        'a' => ['download', 'href', 'hreflang', 'media', 'ping', 'referrerpolicy', 'rel', 'target', 'type'],
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
        'span' => [],
        'style' => ['media', 'type'],
        'title' => [],
    ];

    /**
     * isTagVoid
     * @param string $tagName
     * @return bool
     */
    public static function isTagVoid(string $tagName): bool
    {
        return in_array($tagName, self::$voids);
    }

    /**
     * isValidTagName
     * @param string $tagName
     * @return bool
     */
    public static function isValidTagName(string $tagName): bool
    {
        return key_exists($tagName, self::$tagAttributes);
    }

    /**
     * isValidAttribute
     * @param string $tagName
     * @param string $attributeName
     * @return bool
     */
    public static function isValidAttribute(string $tagName, string $attributeName): bool
    {
        $validAttributes = array_merge(self::getGlobalAttributes(), self::$tagAttributes[$tagName]);
        return in_array($attributeName, $validAttributes);
    }

    /**
     * getGlobalAttributes
     * @return array<int, string>
     */
    public static function getGlobalAttributes(): array
    {
        return array_keys(self::$globalAttributes);
    }

    /**
     * isValidAttributeName
     * @param string $attributeName
     * @return bool
     * returns true for events as well as 'attributes'
     */
    public static function isValidAttributeName(string $attributeName): bool
    {
        return key_exists($attributeName, self::canonicalizeAttributeNameTypes());
    }

    /**
     * canonicalizeAttributeNameTypes
     * @return array<string, string>
     */
    protected static function canonicalizeAttributeNameTypes(): array
    {
        $voids = array_fill_keys(self::$voidAttributes, AttributeVoid::class);
        $singles = array_fill_keys(self::$singleValueAttributes, AttributeSingleValue::class);
        $multi = array_fill_keys(self::$multiValueAttributes, AttributeMultiValue::class);
        $events = array_fill_keys(self::$events, Event::class);

        return array_merge(self::$globalAttributes, $voids, $singles, $multi, $events);
    }

    /**
     * isValidEventName
     * @param string $eventName
     * @return bool
     */
    public static function isValidEventName(string $eventName): bool
    {
        return in_array($eventName, self::$events);
    }


    /**
     * getAttributeType
     * @param string $attributeName
     * @return string|null
     */
    public static function getAttributeType(string $attributeName): string|null
    {
        $canonicalized = self::canonicalizeAttributeNameTypes();
        return $canonicalized[$attributeName] ?? null;
    }
}
