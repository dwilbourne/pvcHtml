<?php

/**
 * @author: Doug Wilbourne (dougwilbourne@gmail.com)
 */

declare(strict_types=1);

namespace pvc\html\err;

use pvc\err\stock\LogicException;
use Throwable;

/**
 * Class MakeDefinitionException
 */
class MakeDefinitionException extends LogicException
{
    public function __construct(string $type, Throwable $prev = null)
    {
        parent::__construct($type, $prev);
    }
}
