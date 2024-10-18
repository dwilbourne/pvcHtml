<?php

/**
 * @author: Doug Wilbourne (dougwilbourne@gmail.com)
 */

declare(strict_types=1);

namespace pvc\html\abstract\err;

use pvc\err\stock\LogicException;
use Throwable;

/**
 * Class AttributeNotAllowedException
 */
class AttributeNotAllowedException extends LogicException
{
    public function __construct(string $attributeName, string $tagName, Throwable $prev = null)
    {
        parent::__construct($attributeName, $tagName, $prev);
    }
}
