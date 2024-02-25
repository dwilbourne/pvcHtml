<?php

/**
 * @author: Doug Wilbourne (dougwilbourne@gmail.com)
 */

declare (strict_types=1);

namespace pvc\html\attribute\factory;

use pvc\filtervar\FilterVarValidateUrl;
use pvc\html\attribute\abstract\AttributeMultiValue;
use pvc\html\attribute\abstract\AttributeSingleValue;
use pvc\html\attribute\abstract\AttributeVoid;
use pvc\html\attribute\val_tester\callable\CallableTesterEventScript;
use pvc\html\attribute\val_tester\callable\CallableTesterLang;
use pvc\html\attribute\val_tester\callable\CallableTesterTitle;
use pvc\html\attribute\val_tester\callable\CallableTesterType;
use pvc\html\attribute\val_tester\regex\RegexAccessKey;
use pvc\html\attribute\val_tester\regex\RegexCssClass;
use pvc\html\attribute\val_tester\regex\RegexCustomDataName;
use pvc\html\attribute\val_tester\regex\RegexId;
use pvc\html\attribute\val_tester\regex\RegexSizes;
use pvc\html\attribute\val_tester\regex\RegexTabIndex;
use pvc\html\attribute\val_tester\regex\RegexTextDirection;
use pvc\regex\boolean\RegexTrueFalse;
use pvc\regex\boolean\RegexYesNo;
use pvc\regex\filename\RegexWindowsFilename;
use pvc\validator\val_tester\always_true\ValTesterAlwaysTrue;
use pvc\validator\val_tester\callable\CallableTester;
use pvc\validator\val_tester\ctype\CTypeTesterPrintable;
use pvc\validator\val_tester\filter_var\FilterVarTester;
use pvc\validator\val_tester\list_choice\ListChoiceValTester;
use pvc\validator\val_tester\regex\RegexTester;

use function DI\create;
use function DI\get;

return [

    /**
     * the following are the global attributes, which are available in any html tag
     */
    'valtesterAccessKey' => create(RegexTester::class)->method('setRegex', get(RegexAccessKey::class)),
    'accesskey' => create(AttributeSingleValue::class)->constructor('accesskey')->method(
        'setTester',
        get('valtesterAccessKey')
    ),

    'valtesterCssClass' => create(RegexTester::class)->method('setRegex', get(RegexCssClass::class)),
    'class' => create(AttributeMultiValue::class)->constructor('class')->method('setTester', get('valtesterCssClass')),

    'valtesterContentEditable' => create(RegexTester::class)->method('setRegex', get(RegexTrueFalse::class)),
    'contenteditable' => create(AttributeSingleValue::class)->constructor('contenteditable')->method(
        'setTester',
        get(
            'valtesterContentEditable'
        )
    ),

    'valtesterTextDirection' => create(RegexTester::class)->method('setRegex', get(RegexTextDirection::class)),
    'dir' => create(AttributeSingleValue::class)->constructor('dir')->method(
        'setTester',
        get('valtesterTextDirection')
    ),

    'valtesterDraggable' => create(RegexTester::class)->method('setRegex', get(RegexTrueFalse::class)),
    'draggable' => create(AttributeSingleValue::class)->constructor('draggable')->method(
        'setTester',
        get('valtesterDraggable')
    ),

    'hidden' => create(AttributeVoid::class)->constructor('hidden'),

    'valtesterId' => create(RegexTester::class)->method('setRegex', get(RegexId::class)),
    'id' => create(AttributeSingleValue::class)->constructor('id')->method('setTester', get('valtesterId')),

    'valtesterLang' => create(CallableTester::class)->method('setCallable', get(CallableTesterLang::class)),
    'lang' => create(AttributeSingleValue::class)->constructor('lang')->method('setTester', get('valtesterLang')),

    'valtesterSpellCheck' => create(RegexTester::class)->method('setRegex', get(RegexTrueFalse::class)),
    'spellcheck' => create(AttributeSingleValue::class)->constructor('spellcheck')->method(
        'setTester',
        get('valtesterSpellCheck')
    ),

    /**
     * dunno about validating css.....
     */
    'valtesterStyle' => create(ValTesterAlwaysTrue::class),
    'style' => create(AttributeSingleValue::class)->constructor('style')->method('setTester', get('valtesterStyle')),

    'valtesterTabIndex' => create(RegexTester::class)->method('setRegex', get(RegexTabIndex::class)),
    'tabindex' => create(AttributeSingleValue::class)->constructor('tabindex')->method(
        'setTester',
        get('valtesterTabIndex')
    ),

    'valtesterTitle' => create(CallableTester::class)->method('setCallable', get(CallableTesterTitle::class)),
    'title' => create(AttributeSingleValue::class)->constructor('title')->method('setTester', get('valtesterTitle')),

    'valtesterTranslate' => create(RegexTester::class)->method('setRegex', get(RegexYesNo::class)),
    'translate' => create(AttributeSingleValue::class)->constructor('translate')->method(
        'setTester',
        get('valtesterTranslate')
    ),

    /**
     * end of global attributes
     */

    /**
     * EventFactory configuration
     */

    'callableTesterEventScript' => create(CallableTesterEventScript::class),
    'eventScriptTester' => create(CallableTester::class)->method('setCallable', get('callableTesterEventScript')),

    EventFactory::class => create(EventFactory::class)->constructor(get('eventScriptTester')),

    /**
     * end of creating events
     */

    /**
     * custom data attribute creation
     */

    'valtesterCustomDataName' => create(RegexTester::class)->method('setRegex', get(RegexCustomDataName::class)),
    CustomDataAttributeFactory::class => create()->constructor(get('valtesterCustomDataName')),

    /**
     * end of creating custom data attributes
     */

    /**
     * below are all the other attributes in html that can be used in various tags
     */

    'valtesterUrl' => create(FilterVarTester::class)->method('setFilterVar', get(FilterVarValidateUrl::class)),
    'href' => create(AttributeSingleValue::class)->constructor('href')->method('setTester', get('valtesterUrl')),

    'valtesterHrefLang' => create(CallableTester::class)->method('setCallable', get(CallableTesterLang::class)),
    'hreflang' => create(AttributeSingleValue::class)->constructor('hreflang')->method(
        'setTester',
        get('valtesterHrefLang')
    ),

    'targetChoices' => ['_blank', '_parent', '_self', '_top'],
    'valtesterTarget' => create(ListChoiceValTester::class)->method('setChoices', get('targetChoices')),
    'target' => create(AttributeSingleValue::class)->constructor('target')->method('setTester', get('valtesterTarget')),

    /**
     * the windows operating systems are the most restrictive in terms of length and characters allowed, so if
     * the filename is good enough for windows, it ought to be ok on Mac and Unix also
     */
    'valtesterDownload' => create(RegexTester::class)->method('setRegex', get(RegexWindowsFilename::class)),
    'download' => create(AttributeSingleValue::class)->constructor('download')->method(
        'setTester',
        get('valtesterDownload')
    ),

    /**
     * 'media' attribute value is a media query, a string indicating the media type of the link (which is
     * different from a mime-type!)
     *
     * don't really want to get into validating the value.  There's a good reference on acceptable values at
     *  https://www.dofactory.com/html/attributes/media
     */

    'valtesterMedia' => create(ValTesterAlwaysTrue::class),
    'media' => create(AttributeSingleValue::class)->constructor('media')->method('setTester', get('valtesterMedia')),

    'ping' => create(AttributeSingleValue::class)->constructor('ping')->method('setTester', get('valtesterUrl')),

    'validPolicies' => [
        'no-referrer',
        'no-referrer-when-downgrade',
        'origin',
        'origin-when-cross-origin',
        'same-origin',
        'strict-origin',
        'strict-origin-when-cross-origin',
        'unsafe-url'
    ],
    'valtesterReferrerPolicy' => create(ListChoiceValTester::class)->method('setChoices', get('validPolicies')),
    'referrerpolicy' => create(AttributeSingleValue::class)->constructor('referrerpolicy')->method(
        'setTester',
        get('valtesterReferrerPolicy')
    ),


    'relChoices' => [
        'nofollow',
        'noopener',
        'noreferrer',
        'stylesheet',
        'icon',
        'canonical',
        'dns-prefetch',
        'external',
        'author',
        'help',
        'license',
        'prev',
        'next',
        'bookmark',
        'search',
        'alternate',
        'tag',
    ],
    'valtesterRel' => create(ListChoiceValTester::class)->method('setChoices', get('relChoices')),
    'rel' => create(AttributeSingleValue::class)->constructor('rel')->method('setTester', get('valtesterRel')),

    'valtesterType' => create(CallableTester::class)->method('setCallable', get(CallableTesterType::class)),
    'type' => create(AttributeSingleValue::class)->constructor('type')->method('setTester', get('valtesterType')),

    'valtesterAlt' => create(CTypeTesterPrintable::class),
    'alt' => create(AttributeSingleValue::class)->constructor('alt')->method('setTester', get('valtesterAlt')),

    'xmlnsChoices' => ['http://www.w3.org/1999/xhtml'],
    'valtesterXmlns' => create(ListChoiceValTester::class)->method('setChoices', get('xmlnsChoices')),
    'xmlns' => create(AttributeSingleValue::class)->constructor('xmlns')->method('setTester', get('valtesterXmlns')),

    'crossOriginChoices' => ['anonymous', 'usecredentials'],
    'valtesterCrossOrigin' => create(ListChoiceValTester::class)->method('setChoices', get('crossOriginChoices')),
    'crossorigin' => create(AttributeSingleValue::class)->constructor('crossorigin')->method(
        'setTester',
        get('valtesterCrossOrigin')
    ),

    'valtesterSizes' => create(RegexTester::class)->method('setRegex', get(RegexSizes::class)),
    'sizes' => create(AttributeSingleValue::class)->constructor('sizes')->method('setTester', get('valtesterSizes')),
];
