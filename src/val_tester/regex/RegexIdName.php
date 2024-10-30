<?php

/**
 * @author: Doug Wilbourne (dougwilbourne@gmail.com)
 */
declare(strict_types=1);

namespace pvc\html\val_tester\regex;

use pvc\regex\Regex;

/**
 * Class RegexIdName
 * From the w3 spec:
 * ID and NAME tokens must begin with a letter ([A-Za-z]) and may be followed by any number of letters, digits ([0-9]),
 * hyphens ("-"), underscores ("_"), colons (":"), and periods (".")
 */
class RegexIdName extends Regex
{

    public function __construct()
    {
        $label = 'idOrName';
        $pattern = '/^(?=.*[a-zA-Z])[a-zA-Z0-9\-_\.]*$/';
        $this->setPattern($pattern);
        $this->setLabel($label);
    }
}