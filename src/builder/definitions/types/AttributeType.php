<?php

/**
 * @author: Doug Wilbourne (dougwilbourne@gmail.com)
 */
declare(strict_types=1);

namespace pvc\html\builder\definitions\types;

use pvc\html\attribute\AttributeMultiValue;
use pvc\html\attribute\AttributeSingleValue;
use pvc\html\attribute\AttributeVoid;

/**
 * Class AttributeType
 */
enum AttributeType: string
{
    use GetClassTrait;

    case AttributeVoid = AttributeVoid::class;
    case AttributeSingleValue = AttributeSingleValue::class;
    case AttributeMultiValue = AttributeMultiValue::class;

}
