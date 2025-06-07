<?php

/**
 * @author: Doug Wilbourne (dougwilbourne@gmail.com)
 */
declare(strict_types=1);

namespace pvc\htmlbuilder\definitions\dto;

use pvc\html\err\DTOExtraPropertyException;
use pvc\html\err\DTOInvalidPropertyValueException;
use pvc\html\err\DTOMissingPropertyException;
use ReflectionClass;
use ReflectionProperty;
use Throwable;

/**
 * Class DTOTrait
 */
trait DTOTrait
{
    /**
     * @param array<string> $constructorProperties
     * @throws DTOExtraPropertyException
     * @throws DTOMissingPropertyException
     * @throws DTOInvalidPropertyValueException
     */
    public function hydrateFromArray(array $constructorProperties): void
    {
        $reflection = new ReflectionClass(static::class);

        $className = $reflection->getName();

        $requiredProperties = array_map(
            function (ReflectionProperty $value): string {
                return $value->getName();
            },
            $reflection->getProperties(ReflectionProperty::IS_PUBLIC)
        );

        $missingProperties = array_diff($requiredProperties, array_keys($constructorProperties));
        if ($missingProperties) {
            throw new DTOMissingPropertyException(implode(',', $missingProperties), $className);
        }

        foreach ($constructorProperties as $propertyName => $propertyValue) {
            if (in_array($propertyName, $requiredProperties)) {
                try {
                    $this->{$propertyName} = $propertyValue;
                } catch (Throwable $e) {
                    throw new DTOInvalidPropertyValueException($propertyName, $propertyValue, $className);
                }
            }
        }
    }

    /**
     * toArray
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return (array) $this;
    }
}