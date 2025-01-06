<?php

/**
 * @author: Doug Wilbourne (dougwilbourne@gmail.com)
 */
declare(strict_types=1);

namespace pvc\html\builder\definitions\dto;

/**
 * Class ElementDef
 */
readonly class ElementDTO
{
    use DTOTrait;

    public string $defId;
    public string $concrete;
    public string $name;

    /**
     * @var array<string>
     */
    public array $allowedAttributeDefIds;

    /**
     * @var array<string>
     */
    public array $allowedChildDefIds;
}