<?php

/**
 * @author: Doug Wilbourne (dougwilbourne@gmail.com)
 */
declare(strict_types=1);

namespace pvc\html\attribute\val_tester\callable;

use pvc\intl\Charset;

/**
 * Class CallableTesterCharset
 */
class CallableTesterCharset
{

    /**
     * __invoke
     * @param string $charset
     * @return bool
     */
    public function __invoke(string $charset): bool
    {
        return Charset::isValid($charset);
    }
}
