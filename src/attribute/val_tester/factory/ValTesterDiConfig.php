<?php

/**
 * @author: Doug Wilbourne (dougwilbourne@gmail.com)
 */

declare (strict_types=1);

namespace pvc\html\attribute\factory;

use pvc\filtervar\FilterVarValidateUrl;
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

/**
 * configuration for a dependency injection valtesterContainer for the value testers for attributes. The key of each entry
 * is the attribute name.  If there is no value tester explicitly defined for the attribute, the code default
 * to a value tester which always returns true.
 */

return [

    /**
     * the following are the global attributes, which are available in any html tag
     */
    'accessKey' => create(RegexTester::class)->method('setRegex', get(RegexAccessKey::class)),
    'cssClass' => create(RegexTester::class)->method('setRegex', get(RegexCssClass::class)),
    'contentEditable' => create(RegexTester::class)->method('setRegex', get(RegexTrueFalse::class)),
    'textDirection' => create(RegexTester::class)->method('setRegex', get(RegexTextDirection::class)),
    'draggable' => create(RegexTester::class)->method('setRegex', get(RegexTrueFalse::class)),
    'hidden' => create(RegexTester::class)->method('setRegex', get(RegexTrueFalse::class)),
    'id' => create(RegexTester::class)->method('setRegex', get(RegexId::class)),
    'lang' => create(CallableTester::class)->method('setCallable', get(CallableTesterLang::class)),
    'spellCheck' => create(RegexTester::class)->method('setRegex', get(RegexTrueFalse::class)),
    /**
     * dunno about validating css.....
     */
    'style' => create(ValTesterAlwaysTrue::class),
    'tabIndex' => create(RegexTester::class)->method('setRegex', get(RegexTabIndex::class)),
    'title' => create(CallableTester::class)->method('setCallable', get(CallableTesterTitle::class)),
    'translate' => create(RegexTester::class)->method('setRegex', get(RegexYesNo::class)),
    /**
     * end of global attributes
     */

    /**
     * custom data attribute creation.  Note that this is not for validating the value of a custom attribute, this
     * validates the *name* of the custom attribute.
     */
    'customDataName' => create(RegexTester::class)->method('setRegex', get(RegexCustomDataName::class)),
    /**
     * end of creating custom data attributes
     */

    /**
     * below are all the other attributes in html that can be used in various tags
     */
    'href' => create(FilterVarTester::class)->method('setFilterVar', get(FilterVarValidateUrl::class)),
    'hrefLang' => create(CallableTester::class)->method('setCallable', get(CallableTesterLang::class)),
    'targetChoices' => ['_blank', '_parent', '_self', '_top'],
    'target' => create(ListChoiceValTester::class)->method('setChoices', get('targetChoices')),
    /**
     * the windows operating systems are the most restrictive in terms of length and characters allowed, so if
     * the filename is good enough for windows, it ought to be ok on Mac and Unix also
     */
    'download' => create(RegexTester::class)->method('setRegex', get(RegexWindowsFilename::class)),
    /**
     * 'media' attribute value is a media query, a string indicating the media type of the link (which is
     * the successor to mime-type and includes all the old mime-types)
     *
     * don't really want to get into validating the value.  There's a good reference on acceptable values at
     * https://www.dofactory.com/html/attributes/media
     */

    'media' => create(ValTesterAlwaysTrue::class),
    'ping' => create(FilterVarTester::class)->method('setFilterVar', get(FilterVarValidateUrl::class)),

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
    'referrerPolicy' => create(ListChoiceValTester::class)->method('setChoices', get('validPolicies')),

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
    'rel' => create(ListChoiceValTester::class)->method('setChoices', get('relChoices')),
    'type' => create(CallableTester::class)->method('setCallable', get(CallableTesterType::class)),
    'alt' => create(CTypeTesterPrintable::class),

    'xmlnsChoices' => ['http://www.w3.org/1999/xhtml'],
    'xmlns' => create(ListChoiceValTester::class)->method('setChoices', get('xmlnsChoices')),

    'crossOriginChoices' => ['anonymous', 'usecredentials'],
    'crossOrigin' => create(ListChoiceValTester::class)->method('setChoices', get('crossOriginChoices')),
    'sizes' => create(RegexTester::class)->method('setRegex', get(RegexSizes::class)),
];
