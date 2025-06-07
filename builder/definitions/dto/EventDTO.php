<?php

/**
 * @author: Doug Wilbourne (dougwilbourne@gmail.com)
 */
declare(strict_types=1);

namespace pvc\htmlbuilder\definitions\dto;

/**
 * Class EventDef
 */
readonly class EventDTO
{
    use DTOTrait;
    public string $name;
    public string $concrete;
}