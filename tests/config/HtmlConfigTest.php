<?php

/**
 * @author: Doug Wilbourne (dougwilbourne@gmail.com)
 */
declare (strict_types=1);

namespace pvcTests\html\config;

use PHPUnit\Framework\TestCase;
use pvc\html\attribute\abstract\AttributeMultiValue;
use pvc\html\attribute\abstract\AttributeSingleValue;
use pvc\html\attribute\abstract\AttributeVoid;
use pvc\html\attribute\abstract\Event;
use pvc\html\config\HtmlConfig;

class HtmlConfigTest extends TestCase
{

    /**
     * testIsTagVoid
     * @covers \pvc\html\config\HtmlConfig::isTagVoid
     */
    public function testIsTagVoid(): void
    {
        self::assertTrue(HtmlConfig::isTagVoid('img'));
        self::assertFalse(HtmlConfig::isTagVoid('div'));
    }

    /**
     * testIsValidTagName
     * @covers \pvc\html\config\HtmlConfig::isValidTagName
     */
    public function testIsValidTagName(): void
    {
        self::assertTrue(HtmlConfig::isValidTagName('div'));
        self::assertFalse(HtmlConfig::isValidTagName('foo'));
    }

    /**
     * testIsRequiredSubtag
     * @covers \pvc\html\config\HtmlConfig::isRequiredSubtag
     */
    public function testIsRequiredSubtag(): void
    {
        self::assertTrue(HtmlConfig::isRequiredSubtag('head', 'html'));
        self::assertFalse(HtmlConfig::isRequiredSubtag('html', 'head'));
        /**
         * no information in the subtags array about an element named foo.....
         */
        self::assertFalse(HtmlConfig::isRequiredSubtag('bar', 'foo'));
    }

    /**
     * testGetRequiredSubtags
     * @covers \pvc\html\config\HtmlConfig::getRequiredSubtags
     */
    public function testGetRequiredSubtags(): void
    {
        $requiredSubtags = ['head', 'body'];
        self::assertEqualsCanonicalizing($requiredSubtags, HtmlConfig::getRequiredSubtags('html'));
        self::assertEmpty(HtmlConfig::getRequiredSubtags('div'));
    }

    /**
     * testIsValidSubTag
     * @covers \pvc\html\config\HtmlConfig::isValidSubtag
     */
    public function testIsValidSubTag(): void
    {
        self::assertTrue(HtmlConfig::isValidSubtag('head', 'html'));
        self::assertFalse(HtmlConfig::isValidSubtag('html', 'head'));
        /**
         * no information in subtags array about an element named foo
         */
        self::assertTrue(HtmlConfig::isValidSubtag('foo', 'bar'));
    }

    /**
     * testIsInlineElement
     * @covers \pvc\html\config\HtmlConfig::isInlineElement
     */
    public function testIsInlineElement(): void
    {
        self::assertTrue(HtmlConfig::isInlineElement('span'));
        self::assertFalse(HtmlConfig::isInlineElement('div'));
    }

    /**
     * testIsBlockElement
     * @covers \pvc\html\config\HtmlConfig::isBlockElement
     */
    public function testIsBlockElement(): void
    {
        self::assertTrue(HtmlConfig::isBlockElement('div'));
        self::assertFalse(HtmlConfig::isBlockElement('span'));
    }

    /**
     * testInnerTextNotAllowed
     * @covers \pvc\html\config\HtmlConfig::innerTextNotAllowed
     */
    public function testInnerTextNotAllowed(): void
    {
        self::assertTrue(HtmlConfig::innerTextNotAllowed('select'));
        self::assertFalse(HtmlConfig::innerTextNotAllowed('div'));
    }

    public function isValidAttributeDataProvider(): array
    {
        return [
            ['hidden', true, 'global attribute failed'],
            ['method', true, 'tag-specific attribute failed'],
            ['foo', false, 'totally invalid attribute name passed'],
            ['xmlns', false, 'passed attribute that does not belong to this tag'],
        ];
    }

    /**
     * testIsValidAttribute
     * @param string $attributeName
     * @param bool $expectedResult
     * @param string $comment
     * @dataProvider isValidAttributeDataProvider
     * @covers       \pvc\html\config\HtmlConfig::isValidAttribute
     */
    public function testIsValidAttribute(string $attributeName, bool $expectedResult, string $comment): void
    {
        $tagName = 'form';
        self::assertEquals($expectedResult, HtmlConfig::isValidAttribute($tagName, $attributeName), $comment);
    }

    public function isValidAttributeNameDataProvider(): array
    {
        return [
            ['id', true, 'global attribute did not pass'],
            ['checked', true, 'boolean attribute did not pass'],
            ['target', true, 'single value attribute did not pass'],
            ['rel', true, 'multiple value attribute did not pass'],
            ['onclick', true, 'event attribute did not pass'],
            ['foo', false, 'invalid attribute name passed'],
        ];
    }

    /**
     * testIsValidAttributeName
     * @param string $name
     * @param bool $expectedValue
     * @param string $comment
     * @dataProvider isValidAttributeNameDataProvider
     * @covers       \pvc\html\config\HtmlConfig::isValidAttributeName
     * @covers       \pvc\html\config\HtmlConfig::canonicalizeAttributeNameTypes
     */
    public function testIsValidAttributeName(string $name, bool $expectedValue, string $comment): void
    {
        self::assertEquals($expectedValue, HtmlConfig::isValidAttributeName($name), $comment);
    }

    /**
     * testIsVoidAttribute
     * @covers \pvc\html\config\HtmlConfig::isVoidAttribute
     */
    public function testIsVoidAttribute(): void
    {
        self::assertTrue(HtmlConfig::isVoidAttribute('hidden'));
        self::assertFalse(HtmlConfig::isVoidAttribute('id'));
    }

    /**
     * testGetRequiredAttributes
     * @covers \pvc\html\config\HtmlConfig::getRequiredAttributes
     */
    public function testGetRequiredAttributes(): void
    {
        $expectedAttributeValues = ['lang' => 'en'];
        self::assertEqualsCanonicalizing($expectedAttributeValues, HtmlConfig::getRequiredAttributes('html'));
    }

    /**
     * testGetGlobalAttributes
     * @covers \pvc\html\config\HtmlConfig::getGlobalAttributes
     */
    public function testGetGlobalAttributes(): void
    {
        $globalAttributes = HtmlConfig::getGlobalAttributes();

        $expectedResult = [
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
        self::assertEquals($expectedResult, $globalAttributes);
    }

    /**
     * testIsValidEventName
     * @covers \pvc\html\config\HtmlConfig::isValidEventName
     */
    public function testIsValidEventName(): void
    {
        self::assertTrue(HtmlConfig::isValidEventName('onclick'));
        self::assertFalse(HtmlConfig::isValidEventName('foo'));
    }

    public function getAttributeTypeDataProvider(): array
    {
        return [
            ['id', AttributeSingleValue::class, 'global attribute did not pass'],
            ['checked', AttributeVoid::class, 'boolean attribute did not pass'],
            ['target', AttributeSingleValue::class, 'single value attribute did not pass'],
            ['rel', AttributeMultiValue::class, 'multiple value attribute did not pass'],
            ['onclick', Event::class, 'event attribute did not pass'],
            ['foo', null, 'invalid attribute name passed'],
        ];
    }

    /**
     * testGetAttributeType
     * @param string $name
     * @param string|null $expectedType
     * @param string $comment
     * @dataProvider getAttributeTypeDataProvider
     * @covers       \pvc\html\config\HtmlConfig::getAttributeType
     */
    public function testGetAttributeType(string $name, string|null $expectedType, string $comment): void
    {
        self::assertEquals($expectedType, HtmlConfig::getAttributeType($name), $comment);
    }
}
