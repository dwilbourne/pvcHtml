<?php

/**
 * @author: Doug Wilbourne (dougwilbourne@gmail.com)
 */
declare(strict_types=1);

namespace pvc\html\builder\definitions\dto;

/**
 * Class AttributeDef
 */
readonly class AttributeDTO
{
    use DTOTrait;

    public string $defId;
    public string $concrete;
    public string $name;
    public string $valTester;
    public bool $caseSensitive;
    public bool $global;
}