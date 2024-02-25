<?php

/**
 * @author: Doug Wilbourne (dougwilbourne@gmail.com)
 */
declare(strict_types=1);

namespace pvc\html\attribute\val_tester\regex;

use pvc\regex\Regex;

/**
 * Class RegexCssClass
 */
class RegexCssClass extends Regex
{
    public function __construct()
    {
        /**
         * must begin with a letter (a-z, A-Z), can be followed by letters, digits, hyphens or underscores
         */
        $pattern = '/^[a-zA-Z][a-zA-Z0-9\-_]*$/';
        $label = 'css class name';
        $this->setPattern($pattern);
        $this->setLabel($label);
    }
}