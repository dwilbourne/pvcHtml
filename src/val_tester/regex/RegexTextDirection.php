<?php

/**
 * @author: Doug Wilbourne (dougwilbourne@gmail.com)
 */
declare(strict_types=1);

namespace pvc\html\val_tester\regex;

use pvc\regex\Regex;

/**
 * Class RegexTextDirection
 */
class RegexTextDirection extends Regex
{
    public function __construct()
    {
        $pattern = '/^ltr|rtl|auto$/i';
        $label = 'text direction';
        $this->setPattern($pattern);
        $this->setLabel($label);
    }
}