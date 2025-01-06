<?php

/**
 * @author: Doug Wilbourne (dougwilbourne@gmail.com)
 */

declare(strict_types=1);

namespace pvc\html\builder\definitions\types;

use pvc\html\element\Element;
use pvc\html\element\ElementVoid;

/**
 * Class ElementType
 */
enum ElementType: string
{
    use GetClassTrait;

    case TagVoid = ElementVoid::class;
    case Tag = Element::class;
}
