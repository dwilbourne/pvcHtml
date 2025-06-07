<?php

declare(strict_types=1);

namespace pvc\htmlbuilder\html;

use pvc\html\factory\DefIdResolver;
use pvc\htmlbuilder\Builder;
use pvc\htmlbuilder\config\BaseConfig;
use pvc\htmlbuilder\config\BuilderConfig;
use pvc\htmlbuilder\definitions\canonical\CanonicalAttribute;
use pvc\htmlbuilder\definitions\canonical\CanonicalElement;
use pvc\htmlbuilder\definitions\types\DefinitionType;

/**
 * @phpstan-import-type ElementDefArray from Builder
 * @phpstan-import-type AttributeDefArray from Builder
 */
class ElementBuilder extends Builder
{
    public function __construct(
        BaseConfig $baseConfig,
        protected BuilderConfig $elementConfig,
        protected BuilderConfig $attributeConfig,
        protected BuilderConfig $eventConfig,
        protected string $htmlBuilderClassString,
    ) {
        parent::__construct($baseConfig);
    }

    public function makeElementClasses(): void
    {
        $elementDefinitionsArray = $this->getDefinitionArrayFromJsonFile($this->elementConfig->jsonDefs);
        foreach ($elementDefinitionsArray as $jsonDef) {
            $this->makeElementClass($jsonDef);
        }
    }

    /**
     * @param  ElementDefArray  $jsonDef
     *
     * @return CanonicalElement
     */
    public function getCanonical(array $jsonDef): CanonicalElement
    {
        return new CanonicalElement($jsonDef);
    }

    /**
     * @param  ElementDefArray $jsonDef
     *
     * @return void
     */
    public function makeElementClass(array $jsonDef): void
    {
        $canonical = $this->getCanonical($jsonDef);
        $attributeNames = $jsonDef['attributeNames'];
        $callBack = function(string $name) { return DefIdResolver::getDefIdFromName($name, DefinitionType::Attribute); };
        $attributeDefIds = array_map($callBack, $attributeNames);



        /**
         * flip values and keys of $attributeNames and take the intersection.  Values
         * come from the first argument, which contains the definition arrays
         */
        $attributeDefsArray = $this->getDefinitionArrayFromJsonFile($this->attributeConfig->jsonDefs);
        $attributeDefsArray = array_intersect_key($attributeDefsArray, array_flip($attributeDefIds));
        $caArray = [];
        foreach ($attributeDefsArray as $defId => $attributeDef) {
            $caArray[$defId] = new CanonicalAttribute($attributeDef);
        }

        $z = $this->makeHeader($this->elementConfig->targetNamespace);

        $z .= 'use ' . $this->elementConfig->baseNamespace . '\\' . $canonical->baseClass . ';' . PHP_EOL;

        if ($canonical->baseClass == 'Element') {
            $z .= 'use ' . $this->htmlBuilderClassString . ';' . PHP_EOL;
        }

        $z .= 'class ' . $canonical->name . ' extends ' . $canonical->baseClass . PHP_EOL;

        $z .= '{' . PHP_EOL;
        $z .= $this->makeUseStmts($this->attributeConfig->targetNamespace, $caArray);
        $z .= $this->makeConstructor($canonical->baseClass);
        $z .= '}' . PHP_EOL;
        $targetFileName = $this->elementConfig->targetDir . '/' . $canonical->name . '.php';
        file_put_contents($targetFileName, $z);
    }

    /**
     * @param  array<CanonicalAttribute>  $caArray
     *
     * @return string
     */
    protected function makeUseStmts(string $targetNamespace, array $caArray): string
    {

        $z = '';
        foreach ($caArray as $ca) {
            $z .= 'use \\';
            $z .= $targetNamespace . '\\';
            $z .= $ca->traitName . ';' . PHP_EOL;
        }
        return $z;
    }

    /**
     * @param  string $baseElementClass
     *
     * @return string
     */
    protected function makeConstructor(string $baseElementClass): string
    {
        $z = 'public function __construct(' . PHP_EOL;
        $z .= 'string $name,' . PHP_EOL;
        $z .= 'array $attributeObjects,' . PHP_EOL;
        $z .= 'array $elementObjects,' . PHP_EOL;
        if ($baseElementClass == 'Element') {
            $z .= 'HtmlFactory $htmlFactory';
        }
        $z .= ')' . PHP_EOL;
        $z .= '{' . PHP_EOL;
        $z .= 'parent::__construct($name, $attributeObjects, $elementObjects,' . PHP_EOL;
        if ($baseElementClass == 'Element') {
            $z .= '$htmlFactory';
        }
        $z .= ');' . PHP_EOL;
        $z .= '}' . PHP_EOL;
        return $z;
    }
}