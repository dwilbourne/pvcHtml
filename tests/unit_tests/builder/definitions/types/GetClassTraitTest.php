<?php

/**
 * @author: Doug Wilbourne (dougwilbourne@gmail.com)
 */
declare (strict_types=1);

namespace pvcTests\html\unit_tests\builder\definitions\types;

use PHPUnit\Framework\TestCase;
use pvc\html\builder\definitions\types\AttributeType;
use pvc\html\builder\definitions\types\GetClassTrait;

class GetClassTraitTest extends TestCase
{
    protected $trait;

    public function setUp(): void
    {
        $this->trait = $this->getMockForTrait(GetClassTrait::class);
    }

    /**
     * testGetClassSucceeds
     * @covers \pvc\html\builder\definitions\types\GetClassTrait
     */
    public function testGetClassSucceeds(): void
    {
        self::assertIsString(AttributeType::getClass('AttributeVoid'));
    }

    /**
     * testgetClassFails
     * @covers \pvc\html\builder\definitions\types\GetClassTrait
     */
    public function testgetClassFails(): void
    {
        self::assertNull(AttributeType::getClass('Foobar'));
    }
}
