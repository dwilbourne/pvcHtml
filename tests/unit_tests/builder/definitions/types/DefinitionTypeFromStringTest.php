<?php

/**
 * @author: Doug Wilbourne (dougwilbourne@gmail.com)
 */
declare (strict_types=1);

namespace pvcTests\html\unit_tests\builder\definitions\types;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use pvc\html\builder\definitions\types\DefinitionTypeFromStringTrait;
use pvc\interfaces\html\builder\definitions\DefinitionType;

class DefinitionTypeFromStringTest extends TestCase
{
    protected DefinitionTypeFromStringTrait|MockObject $trait;

    public function setUp(): void
    {
        $this->trait = $this->getMockForTrait(DefinitionTypeFromStringTrait::class);
    }

    /**
     * testFromNameSucceeds
     * @covers \pvc\html\builder\definitions\types\DefinitionTypeFromStringTrait::fromName
     */
    public function testFromNameSucceeds(): void
    {
        self::assertEquals(DefinitionType::Attribute, $this->trait::fromName('Attribute'));
    }

    /**
     * testFromNameFails
     * @covers \pvc\html\builder\definitions\types\DefinitionTypeFromStringTrait::fromName
     */
    public function testFromNameFails(): void
    {
        self::assertNull($this->trait::fromName('foo'));
    }
}
