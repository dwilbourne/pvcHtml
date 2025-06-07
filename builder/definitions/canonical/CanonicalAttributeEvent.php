<?php

declare(strict_types=1);

namespace pvc\htmlbuilder\definitions\canonical;

use pvc\htmlbuilder\Builder;

/**
 * taks a definition array and creates canonical components which can be
 * used for generating the php code which defines attributes
 * and events.  For example, the class name of an attribute is the attribute
 * name but with the hyphens removed and converted to camel case.
 *
 * Events are really a specific kind of single-valued attribute so in this
 * class the word Attribute includes events.
 */

/**
 * @phpstan-import-type AttributeDefArray from Builder
 * @phpstan-import-type EventDefArray from Builder
 */
abstract readonly class CanonicalAttributeEvent
{
    public string $virtualAttributeName;
    public string $traitName;
    public string $backingAttributeClassName;
    public string $backingAttributeVariableName;
    public string $setterMethodName;
    public string $getterMethodName;

    public string $dataType;

    /**
     * @param AttributeDefArray|EventDefArray $jsonDef
     */
    public function __construct(array $jsonDef)
    {
        $this->virtualAttributeName = $this->getName($jsonDef);
        $this->backingAttributeClassName = $jsonDef['concrete'];
        $this->backingAttributeVariableName = $this->getObjectName($jsonDef);
        $this->traitName = ucfirst($this->virtualAttributeName) . 'Trait';
        $this->setterMethodName = 'set' . ucfirst($this->virtualAttributeName);
        $this->getterMethodName = 'get' . ucfirst($this->virtualAttributeName);
        $this->dataType = $this->getDataType($jsonDef);
    }

    /**
     * @param  AttributeDefArray|EventDefArray  $jsonDef
     * @return string
     */
    abstract protected function getName(array $jsonDef): string;

    /**
     * @param AttributeDefArray|EventDefArray $jsonDef
     * @return string
     */
    abstract protected function getObjectName(array $jsonDef): string;

    /**
     * @return string
     */
    abstract protected function getBaseObjectSetterName(): string;

    /**
     * @return string
     */
    abstract protected function getBaseObjectGetterName(): string;

    /**
     * @param  AttributeDefArray|EventDefArray  $jsonDef
     * @return string
     */
    abstract protected function getDataType(array $jsonDef): string;

}