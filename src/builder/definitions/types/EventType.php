<?php

/**
 * @author: Doug Wilbourne (dougwilbourne@gmail.com)
 */
declare(strict_types=1);

namespace pvc\html\builder\definitions\types;

use pvc\html\attribute\Event;

/**
 * Class EventType
 */
enum EventType: string
{
    use GetClassTrait;

    case Event = Event::class;
}