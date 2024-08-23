<?php

/**
 * @author: Doug Wilbourne (dougwilbourne@gmail.com)
 */
declare(strict_types=1);

namespace pvc\html\attribute\val_tester\regex;

use pvc\regex\Regex;

/**
 * Class RegexId
 */
class RegexId extends Regex
{

    public function __construct()
    {
        $label = 'id';
        $pattern = '/^(?=.*[a-zA-Z])[a-zA-Z0-9\-_]*$/';
        $this->setPattern($pattern);
        $this->setLabel($label);
    }
}