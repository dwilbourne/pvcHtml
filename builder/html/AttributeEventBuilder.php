<?php

declare(strict_types=1);

namespace pvc\htmlbuilder\html;

use pvc\html\err\InvalidDefinitionsFileException;
use pvc\htmlbuilder\Builder;
use pvc\htmlbuilder\config\BaseConfig;
use pvc\htmlbuilder\config\BuilderConfig;
use pvc\htmlbuilder\definitions\canonical\CanonicalAttribute;
use pvc\htmlbuilder\definitions\canonical\CanonicalEvent;

/**
 * the easiest way to see what we are trying to build is to see an example.  This is what the generated code looks
 * like when building the 'accept' attribute.  Since events are a simple kind of attribute, this
 * same code is used to generate events.  There is a slightly different class that builds elements.
 */

/*

<?php

declare(strict_types=1);

namespace pvc\html\attributes;

use pvc\html\attribute\AttributeMultiValue;
use pvc\html\err\InvalidAttributeValueException;
use pvc\html\err\InvalidNumberOfParametersException;

trait AcceptTrait
{
    public array<string|int>|string|int|bool $accept
        {
            set {
                $this->setAccept($value);
            }
            get => $this->getAccept();
        }

    public function setAccept(...$values): self
    {
        $this->attributes['accept']->setValue($values);
        return $this;
    }

    public function getAccept(): array|string|int|bool
    {
        return $this->>attributes['accept']->getValue();
    }
}

*/

/**
 * @phpstan-import-type DefArray from Builder
 * @phpstan-import-type AttributeDefArray from Builder
 * @phpstan-import-type EventDefArray from Builder
 */
abstract class AttributeEventBuilder extends Builder
{
    public function __construct(
        BaseConfig $baseConfig,
        protected BuilderConfig $builderConfig,
    ) {
        parent::__construct($baseConfig);
    }

    /**
     * @throws InvalidDefinitionsFileException
     */
    public function makeTraits(): void
    {
        $definitionsArray = $this->getDefinitionArrayFromJsonFile($this->builderConfig->jsonDefs);
        /** @var DefArray $jsonDef */
        foreach ($definitionsArray as $jsonDef) {
            $this->makeTrait($jsonDef);
        }
    }

    /**
     * @param AttributeDefArray|EventDefArray $jsonDef
     *
     * @return CanonicalAttribute|CanonicalEvent
     */
    abstract function getCanonical(array $jsonDef): CanonicalAttribute|CanonicalEvent;

    /**
     * @param AttributeDefArray|EventDefArray $jsonDef
     * @return void
     */
    protected function makeTrait(array $jsonDef): void
    {
        $canonical = $this->getCanonical($jsonDef);

        $z = $this->makeHeader($this->builderConfig->targetNamespace);

        $import = 'use ' . $this->builderConfig->baseNamespace . '\\' . $canonical->backingAttributeClassName . ';' . PHP_EOL;
        /**
         * these exceptions can be thrown in the course of setting the value of the attribute
         */
        foreach ($this->exceptionNames as $exceptionName) {
            $import .= 'use ' . $this->baseConfig->exceptionNamespace . '\\' . $exceptionName . ';' . PHP_EOL;
        }
        $z .= $import;

        $z .= 'trait ' . $canonical->traitName . PHP_EOL;

        $z .= '{' . PHP_EOL;

        /**
         * declare the virtual property
         */
        $z .= 'public ' . $canonical->dataType . ' $' . $canonical->virtualAttributeName .PHP_EOL;

        /**
         * open the scope for the property hooks
         */
        $z .= '{' . PHP_EOL;

        /**
         * define the setter property hook.  This is just a method call to a method defined below.
         * no dummy parameter name is declared in the hook, php assumes the variable '$value'
         */
        $z .= 'set { $this->' . $canonical->setterMethodName . '($value);}' . PHP_EOL;

        /**
         * the getter property hook has no method call
         */
        $z .= 'get => $this->' . $canonical->getterMethodName . '();' . PHP_EOL;

        /**
         * close the scope for the property hooks
         */
        $z .= '}' . PHP_EOL;

        $methodCall = 'public function ' . $canonical->setterMethodName . '(...$values): self' . PHP_EOL;
        $methodCall .= '{' . PHP_EOL;
        $methodCall .= '$this->' . $canonical->getBaseObjectSetterName() . '(\'' . $canonical->virtualAttributeName . '\', ...$values);' . PHP_EOL;
        $methodCall .= 'return $this;' . PHP_EOL;
        $methodCall .= '}' . PHP_EOL;
        $z .= $methodCall;

        $methodCall = 'public function ' . $canonical->getterMethodName . '(): ' . $canonical->dataType . PHP_EOL;
        $methodCall .= '{' . PHP_EOL;
        $methodCall .= 'return $this->' . $canonical->getBaseObjectGetterName() . '(\'' . $canonical->virtualAttributeName . '\')->getValue();' . PHP_EOL;
        $methodCall .= '}' . PHP_EOL;
        $z .= $methodCall;

        $z .= '}' . PHP_EOL;

        $targetFileName = $this->builderConfig->targetDir . '/' . $canonical->traitName . '.php';
        file_put_contents($targetFileName, $z);
    }
}
