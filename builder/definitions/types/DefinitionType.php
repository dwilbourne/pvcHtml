<?php

/**
 * @author: Doug Wilbourne (dougwilbourne@gmail.com)
 */

declare(strict_types=1);

namespace pvc\htmlbuilder\definitions\types;

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
}
