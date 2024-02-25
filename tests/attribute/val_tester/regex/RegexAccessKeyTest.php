<?php

/**
 * @author: Doug Wilbourne (dougwilbourne@gmail.com)
 */
declare (strict_types=1);

namespace pvcTests\html\attribute\val_tester\regex;

use PHPUnit\Framework\TestCase;
use pvc\html\attribute\val_tester\regex\RegexAccessKey;

class RegexAccessKeyTest extends TestCase
{
    protected RegexAccessKey $tester;

    public function setUp(): void
    {
        $this->tester = new \pvc\html\attribute\val_tester\regex\RegexAccessKey();
    }

    /**
     * testLabel
     * @covers \pvc\html\attribute\val_tester\regex\RegexAccessKey::__construct
     */
    public function testLabel(): void
    {
        self::assertNotEmpty($this->tester->getLabel());
    }

    /**
     * testAccessKey
     * @param string $value
     * @param bool $result
     * @dataProvider accesskeyDataProvider
     * @covers       \pvc\html\attribute\val_tester\regex\RegexAccessKey::__construct
     */
    public function testAccessKey(string $value, bool $expectedResult): void
    {
        self::assertEquals($expectedResult, $this->tester->match($value));
    }

    public function accesskeyDataProvider(): array
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
