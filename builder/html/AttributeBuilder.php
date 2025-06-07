<?php

namespace pvc\htmlbuilder\html;

use pvc\htmlbuilder\definitions\canonical\CanonicalAttribute;

class AttributeBuilder extends AttributeEventBuilder
{
    function getCanonical($jsonDef): CanonicalAttribute
    {
        return new CanonicalAttribute($jsonDef);
    }

}