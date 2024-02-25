<?php
/**
 * @package: pvc
 * @author: Doug Wilbourne (dougwilbourne@gmail.com)
 * @version: 1.0
 */

namespace pvcTests\html\attribute\val_tester\regex;

use PHPUnit\Framework\TestCase;
use pvc\html\attribute\val_tester\regex\RegexTextDirection;

/**
 * Class RegexTextDirectionTest
 */
class RegexTextDirectionTest extends TestCase
{

    /**
     * @var \pvc\html\attribute\val_tester\regex\RegexTextDirection
     */
    protected RegexTextDirection $regex;

    public function setUp(): void
    {
        $this->regex = new RegexTextDirection();
    }

    /**
     * @function testPattern
     * @param string $textDirection
     * @param bool $expectedResult
     * @dataProvider dataProvider
     * @covers       \pvc\html\attribute\val_tester\regex\RegexTextDirection::__construct
     */
    public function testPattern(string $textDirection, bool $expectedResult): void
    {
        $this->assertEquals($expectedResult, $this->regex->match($textDirection));
    }

    public function dataProvider(): array
    {
        return array(
            'lower case ltr - OK' => ['ltr', true],
            'upper case ltr - OK' => ['LTR', true],
            'mixed case Ltr - OK' => ['Ltr', true],
            'lower case rtl - OK' => ['rtl', true],
            'upper case RTL - OK' => ['RTL', true],
            'mixed case rTL - OK' => ['rTL', true],
            'any case aUtO - OK' => ['aUtO', true],
            'other letters - bad' => ['RTOK', false],
            'numbers, punctuation - bad' => ['3476,<-)98', false],
            'right letters wrong order' => ['Rlt', false]
        );
    }
}
