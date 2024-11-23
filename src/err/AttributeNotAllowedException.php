<?php

/**
 * @author: Doug Wilbourne (dougwilbourne@gmail.com)
 */

declare(strict_types=1);

namespace pvc\html\err;

use pvc\err\stock\LogicException;
use Throwable;

/**
 * Class AttributeNotAllowedException
 */
class AttributeNotAllowedException extends LogicException
{
    public function __construct(string $attributeDefId, string $tagDefId, Throwable $prev = null)
    {
        parent::__construct($attributeDefId, $tagDefId, $prev);
    }
}
