<?php

/**
 * @author: Doug Wilbourne (dougwilbourne@gmail.com)
 */
declare(strict_types=1);

namespace pvc\html\builder\definitions\dto;

/**
 * Class AttributeValTesterDef
 */
readonly class AttributeValTesterDTO
{
    use DTOTrait;

    public string $defId;
    public string $concrete;
    /**
     * @var class-string|array<string>|null
     */
    public string|array|null $arg;
}