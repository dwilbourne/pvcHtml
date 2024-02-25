<?php

/**
 * @author: Doug Wilbourne (dougwilbourne@gmail.com)
 */
declare (strict_types=1);

namespace attribute\val_tester\callable;

use PHPUnit\Framework\TestCase;
use pvc\html\attribute\val_tester\callable\CallableTesterType;

class CallableTesterTypeTest extends TestCase
{
    protected CallableTesterType $tester;

    public function setUp(): void
    {
        $this->tester = new CallableTesterType();
    }

    /**
     * testTesterType
     * @param string $mimetype
     * @param bool $expectedResult
     * @param string $comment
     * @dataProvider TesterTypeDataProvider
     * @covers       \pvc\html\attribute\val_tester\callable\CallableTesterType
     */
    public function testTesterType(string $mimetype, bool $expectedResult, string $comment): void
    {
        self::assertEquals($expectedResult, $this->tester->__invoke($mimetype), $comment);
    }

    /**
     * testerTypeDataProvider
     * @return array[]
     * only necessary to test one passing and one failing - the true test for the mime type parser is elsewhere.....
     */
    public function TesterTypeDataProvider(): array
    {
        return [
            ['text/html', true, 'failed to validate text/html as a valid mime type'],
            ['foo', false, 'wrongly validated foo as a valid mime type'],
        ];
    }
}
