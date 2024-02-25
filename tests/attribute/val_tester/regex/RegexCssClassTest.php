<?php

/**
 * @author: Doug Wilbourne (dougwilbourne@gmail.com)
 */
declare(strict_types=1);

namespace pvcTests\html\attribute\val_tester\regex;

use PHPUnit\Framework\TestCase;
use pvc\html\attribute\val_tester\regex\RegexCssClass;

/**
 * Class RegexCssClassTest
 */
class RegexCssClassTest extends TestCase
{
    protected \pvc\html\attribute\val_tester\regex\RegexCssClass $regex;

    public function setUp(): void
    {
        $this->regex = new \pvc\html\attribute\val_tester\regex\RegexCssClass();
    }

    /**
     * testLabel
     * @covers \pvc\html\attribute\val_tester\regex\RegexCssClass::__construct
     */
    public function testLabel(): void
    {
        self::assertNotEmpty($this->regex->getLabel());
    }

    /**
     * testCssClass
     * @param string $className
     * @param bool $expectedResult
     * @dataProvider cssClassDataProvider
     * @covers       \pvc\html\attribute\val_tester\regex\RegexCssClass::__construct
     */
    public function testCssClass(string $className, bool $expectedResult): void
    {
        self::assertEquals($expectedResult, $this->regex->match($className));
    }

    public function cssClassDataProvider(): array
    {
        return array(
            'begins with a letter, contains numbers and letters - OK' => ['a94P', true],
            'begins with a letter, contains numbers, letters and an underscore - OK' => ['PPhgsd892JNay_', true],
            'begins with a letter, contains numbers, letters, underscore and hyphen - OK' => ['Uhsdb-jk_III', true],
            'starts with a numbers - bad' => ['7Ysg2', false],
            'cannot contain an asterisk - bad' => ['J56*dj', false],
            'cannot contain a dollar sign - bad' => ['Js56$9jk', false]
        );
    }


}