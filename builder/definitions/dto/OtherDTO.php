<?php

/**
 * @author: Doug Wilbourne (dougwilbourne@gmail.com)
 */
declare(strict_types=1);

namespace pvc\htmlbuilder\definitions\dto;

/**
 * Class OtherDef
 */
readonly class OtherDTO
{
    use DTOTrait;
    public string $name;
    public string $concrete;
    /**
     * @var class-string|null
     */
    public ?string $arg;
    public bool $shared;
}