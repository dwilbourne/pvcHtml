<?php

/**
 * @author: Doug Wilbourne (dougwilbourne@gmail.com)
 */
declare(strict_types=1);

namespace pvc\html\attribute\val_tester\regex;

use pvc\regex\Regex;

/**
 * Class RegexAccessKey
 */
class RegexAccessKey extends Regex
{
    public function __construct()
    {
        /**
         * digit or lower case letter
         */
        $pattern = '/^((\d)|([a-z]))$/';
        $label = 'access key';
        $this->setPattern($pattern);
        $this->setLabel($label);
    }
}