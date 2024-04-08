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
