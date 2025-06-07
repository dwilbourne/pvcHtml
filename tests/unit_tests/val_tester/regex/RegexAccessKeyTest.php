<?php

/**
 * @author: Doug Wilbourne (dougwilbourne@gmail.com)
 */
declare (strict_types=1);

namespace pvcTests\html\unit_tests\val_tester\regex;

use PHPUnit\Framework\TestCase;
use pvc\html\val_tester\regex\RegexAccessKey;

class RegexAccessKeyTest extends TestCase
{
    protected RegexAccessKey $regex;

    public function setUp(): void
    {
        $this->regex = new RegexAccessKey();
    }

    /**
     * testLabel
     * @covers \pvc\html\val_tester\regex\RegexAccessKey::__construct
     */
    public function testLabel(): void
    {
        self::assertNotEmpty($this->regex->getLabel());
    }

    /**
     * testAccessKey
     * @param string $value
     * @param bool $result
     * @dataProvider accesskeyDataProvider
     * @covers \pvc\html\val_tester\regex\RegexAccessKey::__construct
     */
    public function testAccessKey(string $value, bool $expectedResult): void
    {
        self::assertEquals($expectedResult, $this->regex->match($value));
    }

    public static function accesskeyDataProvider(): array
    {
        return array(
            "lower case letter 'a' OK" => ['a', true],
            "lower case letter 'p' OK" => ['p', true],
            "number '2' OK" => ['2', true],
            "number '9' OK" => ['2', true],
            "upper case letter 'H' bad" => ['H', false],
            "upper case letter 'P' bad" => ['P', false],
            "cannot be more than one character" => ['Foo', false],
            "can only be a lower case letter or number" => ['!', false],
            "can only be one number" => ['99', false]
        );
    }
}
