<?php

/**
 * @author: Doug Wilbourne (dougwilbourne@gmail.com)
 */
declare(strict_types=1);

namespace pvcTests\html\unit_tests\factory\definitions\dto;

use pvc\html\factory\definitions\dto\DTOTrait;

/**
 * Class AttributeDef
 */
readonly class AttributeDTO
{
    use DTOTrait;

    public string $defId;
    public string $defType;
    public string $concrete;
    public string $name;
    public string $valTester;
    public bool $caseSensitive;
    public bool $global;
}