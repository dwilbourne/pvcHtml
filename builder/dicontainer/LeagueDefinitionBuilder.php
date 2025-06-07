<?php

/**
 * @author: Doug Wilbourne (dougwilbourne@gmail.com)
 */

declare(strict_types=1);

namespace pvc\htmlbuilder\dicontainer;

use pvc\html\err\DTOInvalidPropertyValueException;
use pvc\html\err\DTOMissingPropertyException;
use pvc\html\factory\DefIdResolver;
use pvc\htmlbuilder\Builder;
use pvc\htmlbuilder\config\BaseConfig;
use pvc\htmlbuilder\config\BuilderConfig;
use pvc\htmlbuilder\definitions\dto\AttributeDTO;
use pvc\htmlbuilder\definitions\dto\AttributeValTesterDTO;
use pvc\htmlbuilder\definitions\dto\ElementDTO;
use pvc\htmlbuilder\definitions\dto\EventDTO;
use pvc\htmlbuilder\definitions\dto\OtherDTO;
use pvc\htmlbuilder\definitions\types\AttributeType;
use pvc\htmlbuilder\definitions\types\DefinitionType;
use pvc\htmlbuilder\definitions\types\ElementType;
use pvc\htmlbuilder\definitions\types\EventType;
use pvc\interfaces\validator\ValTesterInterface;
use ReflectionClass;
use Throwable;

/**
 * Class LeagueDefinitionFactory
 */
class LeagueDefinitionBuilder extends Builder
{
    public function __construct(
        BaseConfig $baseConfig,
        protected BuilderConfig $attributeConfig,
        protected string $attributeValueTesterJsonDefs,
        protected string $otherJsonDefs,
        protected BuilderConfig $eventConfig,
        protected BuilderConfig $elementConfig,
    ) {
        parent::__construct($baseConfig);
    }

    /**
     * @return void
     * @throws DTOInvalidPropertyValueException
     * @throws DTOMissingPropertyException
     * @throws \pvc\html\err\InvalidDefinitionsFileException
     */
    public function makeLeagueDefinitions(): void
    {
        $jsonFiles = [
            $this->attributeConfig->jsonDefs,
            $this->attributeValueTesterJsonDefs,
            $this->otherJsonDefs,
            $this->eventConfig->jsonDefs,
            $this->elementConfig->jsonDefs,
        ];

        $z = self::makeHeader($this->baseConfig->containerDefsNamespace);
        $z .= 'use League\Container\Definition\Definition;' . PHP_EOL;
        $z .= 'use League\Container\Argument\LiteralArgument;' . PHP_EOL;
        $z .= 'use pvc\html\val_tester\EventScriptTester;' . PHP_EOL;

        /**
         * and here are all the definitions
         */
        $z .= 'return [' . PHP_EOL;

        foreach ($jsonFiles as $jsonFile) {
            /** @var array<array<string>> $defs */
            $defs = self::getDefinitionArrayFromJsonFile($jsonFile);
            foreach ($defs as $def) {
                $z .= self::makeDefinition($def);
            }
        }
        $z .= '];'.PHP_EOL;

        file_put_contents($this->baseConfig->containerDefsFilePath, $z);
    }

    /**
     * makeDefinition
     *
     * @param  array<string>  $defArray
     *
     * @return string
     * @throws DTOInvalidPropertyValueException|DTOMissingPropertyException
     */
    protected function makeDefinition(array $defArray): string
    {
        /** @var DefinitionType $defType */
        $defType = DefinitionType::tryFrom($defArray['defType']);

        $result = match ($defType) {
            DefinitionType::Attribute => $this->makeAttributeDefinition($defArray),
            DefinitionType::AttributeValueTester => $this->makeAttributeValueTesterDefinition($defArray),
            DefinitionType::Element => $this->makeElementDefinition($defArray),
            DefinitionType::Event => $this->makeEventDefinition($defArray),
            DefinitionType::Other => $this->makeOtherDefinition($defArray),
        };
        return $result;
    }

    protected static function formatBool(bool $bool): string
    {
        return $bool ? 'true' : 'false';
    }

    /**
     * makeAttributeDefinition
     *
     * @param  array<string>  $definitionArray
     *
     * @return string
     * @throws DTOMissingPropertyException
     */
    protected function makeAttributeDefinition(array $definitionArray
    ): string {
        $attributeDTO = new AttributeDTO();
        $attributeDTO->hydrateFromArray($definitionArray);

        if (!$class = AttributeType::getClass($attributeDTO->concrete)) {
            throw new DTOInvalidPropertyValueException('concrete',
                $attributeDTO->concrete, 'Attribute');
        }
        $defId = DefIdResolver::getDefIdFromName($attributeDTO->name, DefinitionType::Attribute);
        $def = "(new Definition('" . $defId . "', '" . $class . "'))" . PHP_EOL;
        $def .= "->addArgument([new LiteralArgument('" . $attributeDTO->name . "')])" . PHP_EOL;
        $def .= "->addArgument([new LiteralArgument('" . $attributeDTO->dataType . "')])" . PHP_EOL;
        $def .= "->addArgument(" . self::formatBool($attributeDTO->caseSensitive) . ")" . PHP_EOL;
        $def .= "->addArgument([new LiteralArgument('" . $attributeDTO->valTester . "')])," . PHP_EOL . PHP_EOL;
        return $def;
    }

    /**
     * isValTester
     *
     * @param  string  $valTester
     *
     * @return bool
     */
    protected static function isValTester(string $valTester): bool
    {
        try {
            /** @phpstan-ignore argument.type */
            $reflection = new ReflectionClass($valTester);
            if (!$reflection->implementsInterface(ValTesterInterface::class)) {
                return false;
            }
        } catch (Throwable $e) {
            return false;
        }
        return true;
    }


    /**
     * makeAttributeValueTesterDefinition
     *
     * @param  array<string>  $definitionArray
     *
     * @return string
     */
    protected function makeAttributeValueTesterDefinition(
        array $definitionArray
    ): string {
        $attributeValTesterDTO = new AttributeValTesterDTO();
        $attributeValTesterDTO->hydrateFromArray($definitionArray);

        if (!self::isValTester($attributeValTesterDTO->concrete)) {
            throw new DTOInvalidPropertyValueException('valTester',
                $attributeValTesterDTO->concrete, 'AttributeValtesterDTO');
        }
        $defId = DefIdResolver::getDefIdFromName($attributeValTesterDTO->name, DefinitionType::AttributeValueTester);
        $def= "(new Definition('" . $defId . "', '" . $attributeValTesterDTO->concrete . "'))" . PHP_EOL;

        /**
         * arg is either a string (which should be resolved) or an array of literal arguments
         */
        if (is_array($attributeValTesterDTO->arg)) {
            $arg = self::arrayToLiteral($attributeValTesterDTO->arg);
        } else {
            $arg = "'" . $attributeValTesterDTO->arg . "'";
        }

        $def .= "->addArgument(" . $arg . ")" . PHP_EOL;
        $def .= "->setShared(true)," . PHP_EOL . PHP_EOL;
        return $def;
    }


    /**
     * isArrayOfStrings
     * this is to verify that any arrays that appear in a definition are arrays
     * of strings.
     * @param  array<mixed>  $array
     *
     * @return bool
     */
    protected static function isArrayOfStrings(array $array): bool
    {
        $callback = function (bool $carry, mixed $x) {
            return $carry && is_string($x);
        };
        return array_reduce($array, $callback, true);
    }

    /**
     * @param  array<string>  $array
     *
     * @return string
     */
    protected static function arrayToLiteral(array $array): string
    {
        $string = 'new LiteralArgument([';
        foreach ($array as $value) {
            $string .= "'" . $value . "',";
        }
        $string .= '])';
        return $string;
    }

    protected static function boolToString(bool $bool): string
    {
        return $bool ? 'true' : 'false';
    }


    /**
     * makeElementDefinition
     *
     * @param  array<string>  $definitionArray
     *
     * @return string
     */


    protected function makeElementDefinition(array $definitionArray
    ): string {
        $elementDTO = new ElementDTO();
        $elementDTO->hydrateFromArray($definitionArray);

        if (!$class = ElementType::getClass($elementDTO->concrete)) {
            throw new DTOInvalidPropertyValueException('concrete',
                $elementDTO->concrete, 'ElementDTO');
        }

        $defId = DefIdResolver::getDefIdFromName($elementDTO->name, DefinitionType::Element);
        $def = "(new Definition('" . $defId . "', '" . $class . "'))" . PHP_EOL;
        $def .= "->addArgument([new LiteralArgument('" . $elementDTO->name . "')])" . PHP_EOL;
        $def .= "->addArgument([new LiteralArgument(['" . implode('\', \'', $elementDTO->attributeNames) . "'])])" . PHP_EOL;
        $def .= "->addArgument('HtmlFactory::class'), " . PHP_EOL . PHP_EOL;
        return $def;
    }

    /**
     * makeEventDefinition
     *
     * @param  array<string>  $definitionArray
     *
     * @return string
     */
    protected function makeEventDefinition(array $definitionArray): mixed
    {
        $eventDTO = new EventDTO();
        $eventDTO->hydrateFromArray($definitionArray);

        if (!$class = EventType::getClass($eventDTO->concrete)) {
            throw new DTOInvalidPropertyValueException('concrete',
                $eventDTO->concrete, 'EventDTO');
        }

        /**
         * Event extends AttributeSingleValue so the construction seems a bit
         * complicated.
         */
        $defId = DefIdResolver::getDefIdFromName($eventDTO->name, DefinitionType::Event);
        $def = "(new Definition('" . $defId . "', '" . $class . "'))" . PHP_EOL;
        $def .= "->addArgument([new LiteralArgument('" . $eventDTO->name . "')])" . PHP_EOL;
        /**
         * events are like global attributes: you can add an event to any element
         */
        $def .= "->addArgument([new LiteralArgument('true')])" . PHP_EOL;
        /**
         * scripts are case-sensitive because javascript is case-sensitive
         */
        $def .= "->addArgument([new LiteralArgument('true')])" . PHP_EOL;
        /**
         * some minimal testing to see if the string smells like javascript
         */
        $def .= "->addArgument(EventScriptTester::class)," . PHP_EOL . PHP_EOL;

        return $def;
    }

    /**
     * makeOtherSharedDefinition
     *
     * @param  array<string>  $definitionArray
     *
     * @return string
     */
    protected function makeOtherDefinition(array $definitionArray
    ): string {
        $otherDTO = new OtherDTO();
        $otherDTO->hydrateFromArray($definitionArray);

        if (!class_exists($otherDTO->concrete)) {
            throw new DTOInvalidPropertyValueException('concrete',
                $otherDTO->concrete, 'OtherDTO');
        }
        $defId = DefIdResolver::getDefIdFromName($otherDTO->name, DefinitionType::Other);
        $def = "(new Definition('" . $defId . "', '" . $otherDTO->concrete . "'))" . PHP_EOL;

        /**
         * not all the constructors have arguments. If there are no arguments then
         * the value is an empty string.  But leave the empty verb below in case we change our
         * minds and want the json defs to use null instead of an empty string
         */
        if (!empty($otherDTO->arg)) {
            $def .= "->addArgument('" . $otherDTO->arg . "')" . PHP_EOL;
        }
        $def .= "->setShared(" . self::boolToString($otherDTO->shared) .")," . PHP_EOL . PHP_EOL;
        return $def;
    }

    /**
     * @param  array  $jsonDef
     *
     * @return null
     * this method is not used in this class
     */
    function getCanonical(array $jsonDef): null
    {
        return null;
    }
}
