<?php

/**
 * @author: Doug Wilbourne (dougwilbourne@gmail.com)
 */
declare(strict_types=1);

namespace pvc\htmlbuilder\definitions\types;

use pvc\html\event\Event;

/**
 * Class EventType
 */
enum EventType: string
{
    use GetClassTrait;

    case Event = Event::class;
}