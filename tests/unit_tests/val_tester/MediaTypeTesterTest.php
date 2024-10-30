<?php

/**
 * @author: Doug Wilbourne (dougwilbourne@gmail.com)
 */

declare (strict_types=1);

namespace pvcTests\html\unit_tests\val_tester;

use PHPUnit\Framework\TestCase;
use pvc\html\val_tester\MediaTypeTester;
use pvc\interfaces\http\mimetype\MimeTypesInterface;

class MediaTypeTesterTest extends TestCase
{
    protected MimeTypesInterface $mimeTypes;

    protected MediaTypeTester $tester;

    public function setUp(): void
    {
        $this->mimeTypes = $this->createMock(MimeTypesInterface::class);
        $this->tester = new MediaTypeTester($this->mimeTypes);
    }

    /**
     * testConstruct
     * @covers \pvc\html\val_tester\MediaTypeTester::__construct
     */
    public function testConstruct(): void
    {
        self::assertInstanceOf(MediaTypeTester::class, $this->tester);
    }

    /**
     * testTestValueSpecificStrings
     * @param string $mediaType
     * @param bool $expectedResult
     * @param string $comment
     * @covers \pvc\html\val_tester\MediaTypeTester::testValue
     * @dataProvider dataProvider
     */
    public function testTestValueSpecificStrings(string $mediaType, bool $expectedResult, string $comment): void
    {
        self::assertEquals($expectedResult, $this->tester->testValue($mediaType), $comment);
    }

    public function dataProvider(): array
    {
        return [
            ['audio/*', true, 'failed to validate audio/*'],
            ['video/*', true, 'failed to validate video/*'],
            ['image/*', true, 'failed to validate image/*'],
            ['kinesthetic/*', false, 'incorrectly validated kinesthetic/*'],
        ];
    }

    /**
     * testValidMimeTypeName
     * @covers \pvc\html\val_tester\MediaTypeTester::testValue
     */
    public function testValidMimeTypeName(): void
    {
        $mimeType = 'foo/bar';
        $this->mimeTypes->method('isValidMimeTypeName')->willReturn(true);
        $this->mimeTypes->method('isValidMimeTypeFileExtension')->willReturn(false);
        self::assertTrue($this->tester->testValue($mimeType));
    }

    /**
     * testValidMimeTypeFileExtension
     * @covers \pvc\html\val_tester\MediaTypeTester::testValue
     */
    public function testValidMimeTypeFileExtension(): void
    {
        $mimeType = 'bar';
        $this->mimeTypes->method('isValidMimeTypeName')->willReturn(false);
        $this->mimeTypes->method('isValidMimeTypeFileExtension')->willReturn(true);
        self::assertTrue($this->tester->testValue($mimeType));
    }

    /**
     * testInvalidString
     * @covers \pvc\html\val_tester\MediaTypeTester::testValue
     */
    public function testInvalidString(): void
    {
        $mimeType = 'bar';
        $this->mimeTypes->method('isValidMimeTypeName')->willReturn(false);
        $this->mimeTypes->method('isValidMimeTypeFileExtension')->willReturn(false);
        self::assertFalse($this->tester->testValue($mimeType));
    }
}
