<?php

namespace pvcTests\html\unit_tests\factory;

use pvc\html\factory\DefIdResolver;
use PHPUnit\Framework\TestCase;
use pvc\htmlbuilder\definitions\types\DefinitionType;

class DefIdResolverTest extends TestCase
{
    /**
     * @return void
     * @covers \pvc\html\factory\DefIdResolver::isAmbiguousName
     * @covers \pvc\html\factory\DefIdResolver::getDefIdFromName
     */
    public function testDefIdResolverReturnsNameIfNotAmbiguous(): void
    {
        $name = 'foo';
        $defType = DefinitionType::Event;
        self::assertEquals($name, DefIdResolver::getDefIdFromName($name, $defType));
    }

    /**
     * @return void
     * @covers \pvc\html\factory\DefIdResolver::isAmbiguousName
     * @covers \pvc\html\factory\DefIdResolver::getSuffix
     * @covers \pvc\html\factory\DefIdResolver::getDefIdFromName
     */
    public function testDefIdResolverReturnsAttributeDefIdAmbiguous(): void
    {
        $name = 'cite';
        $defType = DefinitionType::Attribute;
        $expectedResult = $name . '_attr';
        self::assertEquals($expectedResult, DefIdResolver::getDefIdFromName($name, $defType));
    }

    /**
     * @return void
     * @covers \pvc\html\factory\DefIdResolver::isAmbiguousName
     * @covers \pvc\html\factory\DefIdResolver::getSuffix
     * @covers \pvc\html\factory\DefIdResolver::getDefIdFromName
     */
    public function testDefIdResolverReturnsElementDefIdAmbiguous(): void
    {
        $name = 'cite';
        $defType = DefinitionType::Element;
        $expectedResult = $name . '_element';
        self::assertEquals($expectedResult, DefIdResolver::getDefIdFromName($name, $defType));
    }

}
