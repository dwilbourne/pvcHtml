<?php

/**
 * @author: Doug Wilbourne (dougwilbourne@gmail.com)
 */

declare(strict_types=1);

namespace pvc\html\builder\definitions\types;

use pvc\interfaces\html\builder\definitions\DefinitionType;

/**
 * Class DefinitionTypeFromStringTrait
 */
trait DefinitionTypeFromStringTrait
{
    public static function fromName(string $name): ?DefinitionType
    {
        foreach (DefinitionType::cases() as $status) {
            if ($name === $status->name) {
                return $status;
            }
        }
        return null;
    }
}
