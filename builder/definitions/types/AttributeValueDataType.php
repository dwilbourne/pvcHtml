<?php

namespace pvc\htmlbuilder\definitions\types;

enum AttributeValueDataType: string
{
    case String = 'string';
    case Integer = 'int';
    case Bool = 'bool';
    case Array = 'array';
}