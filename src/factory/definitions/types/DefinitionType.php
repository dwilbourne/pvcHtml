<?php

/**
 * @author: Doug Wilbourne (dougwilbourne@gmail.com)
 */

declare(strict_types=1);

namespace pvc\html\factory\definitions\types;

/**
 * Class DefinitionType
 */
enum DefinitionType: string
{
    case Attribute = 'Attribute';
    case AttributeValueTester = 'AttributeValueTester';
    case Element = 'Element';
    case Event = 'Event';
    case Other = 'Other';

    public static function fromName(string $name): ?DefinitionType
    {
        foreach (self::cases() as $status) {
            if ($name === $status->name) {
                return $status;
            }
        }
        return null;
    }
}
