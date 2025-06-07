<?php

namespace pvc\htmlbuilder\definitions\canonical;

use pvc\html\err\GetDataTypeException;
use pvc\htmlbuilder\Builder;
use pvc\htmlbuilder\definitions\types\AttributeValueDataType;

/**
 * @phpstan-import-type AttributeDefArray from Builder
 */
readonly class CanonicalAttribute extends CanonicalAttributeEvent
{

    /**
     * @param  AttributeDefArray  $jsonDef
     *
     * @return string
     *
     * There are a few attributes that have hyphens in them.
     * Best to remove the hyphens and make them camel case so they conform with standard naming rules.
     */
    protected function getName(array $jsonDef): string
    {
        $jsonDefName = $jsonDef['name'];
        $pattern = '/(-.)/';
        preg_match($pattern, $jsonDefName, $matches);
        /**
         * $matches[0] is the whole $attributeName, $matches[1] is the first
         * captured subpattern, $matches[2] is the second, etc.
         */
        if (!empty($matches)) {
            for ($i = 1; $i < count($matches); $i++) {
                /**
                 * there are no attributes ending with a hyphen so no need to check
                 * for the existence of the subsequent character.
                 */
                $replace = strtoupper(substr($matches[$i], 1, 1));
                $jsonDefName = str_replace($matches[$i], $replace, $jsonDefName);
            }
        }
        return $jsonDefName;
    }

    /**
     * @param  AttributeDefArray  $jsonDef
     *
     * @return string
     */
    public function getObjectName(array $jsonDef): string
    {
        return $this->getName($jsonDef) . 'Attribute';
    }

    /**
     * @return string
     */
    public function getBaseObjectSetterName(): string
    {
        return 'setAttribute';
    }

    /**
     * @return string
     */
    public function getBaseObjectGetterName(): string
    {
        return 'getAttribute';
    }

    /**
     * @param  AttributeDefArray  $jsonDef
     *
     * @return string
     */
    protected function getDataType(array $jsonDef): string
    {
        if (!$type = AttributeValueDataType::tryFrom($jsonDef['dataType'])) {
            throw new GetDataTypeException($jsonDef['name']);
        }
        return $type->value;
    }
}