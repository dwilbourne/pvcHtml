<?php

/**
 * @author: Doug Wilbourne (dougwilbourne@gmail.com)
 */
declare(strict_types=1);

namespace pvc\html\attribute\val_tester\regex;

use pvc\regex\Regex;

/**
 * Class RegexSizes
 */
class RegexSizes extends Regex
{
    /**
     * @throws \pvc\regex\err\RegexBadPatternException
     */
    public function __construct()
    {
        $pattern = '/^\d+x\d+|any$/';
        $label = "height x width (in pixels) or 'any'";
        $this->setPattern($pattern);
        $this->setLabel($label);
    }
}