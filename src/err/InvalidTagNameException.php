<?php

/**
 * @author: Doug Wilbourne (dougwilbourne@gmail.com)
 */
declare(strict_types=1);

namespace pvc\html\err;

use pvc\err\stock\LogicException;
use Throwable;

/**
 * Class InvalidTagNameException
 */
class InvalidTagNameException extends LogicException
{
    public function __construct(string $tagName, Throwable $prev = null)
    {
        parent::__construct($tagName, $prev);
    }
}