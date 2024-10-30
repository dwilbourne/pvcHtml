<?php

/**
 * @author: Doug Wilbourne (dougwilbourne@gmail.com)
 */
declare(strict_types=1);

namespace pvc\html\err;

use pvc\err\stock\LogicException;
use Throwable;

/**
 * Class InvalidEventNameException
 */
class InvalidEventNameException extends LogicException
{
    public function __construct(string $eventName, Throwable $prev = null)
    {
        parent::__construct($eventName, $prev);
    }
}