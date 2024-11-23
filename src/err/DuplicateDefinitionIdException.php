<?php

/**
 * @author: Doug Wilbourne (dougwilbourne@gmail.com)
 */

declare(strict_types=1);

namespace pvc\html\err;

use pvc\err\stock\LogicException;
use Throwable;

/**
 * Class DuplicateDefinitionIdException
 */
class DuplicateDefinitionIdException extends LogicException
{
    public function __construct(string $defId, Throwable $prev = null)
    {
        parent::__construct($defId, $prev);
    }
}
