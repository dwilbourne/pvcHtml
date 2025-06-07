<?php

/**
 * @author: Doug Wilbourne (dougwilbourne@gmail.com)
 */
declare(strict_types=1);

namespace pvc\htmlbuilder\definitions\dto;

/**
 * Class AttributeValTesterDef
 */
readonly class AttributeValTesterDTO
{
    use DTOTrait;
    public string $name;
    public string $concrete;
    /**
     * @var class-string|array<string>|null
     */
    public string|array|null $arg;
}