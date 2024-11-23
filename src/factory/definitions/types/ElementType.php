<?php

/**
 * @author: Doug Wilbourne (dougwilbourne@gmail.com)
 */

declare(strict_types=1);

namespace pvc\html\factory\definitions\types;

use pvc\html\tag\Tag;
use pvc\html\tag\TagVoid;

/**
 * Class ElementType
 */
enum ElementType: string
{
    use GetClassTrait;

    case TagVoid = TagVoid::class;
    case Tag = Tag::class;
}
