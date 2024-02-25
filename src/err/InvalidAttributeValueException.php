<?php

/**
 * @author: Doug Wilbourne (dougwilbourne@gmail.com)
 */

declare(strict_types=1);

namespace pvc\html\err;

use pvc\err\stock\LogicException;
use Throwable;

/**
 * Class InvalidAttributeValueException
 */
class InvalidAttributeValueException extends LogicException
{
    public function __construct(string $attributeName, string $attributeValue, Throwable $prev = null)
    {
        parent::__construct($attributeName, $attributeValue, $prev);
    }
}