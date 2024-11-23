<?php

/**
 * @author: Doug Wilbourne (dougwilbourne@gmail.com)
 */
declare(strict_types=1);

namespace pvc\html\factory\definitions\dto;

/**
 * Class EventDef
 */
readonly class EventDTO
{
    use DTOTrait;

    public string $defId;
    public string $concrete;
}