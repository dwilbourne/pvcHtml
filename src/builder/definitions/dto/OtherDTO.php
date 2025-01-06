<?php

/**
 * @author: Doug Wilbourne (dougwilbourne@gmail.com)
 */
declare(strict_types=1);

namespace pvc\html\builder\definitions\dto;

/**
 * Class OtherDef
 */
readonly class OtherDTO
{
    use DTOTrait;

    public string $defId;
    public string $concrete;
    /**
     * @var class-string|null
     */
    public ?string $arg;
    public bool $shared;

}