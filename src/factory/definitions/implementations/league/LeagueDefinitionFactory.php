<?php

/**
 * @author: Doug Wilbourne (dougwilbourne@gmail.com)
 */

declare(strict_types=1);

namespace pvc\html\factory\definitions\implementations\league;

use League\Container\Argument\LiteralArgument;
use League\Container\Definition\Definition;
use League\Container\Definition\DefinitionInterface;
use pvc\html\err\DTOInvalidPropertyValueException;
use pvc\html\err\DTOMissingPropertyException;
use pvc\html\factory\definitions\dto\AttributeDTO;
use pvc\html\factory\definitions\dto\AttributeValTesterDTO;
use pvc\html\factory\definitions\dto\ElementDTO;
use pvc\html\factory\definitions\dto\EventDTO;
use pvc\html\factory\definitions\dto\OtherDTO;
use pvc\html\factory\definitions\types\AttributeType;
use pvc\html\factory\definitions\types\ElementType;
use pvc\html\factory\definitions\types\EventType;
use pvc\html\val_tester\EventScriptTester;
use pvc\interfaces\html\factory\definitions\DefinitionFactoryInterface;
use pvc\interfaces\validator\ValTesterInterface;
use Throwable;

/**
 * Class LeagueDefinitionFactory
 *
 * @phpstan-import-type DefArray from DefinitionFactoryInterface
 * @implements DefinitionFactoryInterface<Definition>
 */
class LeagueDefinitionFactory implements DefinitionFactoryInterface
{
    /**
     * makeAttributeDefinition
     * @param DefArray $definitionArray
     * @return DefinitionInterface
     * @throws DTOMissingPropertyException
     */
    public function makeAttributeDefinition(array $definitionArray): mixed
    {
        $attributeDTO = new AttributeDTO();
        $attributeDTO->permitExtraProperties();
        $attributeDTO->hydrateFromArray($definitionArray);

        if (!$class = AttributeType::getClass($attributeDTO->concrete)) {
            throw new DTOInvalidPropertyValueException('concrete', $attributeDTO->concrete, 'Attribute');
        }

        $def = (new Definition($attributeDTO->defId, $class))
            ->addMethodCall('setDefId', [new LiteralArgument($attributeDTO->defId)])
            ->addMethodCall('setName', [new LiteralArgument($attributeDTO->name)])
            ->addMethodCall('setGlobal', [$attributeDTO->global]);

        if ($attributeDTO->valTester) {
            $def->addMethodCall('setTester', [$attributeDTO->valTester])
                ->addMethodCall('setCaseSensitive', [$attributeDTO->caseSensitive]);
        }
        return $def;
    }

    /**
     * isValTester
     * @param string $valTester
     * @return bool
     */
    protected function isValTester(string $valTester): bool
    {
        try {
            /** @phpstan-ignore argument.type */
            $reflection = new \ReflectionClass($valTester);
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
     * @param DefArray $definitionArray
     * @return DefinitionInterface
     */
    public function makeAttributeValueTesterDefinition(array $definitionArray): mixed
    {
        $attributeValTesterDTO = new AttributeValTesterDTO();
        $attributeValTesterDTO->permitExtraProperties();
        $attributeValTesterDTO->hydrateFromArray($definitionArray);
        
        if (!$this->isValTester($attributeValTesterDTO->concrete)) {
            throw new DTOInvalidPropertyValueException('valTester', $attributeValTesterDTO->concrete, 'AttributeValtesterDTO');
        }

        return (new Definition($attributeValTesterDTO->defId, $attributeValTesterDTO->concrete))
            ->addArgument($attributeValTesterDTO->arg)
            ->setShared(true);
    }


    /**
     * isArrayOfStrings
     * @param array<mixed> $array
     * @return bool
     */
    protected function isArrayOfStrings(array $array): bool
    {
        $callback = function (bool $carry, mixed $x) {
            return $carry && is_string($x);
        };
        return array_reduce($array, $callback, true);
    }


    /**
     * makeElementDefinition
     * @param DefArray $definitionArray
     * @return DefinitionInterface
     */
    public function makeElementDefinition(array $definitionArray): mixed
    {
        $elementDTO = new ElementDTO();
        $elementDTO->permitExtraProperties();
        $elementDTO->hydrateFromArray($definitionArray);

        if (!$class = ElementType::getClass($elementDTO->concrete)) {
            throw new DTOInvalidPropertyValueException('concrete', $elementDTO->concrete, 'ElementDTO');
        }

        if (!$this->isArrayOfStrings($elementDTO->allowedAttributeDefIds)) {
            $childDefIds = implode(',', $elementDTO->allowedAttributeDefIds);
            throw new DTOInvalidPropertyValueException('allowedAttributeDefIds', $childDefIds, 'ElementDTO');
        }

        if (!$this->isArrayOfStrings($elementDTO->allowedChildDefIds)) {
            throw new DTOInvalidPropertyValueException('allowedChildDefIds', $elementDTO->allowedChildDefIds, 'ElementDTO');
        }

        $def = (new Definition($elementDTO->defId, $class))
            ->addMethodCall('setName', [new LiteralArgument($elementDTO->name)])
            ->addMethodCall('setAllowedAttributeDefIds', [$elementDTO->allowedAttributeDefIds]);

        /**
         * child elements are only applicable to the Tag class (not TagVoid)
         */
        if ($elementDTO->concrete == 'Tag') {
            $def->addMethodCall('setAllowedChildDefIds', [$elementDTO->allowedChildDefIds]);
        }
        return $def;
    }

    /**
     * makeEventDefinition
     * @param DefArray $definitionArray
     * @return DefinitionInterface
     */
    public function makeEventDefinition(array $definitionArray): mixed
    {
        $eventDTO = new EventDTO();
        $eventDTO->permitExtraProperties();
        $eventDTO->hydrateFromArray($definitionArray);

        if (!$class = EventType::getClass($eventDTO->concrete)) {
            throw new DTOInvalidPropertyValueException('concrete', $eventDTO->concrete, 'EventDTO');
        }

        return (new Definition($eventDTO->defId, $class))
            ->addArgument(EventScriptTester::class)
            ->addMethodCall('setDefId', [new LiteralArgument($eventDTO->defId)]);
    }

    /**
     * makeOtherSharedDefinition
     * @param DefArray $definitionArray
     * @return DefinitionInterface
     */
    public function makeOtherDefinition(array $definitionArray): mixed
    {
        $otherDTO = new OtherDTO();
        $otherDTO->permitExtraProperties();
        $otherDTO->hydrateFromArray($definitionArray);

        if (!class_exists($otherDTO->concrete)) {
            throw new DTOInvalidPropertyValueException('concrete', $otherDTO->concrete, 'OtherDTO');
        }

        $def = (new Definition($otherDTO->defId, $otherDTO->concrete))
            ->setShared($otherDTO->shared);
        /**
         * not all the constructors have arguments....
         */
        if ($otherDTO->arg) {
            $def->addArgument($otherDTO->arg);
        }
        return $def;
    }
}
