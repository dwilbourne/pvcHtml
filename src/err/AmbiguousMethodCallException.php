<?php

/**
 * @author: Doug Wilbourne (dougwilbourne@gmail.com)
 */

declare(strict_types=1);

namespace pvc\html\err;

use pvc\err\stock\LogicException;
use Throwable;

/**
 * Class AmbiguousMethodCallException
 */
class AmbiguousMethodCallException extends LogicException
{
    public function __construct(string $methodName, Throwable $prev = null)
    {
        parent::__construct($methodName, $prev);
    }
}
