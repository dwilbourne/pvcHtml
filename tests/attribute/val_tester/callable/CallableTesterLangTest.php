<?php

/**
 * @author: Doug Wilbourne (dougwilbourne@gmail.com)
 */
declare (strict_types=1);

namespace pvcTests\html\attribute\val_tester\callable;

use PHPUnit\Framework\TestCase;
use pvc\html\attribute\val_tester\callable\CallableTesterLang;

class CallableTesterLangTest extends TestCase
{
    protected CallableTesterLang $tester;

    public function setUp(): void
    {
        $this->tester = new CallableTesterLang();
    }

    /**
     * testLangTester
     * @param string $languageCode
     * @param bool $expectedResult
     * @param string $comment
     * @dataProvider LangTesterDataProvider
     * @covers       \pvc\html\attribute\val_tester\callable\CallableTesterLang
     */
    public function testLangTester(string $languageCode, bool $expectedResult, string $comment): void
    {
        self::assertEquals($expectedResult, $this->tester->__invoke($languageCode), $comment);
    }

    /**
     * LangTesterDataProvider
     * @return array[]
     * not really necessary to test more than one true and one false since the tests for the underlying class method
     * are done in the intl library in the Language class
     */
    public function LangTesterDataProvider(): array
    {
        return [
            ['en', true, 'failed to pass en as a valid language'],
            ['fr', true, 'failed to pass fr as a valid language'],
            ['zz', false, 'wrongly passed zz as a valid language'],
        ];
    }
}
