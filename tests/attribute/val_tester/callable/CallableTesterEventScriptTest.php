<?php

/**
 * @author: Doug Wilbourne (dougwilbourne@gmail.com)
 */
declare (strict_types=1);

namespace pvcTests\html\attribute\val_tester\callable;

use PHPUnit\Framework\TestCase;
use pvc\html\attribute\val_tester\callable\CallableTesterEventScript;

class CallableTesterEventScriptTest extends TestCase
{
    /**
     * @var CallableTesterEventScript
     */
    protected CallableTesterEventScript $testerEventScript;

    /**
     * setUp
     */
    public function setUp(): void
    {
        $this->testerEventScript = new CallableTesterEventScript();
    }

    /**
     * testEventScriptTester
     * @param bool $expectedResult
     * @param string $script
     * @param string $comment
     * @covers       \pvc\html\attribute\val_tester\callable\CallableTesterEventScript
     * @dataProvider EventScriptTesterDataProvider
     */
    public function testEventScriptTester(bool $expectedResult, string $script, string $comment): void
    {
        self::assertEquals($expectedResult, $this->testerEventScript->__invoke($script), $comment);
    }

    /**
     * EventScriptTesterDataProvider
     * @return array<string, bool, string>
     */
    public function EventScriptTesterDataProvider(): array
    {
        return [
            [true, 'some javascript;', 'failed to pass string that has length greater than 2 and ends with a ;'],
            [false, ';', 'wrongly passed a single semi-colon - it does not have length > 2'],
            [false, 'zzzzzzz', 'wrongly passed zzzzzzzz - does not end in a demi-colon'],
        ];
    }

}
