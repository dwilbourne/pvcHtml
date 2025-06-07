<?php

/**
 * @author: Doug Wilbourne (dougwilbourne@gmail.com)
 */

declare(strict_types=1);

namespace pvc\html\err;

use pvc\err\stock\LogicException;
use Throwable;

/**
 * Class InvalidAttributeValueTesterNameException
 */
class InvalidAttributeValueTesterNameException extends LogicException
{
    public function __construct(string $attributeValueTesterDefId, ?Throwable $prev = null)
    {
        parent::__construct($attributeValueTesterDefId, $prev);
    }
}
