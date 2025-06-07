<?php

/**
 * @author: Doug Wilbourne (dougwilbourne@gmail.com)
 */
declare(strict_types=1);

namespace pvc\htmlbuilder\definitions\dto;

/**
 * Class ElementDef
 */
readonly class ElementDTO
{
    use DTOTrait;

    public string $name;
    public string $concrete;

    /**
     * @var array<string>
     */
    public array $attributeNames;
}