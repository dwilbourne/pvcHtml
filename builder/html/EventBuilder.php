<?php

namespace pvc\htmlbuilder\html;

use pvc\htmlbuilder\definitions\canonical\CanonicalEvent;

class EventBuilder extends AttributeEventBuilder
{
    function getCanonical($jsonDef): CanonicalEvent
    {
        return new CanonicalEvent($jsonDef);
    }
}
