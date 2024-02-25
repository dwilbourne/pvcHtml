<?php

/**
 * @author: Doug Wilbourne (dougwilbourne@gmail.com)
 */
declare (strict_types=1);

namespace pvcTests\html\attribute\val_tester\regex;

use PHPUnit\Framework\TestCase;
use pvc\html\attribute\val_tester\regex\RegexCustomDataName;

class RegexCustomDataNameTest extends TestCase
{
    protected RegexCustomDataName $regex;

    public function setUp(): void
    {
        $this->regex = new RegexCustomDataName();
    }

    /**
     * testLabel
     * @covers \pvc\html\attribute\val_tester\regex\RegexCustomDataName::__construct
     */
    public function testLabel(): void
    {
        self::assertNotEmpty($this->regex->getLabel());
    }

    /**
     * testCustomDataName
     * @param string $testName
     * @param bool $expectedResult
     * @dataProvider customDataNameDataProvider
     * @covers       \pvc\html\attribute\val_tester\regex\RegexCustomDataName::__construct
     */
    public function testCustomDataName(string $testName, bool $expectedResult): void
    {
        self::assertEquals($expectedResult, $this->regex->match($testName));
    }

    public function customDataNameDataProvider(): array
    {
        return array(
            'contains numbers and lowercase letters - OK' => ['a94p', true],
            'contains numbers, lowercase letters, and various punctuation' => ['jsd78#)kj', false],
            'contains upper case letter' => ['7Ysg2', false],
            'contains another upper case letter' => ['J56*dj', false]
        );
    }


}
