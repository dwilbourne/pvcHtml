<?php

/**
 * @author: Doug Wilbourne (dougwilbourne@gmail.com)
 */
declare(strict_types=1);

namespace pvc\html\attribute\val_tester\regex;

use pvc\regex\Regex;

/**
 * Class RegexTabIndex
 */
class RegexTabIndex extends Regex
{
    public function __construct()
    {
        /**
         * any sequence of digits that does not start with 0
         */
        $pattern = '/^[1-9][0-9]*$/';
        $label = 'positive integer';
        $this->setPattern($pattern);
        $this->setLabel($label);
    }
}