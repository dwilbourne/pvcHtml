<?php

/**
 * @author: Doug Wilbourne (dougwilbourne@gmail.com)
 */
declare(strict_types=1);

namespace pvc\html\attribute\val_tester\callable;

/**
 * Class ValTesterTitle
 */
class CallableTesterTitle
{
    const MAX_TITLE_LENGTH = 100;

    public function __invoke(string $title): bool
    {
        return (self::MAX_TITLE_LENGTH >= strlen($title));
    }
}