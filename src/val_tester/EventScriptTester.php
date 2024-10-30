<?php

/**
 * @author: Doug Wilbourne (dougwilbourne@gmail.com)
 */
declare(strict_types=1);

namespace pvc\html\val_tester;

use pvc\interfaces\validator\ValTesterInterface;

/**
 * Class EventScriptTester
 * @implements ValTesterInterface<string>
 */
class EventScriptTester implements ValTesterInterface
{
    public function testValue(mixed $value): bool
    {
        return ((strlen($value) > 1) && (str_ends_with($value, ';')));
    }
}
