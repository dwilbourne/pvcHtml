<?php

/**
 * @author: Doug Wilbourne (dougwilbourne@gmail.com)
 */
declare (strict_types=1);

namespace pvcTests\html\unit_tests\factory\definitions\types;

use pvc\html\factory\definitions\types\DefinitionType;
use PHPUnit\Framework\TestCase;

class DefinitionTypeTest extends TestCase
{
    /**
     * testFromNameSucceeds
     * @covers \pvc\html\factory\definitions\types\DefinitionType::fromName
     */
    public function testFromNameSucceeds(): void
    {
        self::assertEquals(DefinitionType::Attribute, DefinitionType::fromName('Attribute'));
    }

    /**
     * testFromNameFails
     * @covers \pvc\html\factory\definitions\types\DefinitionType::fromName
     */
    public function testFromNameFails(): void
    {
        self::assertNull(DefinitionType::fromName('foo'));
    }
}
