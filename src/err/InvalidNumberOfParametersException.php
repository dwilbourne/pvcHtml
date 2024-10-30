<?php

/**
 * @author: Doug Wilbourne (dougwilbourne@gmail.com)
 */
declare(strict_types=1);

namespace pvc\html\err;

use pvc\err\stock\LogicException;
use Throwable;

/**
 * Class InvalidNumberOfParametersException
 */
class InvalidNumberOfParametersException extends LogicException
{
    public function __construct(string $expectedNumberOfParameters, Throwable $prev = null)
    {
        parent::__construct($expectedNumberOfParameters, $prev);
    }
}