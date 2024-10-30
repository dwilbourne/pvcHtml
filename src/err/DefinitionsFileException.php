<?php

/**
 * @author: Doug Wilbourne (dougwilbourne@gmail.com)
 */

declare(strict_types=1);

namespace pvc\html\err;

use pvc\err\stock\LogicException;
use Throwable;

/**
 * Class DefinitionsFileException
 */
class DefinitionsFileException extends LogicException
{
    public function __construct(string $filePath, Throwable $prev = null)
    {
        parent::__construct($filePath, $prev);
    }
}
