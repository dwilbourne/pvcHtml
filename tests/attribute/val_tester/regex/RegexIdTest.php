<?php
/**
 * @package: pvc
 * @author: Doug Wilbourne (dougwilbourne@gmail.com)
 * @version: 1.0
 */

namespace pvcTests\html\attribute\val_tester\regex;

use PHPUnit\Framework\TestCase;
use pvc\html\attribute\val_tester\regex\RegexId;

/**
 * Class RegexIdTest
 */
class RegexIdTest extends TestCase
{

    protected RegexId $regex;

    function setUp(): void
    {
        $this->regex = new RegexId();
    }

    /**
     * testLabel
     * @covers \pvc\html\attribute\val_tester\regex\RegexId::__construct
     */
    public function testLabel(): void
    {
        self::assertIsString($this->regex->getLabel());
    }

    /**
     * @function testPattern
     * @param string $idName
     * @param bool $expectedResult
     * @dataProvider dataProvider
     * @covers       \pvc\html\attribute\val_tester\regex\RegexId::__construct
     */
    public function testPattern(string $idName, bool $expectedResult): void
    {
        $this->assertEquals($expectedResult, $this->regex->match($idName));
    }

    /**
     * dataProvider
     * @return array|array[]
     */
    public function dataProvider(): array
    {
        return array(
            'contains numbers and lowercase letters - OK' => ['a94p', true],
            'contains numbers only - bad' => ['8943', false],
            'contains numbers, lowercase letters and an underscore - OK' => ['14p8734uh_', true],
            'contains numbers, letters, and various punctuation - bad' => ['jsd78#)kj', false],
            'contains upper case letter - OK' => ['7Ysg2', true],
            'contains another upper case letter and a hyphen - OK' => ['J56-dj', true]
        );
    }

}