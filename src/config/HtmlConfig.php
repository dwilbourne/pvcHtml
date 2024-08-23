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
 * Class AttributeConfig
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
        'hidden',
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
        //TODO: find a definitive list of these elements that 'accept more than one value'
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
    protected static array $voidTags = [
        'area',
        'base',
        'br',
        'col',
        'command',
        'embed',
        'hr',
        'img',
        'input',
        'keygen',
        'link',
        'meta',
        'param',
        'src',
        'track',
        'wbr',
    ];

    /**
     * @var array<string, array<string>>
     * This array is used for two purposes.  When making a new Tag (or TagVoid), TagFactory will check here
     * to make sure that the tag name appears as a key in this array.  If the key does not exist, we throw an
     * exception because either the tag name is invalid or we do not have any configuration information for this
     * tag.  Thus, even if a tag has no attributes beyond the global attributes, you still need an entry with an
     * empty array.
     *
     * We also use it to validate attribute names that you are trying to add to a tag.  If the value that corresponds
     * to the tag name key is an empty array, then the tag can use the global attributes but no others.  If the
     * array is non-empty, then the tag supports the global attributes plus whatever attribute names appear in the
     * array.  Recall that all html tags support the global attributes, although there are cases where specifying a
     * global attribute for a tag will have no effect.
     *
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
     * @var array<string, array<string, string>>
     *
     * This array is used to automatically generate any required attributes for a given tag.  So the array
     * keys consist of tag names which have required attributes and the values are arrays which specify
     * [attribute name => default value] pairs for the required attributes.
     */
    protected static array $requiredAttributes = [
        'html' => ['lang' => 'en'],
        'head' => ['title' => 'title'],
    ];

    /**
     * @var array|string[]
     * these elements are inline by default.  The Tag object will not let you put a block element inside one of these
     */
    protected static array $inlineElements = [
        'a',
        'abbr',
        'acronym',
        'b',
        'bdo',
        'big',
        'br',
        'button',
        'cite',
        'code',
        'dfn',
        'em',
        'i',
        'img',
        'input',
        'kbd',
        'label',
        'map',
        'object',
        'output',
        'q',
        'samp',
        'script',
        'select',
        'small',
        'span',
        'strong',
        'sub',
        'sup',
        'textarea',
        'time',
        'tt',
        'var',
    ];

    /**
     * @var array<string, array<string, bool>>
     * this array aims to define what tags are allowed and potentially required inside a parent tag.  A value
     * of true indicates the subtag is required, a value of false indicates the subtag is allowed but is not required.
     *
     * If a tag has no entry in this array, then it is assumed that any tag can be nested within, except when it
     * breaks the rule of trying to put a block level element into an inline element (and obviously void tags
     * have no inner html).
     *
     * When TagFactory makes a tag, it automatically generates the required subtags.  If you add additional subtags,
     * they must be listed in the array corresponding to the parent (and they must be listed as optional - you
     * cannot duplicate required tags).  They will be rendered in the order specified in this array.  If you add a
     * subtag of the same type multiple times (think, for instance, of a <tr> element), those tags will be rendered
     * in the order in which you add them to the parent element.
     */
    protected static array $innerTags = [
        'html' => [
            'head' => true,
            'body' => true,
            'footer' => false,
        ],
        'head' => [
            'title' => true,
            'style' => false,
            'base' => false,
            'link' => false,
            'meta' => false,
            'script' => false,
            'noscript' => false,
        ]
    ];

    /**
     * @var array<string>
     * There are some tags where you do not want to allow text (or Message objects, which become text when rendered)
     * in between the opening and closing tags.  These tags are used as containers for DOM objects which have more
     * complex and/or iterative structures.
     */
    protected static array $noInnerText = [
        'html',
        'head',
        'select',
        'table',
        'tr',
    ];

    /**
     * isTagVoid
     * @param string $tagName
     * @return bool
     */
    public static function isTagVoid(string $tagName): bool
    {
        return in_array($tagName, self::$voidTags);
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
     * isRequiredSubtag
     * @param string $subTagName
     * @param string $parentTagName
     * @return bool
     */
    public static function isRequiredSubtag(string $subTagName, string $parentTagName): bool
    {
        /**
         * if the key does not exist, then the parent tag has no required or optional subtags, e.g. you can nest
         * any tag you want into this tag
         */
        if (!key_exists($parentTagName, self::$innerTags)) {
            return false;
        }

        $innerTags = self::$innerTags[$parentTagName];
        /**
         * If the subtag is not in the list, not only is the tag not required, it is not allowed!
         */
        if (!key_exists($subTagName, $innerTags)) {
            return false;
        }

        /**
         * if the value of the subtag key is true, it is required, otherwise it is not required.
         */
        return $innerTags[$subTagName];
    }

    public static function getRequiredSubtags(string $parentTagName): array
    {
        $allowedSubtags = self::$innerTags[$parentTagName] ?? [];
        /**
         * @param bool $subtagValue
         * @return bool
         * subtag values are boolean, a value of true means the subtag is required
         */
        $closure = function (bool $subtagValue) {
            return $subtagValue;
        };
        return array_keys(array_filter($allowedSubtags, $closure));
    }

    public static function isValidSubtag(string $subTagName, string $parentTagName): bool
    {
        /**
         * if the key does not exist, then the parent tag has no required or optional subtags, e.g. you can nest
         * any tag you want into this parent tag.  So the subtag is allowed.
         */
        if (!key_exists($parentTagName, self::$innerTags)) {
            return true;
        }

        $innerTags = self::$innerTags[$parentTagName];
        /**
         * If the subtag is not in the list, it is not allowed
         */
        if (!key_exists($subTagName, $innerTags)) {
            return false;
        }

        /**
         * otherwise it is ok
         */
        return true;
    }

    public static function isInlineElement(string $tagName): bool
    {
        return in_array($tagName, self::$inlineElements);
    }

    public static function isBlockElement(string $tagName): bool
    {
        return (self::isValidTagName($tagName) && !self::isInlineElement($tagName));
    }

    public static function innerTextNotAllowed(string $tagName): bool
    {
        return in_array($tagName, self::$noInnerText);
    }

    /**
     * exists
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
     * isVoidAttribute
     * @param string $attrName
     * @return bool
     */
    public static function isVoidAttribute(string $attrName): bool
    {
        return in_array($attrName, self::$voidAttributes);
    }

    /**
     * globalAttributes
     * @return array<int, string>
     */
    public static function getGlobalAttributes(): array
    {
        return array_keys(self::$globalAttributes);
    }

    /**
     * getRequiredAttributes
     * @param string $tagName
     * @return array|string[]
     */
    public static function getRequiredAttributes(string $tagName): array
    {
        return self::$requiredAttributes[$tagName] ?? [];
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
