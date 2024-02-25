<?php

/**
 * @author: Doug Wilbourne (dougwilbourne@gmail.com)
 */
declare (strict_types=1);

namespace pvcTests\html\attribute\val_tester\regex;

use PHPUnit\Framework\TestCase;
use pvc\html\attribute\val_tester\regex\RegexSizes;

class RegexSizesTest extends TestCase
{
    /**
     * @var \pvc\html\attribute\val_tester\regex\RegexTextDirection
     */
    protected RegexSizes $regex;

    public function setUp(): void
    {
        $this->regex = new RegexSizes();
    }

    /**
     * @function testPattern
     * @param string $size
     * @param bool $expectedResult
     * @dataProvider dataProvider
     * @covers       \pvc\html\attribute\val_tester\regex\RegexSizes::__construct
     */
    public function testPattern(string $size, bool $expectedResult): void
    {
        $this->assertEquals($expectedResult, $this->regex->match($size));
    }

    public function dataProvider(): array
    {
        return array(
            'zeros make no sense but are ok' => ['0x0', true, 'failed on zeros make no sense but are ok'],
            'one number x one number' => ['5x5', true, 'failed one number x one number'],
            'two numbers x two numbers' => ['24x35', true, 'two numbers x two numbers'],
            'n numbers by m numbers' => ['9x132', true, ' failed on n numbers by m numbers'],
            'must start with a number' => ['x35', false, 'incorrectly passed must start with a number'],
            'must end with a number' => ['102x', false, 'incorrectly passed must end with a number'],
            'separator must be an x - used y instead' => [
                '24y35',
                false,
                'incorrectly passed separator must be an x - used y instead'
            ],
            'separator must be an x - used space instead' => [
                '24y35',
                false,
                'incorrectly passed separator must be an x - used space instead'
            ],
        );
    }

}
