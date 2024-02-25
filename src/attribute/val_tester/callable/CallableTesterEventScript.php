<?php

/**
 * @author: Doug Wilbourne (dougwilbourne@gmail.com)
 */
declare(strict_types=1);

namespace pvc\html\attribute\val_tester\callable;

/**
 * Class CallableTesterEventScript
 */
class CallableTesterEventScript
{
    /**
     * __invoke
     * @param string $script
     * @return bool
     */
    public function __invoke(string $script): bool
    {
        return ((strlen($script) > 1) && (str_ends_with($script, ';')));
    }
}
