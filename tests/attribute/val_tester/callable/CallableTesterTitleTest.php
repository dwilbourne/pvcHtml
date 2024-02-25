<?php

/**
 * @author: Doug Wilbourne (dougwilbourne@gmail.com)
 */
declare (strict_types=1);

namespace pvcTests\html\attribute\val_tester\callable;

use PHPUnit\Framework\TestCase;
use pvc\html\attribute\val_tester\callable\CallableTesterTitle;

class CallableTesterTitleTest extends TestCase
{
    protected CallableTesterTitle $tester;

    public function setUp(): void
    {
        $this->tester = new CallableTesterTitle();
    }

    /**
     * testValTesterTitle
     * @param string $title
     * @param bool $expectedOutput
     * @param string $comment
     * @dataProvider valTesterTitleDataProvider
     * @covers       \pvc\html\attribute\val_tester\callable\CallableTesterTitle
     */
    public function testValTesterTitle(string $title, bool $expectedOutput, string $comment): void
    {
        self::assertEquals($expectedOutput, $this->tester->__invoke($title), $comment);
    }

    public function valTesterTitleDataProvider(): array
    {
        return [
            ['short title', true, 'failed on short title test'],
            [
                'long title XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX',
                false,
                'failed long title test'
            ],
        ];
    }
}
