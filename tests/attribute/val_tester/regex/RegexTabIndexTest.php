<?php

/**
 * @author: Doug Wilbourne (dougwilbourne@gmail.com)
 */
declare (strict_types=1);

namespace pvcTests\html\attribute\val_tester\regex;

use PHPUnit\Framework\TestCase;
use pvc\html\attribute\val_tester\regex\RegexTabIndex;

class RegexTabIndexTest extends TestCase
{
    protected RegexTabIndex $regex;

    public function setUp(): void
    {
        $this->regex = new \pvc\html\attribute\val_tester\regex\RegexTabIndex();
    }

    /**
     * testLabelIsSet
     * @covers \pvc\html\attribute\val_tester\regex\RegexTabIndex::__construct
     */
    public function testLabelIsSet(): void
    {
        self::assertIsString($this->regex->getLabel());
    }

    /**
     * testRegex
     * @param string $testValue
     * @param bool $expectedResult
     * @dataProvider regexDataProvider
     * @covers       \pvc\html\attribute\val_tester\regex\RegexTabIndex::__construct
     */
    public function testRegex(string $testValue, bool $expectedResult, string $comment): void
    {
        self::assertEquals($expectedResult, $this->regex->match($testValue), $comment);
    }

    public function regexDataProvider(): array
    {
        return [
            ['2', true, 'failed basic single digit test'],
            ['32', true, 'failed double digit test'],
            ['04', false, 'failed cannot start with zero test'],
            ['A6', false, 'failed cannot start with non-digit test'],
        ];
    }
}
