<?php

namespace pvc\htmlbuilder\definitions\canonical;

use pvc\htmlbuilder\Builder;

/**
 * @phpstan-import-type ElementDefArray from Builder
 */
readonly class CanonicalElement
{
    public string $name;
    public string $baseClass;

    /**
     * @param  ElementDefArray  $jsonDef
     */
    public function __construct(array $jsonDef)
    {
        $this->name = ucfirst($jsonDef['name']);
        $this->baseClass = $jsonDef['concrete'];
    }
}