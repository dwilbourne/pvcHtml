<?php

/**
 * @author: Doug Wilbourne (dougwilbourne@gmail.com)
 */

declare(strict_types=1);

namespace pvc\html\err;

use pvc\err\stock\LogicException;
use Throwable;

/**
 * Class InvalidTagException
 */
class ChildElementNotAllowedException extends LogicException
{
    public function __construct(string $badDefId, Throwable $prev = null)
    {
        parent::__construct($badDefId, $prev);
    }
}

