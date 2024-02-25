<?php

/**
 * @author: Doug Wilbourne (dougwilbourne@gmail.com)
 */
declare (strict_types=1);

namespace pvcTests\html\tag\abstract;

use PHPUnit\Framework\TestCase;
use pvc\html\tag\abstract\TagAttributes;

class TagAttributesTest extends TestCase
{
    /**
     * testIsValidAttributeSucceeds
     * @covers \pvc\html\tag\abstract\TagAttributes::isValidAttribute
     */
    public function testIsValidAttributeSucceeds(): void
    {
        $tagName = 'form';
        $attributeName = 'method';
        self::assertTrue(TagAttributes::isValidAttribute($tagName, $attributeName));
    }

    /**
     * testIsValidAttributeFails
     * @covers \pvc\html\tag\abstract\TagAttributes::isValidAttribute
     */
    public function testIsValidAttributeFails(): void
    {
        $tagName = 'form';
        $attributeName = 'foo';
        self::assertFalse(TagAttributes::isValidAttribute($tagName, $attributeName));
    }

    /**
     * testIsValidEventSucceeds
     * @covers \pvc\html\tag\abstract\TagAttributes::isValidEvent
     */
    public function testIsValidEventSucceeds(): void
    {
        $tagName = 'form';
        $attributeName = 'onclick';
        self::assertTrue(TagAttributes::isValidEvent($attributeName));
    }

    /**
     * testIsValidEventSucceeds
     * @covers \pvc\html\tag\abstract\TagAttributes::isValidEvent
     */
    public function testIsValidEventFails(): void
    {
        $tagName = 'form';
        $attributeName = 'foo';
        self::assertFalse(TagAttributes::isValidEvent($attributeName));
    }
}
