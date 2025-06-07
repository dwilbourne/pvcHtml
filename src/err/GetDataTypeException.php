<?php

namespace pvc\html\err;

use pvc\err\stock\RuntimeException;
use Throwable;

class GetDataTypeException extends RuntimeException
{
    public function __construct(string $name, ?Throwable $previous = null)
    {
        parent::__construct($name, $previous);
    }
}