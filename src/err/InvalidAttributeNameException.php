<?php

/**
 * @author: Doug Wilbourne (dougwilbourne@gmail.com)
 */
declare(strict_types=1);

namespace pvc\html\err;

use pvc\err\stock\LogicException;
use Throwable;

/**
 * Class InvalidAttributeNameException
 */
class InvalidAttributeNameException extends LogicException
{
    public function __construct(string $attributeName, Throwable $prev = null)
    {
        parent::__construct($attributeName, $prev);
    }
}