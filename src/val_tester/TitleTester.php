<?php

/**
 * @author: Doug Wilbourne (dougwilbourne@gmail.com)
 */
declare(strict_types=1);

namespace pvc\html\val_tester;

use pvc\interfaces\validator\ValTesterInterface;

/**
 * Class TitleTester
 * @implements ValTesterInterface<string>
 */
class TitleTester implements ValTesterInterface
{
    const MAX_TITLE_LENGTH = 100;

    public function testValue(mixed $value): bool
    {
        return (self::MAX_TITLE_LENGTH >= strlen($value));
    }
}