<?php

/**
 * @author: Doug Wilbourne (dougwilbourne@gmail.com)
 */
declare(strict_types=1);

namespace pvc\htmlbuilder\definitions\dto;

/**
 * Class AttributeDef
 */
readonly class AttributeDTO
{
    use DTOTrait;

    public string $name;
    public string $concrete;
    public string $dataType;
    public bool $caseSensitive;
    public string $valTester;

}