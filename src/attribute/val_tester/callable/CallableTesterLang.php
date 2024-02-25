<?php

/**
 * @author: Doug Wilbourne (dougwilbourne@gmail.com)
 */
declare(strict_types=1);

namespace pvc\html\attribute\val_tester\callable;

use pvc\intl\LanguageCodes;

/**
 * Class CallableTesterLang
 */
class CallableTesterLang
{
    /**
     * __invoke
     * @param string $languageCode
     * @return bool
     */
    public function __invoke(string $languageCode): bool
    {
        return LanguageCodes::validate($languageCode);
    }
}
