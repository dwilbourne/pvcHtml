<?php

/**
 * @author: Doug Wilbourne (dougwilbourne@gmail.com)
 */

declare(strict_types=1);

namespace pvc\html\err;

use pvc\err\stock\LogicException;
use Throwable;

/**
 * Class InvalidAttributeException
 */
class InvalidAttributeException extends LogicException
{
    public function __construct(string $badAttributeName, Throwable $prev = null)
    {
        parent::__construct($badAttributeName, $prev);
    }
}
