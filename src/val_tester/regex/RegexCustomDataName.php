<?php

/**
 * @author: Doug Wilbourne (dougwilbourne@gmail.com)
 */
declare(strict_types=1);

namespace pvc\html\val_tester\regex;

use pvc\regex\Regex;

/**
 * Class RegexCustomDataName
 */
class RegexCustomDataName extends Regex
{
    public function __construct()
    {
        /**
         * according to various online sources, the data attribute id must be at least one character long and must
         * be prefixed with 'data-'. It should not contain any uppercase letters.  This regex restricts it to lower
         * case letters and numbers
         */
        $pattern = '/^[a-z0-9]*$/';
        $label = 'custom attribute id';
        $this->setPattern($pattern);
        $this->setLabel($label);
    }
}