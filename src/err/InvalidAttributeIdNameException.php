<?php

/**
 * @author: Doug Wilbourne (dougwilbourne@gmail.com)
 */

declare(strict_types=1);

namespace pvc\html\err;

use pvc\err\stock\LogicException;
use Throwable;

/**
 * Class InvalidAttributeIdNameException
 */
class InvalidAttributeIdNameException extends LogicException
{
    public function __construct(string $badName, ?Throwable $prev = null)
    {
        parent::__construct($badName, $prev);
    }
}
