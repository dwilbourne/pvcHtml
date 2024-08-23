<?php

/**
 * @author: Doug Wilbourne (dougwilbourne@gmail.com)
 */

declare (strict_types=1);

namespace pvcTests\html\attribute\val_tester\callable;

use PHPUnit\Framework\TestCase;
use pvc\html\attribute\val_tester\callable\CallableTesterCharset;

class CallableTesterCharsetTest extends TestCase
{
    protected CallableTesterCharset $tester;

    public function setUp(): void
    {
        $this->tester = new CallableTesterCharset();
    }

    /**
     * testCharsetTester
     * @param string $input
     * @param bool $expectedResult
     * @param string $comment
     * @dataProvider dataProvider
     * @covers       \pvc\html\attribute\val_tester\callable\CallableTesterCharset::__invoke
     */
    public function testCharsetTester(string $input, bool $expectedResult, string $comment): void
    {
        self::assertEquals($expectedResult, $this->tester->__invoke($input), $comment);
    }

    public function dataProvider(): array
    {
        return [
            ['utf-8', true, 'failed to validate utf-8'],
            ['UTF-8', true, 'failed to validate UTF-8'],
            ['foo', false, 'incorreectly validated foo'],
        ];
    }
}
