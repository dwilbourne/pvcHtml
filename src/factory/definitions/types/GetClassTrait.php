<?php

/**
 * @author: Doug Wilbourne (dougwilbourne@gmail.com)
 */
declare(strict_types=1);

namespace pvc\html\factory\definitions\types;

/**
 * Class GetClassTrait
 */
trait GetClassTrait
{
    /**
     * getClass
     * @param string $name
     * @return class-string|null
     */
    public static function getClass(string $name): ?string
    {
        foreach (self::cases() as $type) {
            if ($name === $type->name) {
                return $type->value;
            }
        }
        return null;
    }
}