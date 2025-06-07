<?php

namespace pvc\htmlbuilder\definitions\canonical;

use pvc\htmlbuilder\Builder;

/**
 * @phpstan-import-type EventDefArray from Builder
 */
readonly class CanonicalEvent extends CanonicalAttributeEvent
{
    /**
     * @param  EventDefArray  $jsonDef
     *
     * @return string
     * class names should start with an upper case letter, event names
     * are all lower case
     */
    protected function getName(array $jsonDef): string
    {
        return $jsonDef['name'];
    }

    /**
     * @param  EventDefArray  $jsonDef
     *
     * @return string
     */
    public function getObjectName(array $jsonDef): string
    {
        return $this->getName($jsonDef) . 'Event';
    }

    /**
     * @return string
     */
    public function getBaseObjectSetterName(): string
    {
        return 'setAttribute';
    }

    /**
     * @return string
     */
    public function getBaseObjectGetterName(): string
    {
        return 'getAttribute';
    }

    /**
     * @param  EventDefArray  $jsonDef
     *
     * @return string
     */
    protected function getDataType(array $jsonDef): string
    {
        return 'string';
    }
}