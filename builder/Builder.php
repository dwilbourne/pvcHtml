<?php

declare(strict_types=1);

namespace pvc\htmlbuilder;

use pvc\html\err\InvalidDefinitionsFileException;
use pvc\htmlbuilder\config\BaseConfig;
use pvc\htmlbuilder\definitions\canonical\CanonicalAttribute;
use pvc\htmlbuilder\definitions\canonical\CanonicalElement;
use pvc\htmlbuilder\definitions\canonical\CanonicalEvent;
use pvc\htmlbuilder\definitions\types\DefinitionType;

/**
 * @phpstan-type DefArray array{'name':string,'defType':string,'concrete':string}
 * @phpstan-type AttributeDefArray array{'name':string,'defType':string,'concrete':string,'global':bool,'dataType':string,'caseSensitive':bool,'valTester':string}
 * @phpstan-type ElementDefArray array{'name':string,'defType':string,'concrete':string,'comment':string,'attributeNames':array<string>}
 * @phpstan-type EventDefArray array{'name':string,'defType':string,'concrete':string}
 */
abstract class Builder
{
    /**
     * @var array<string>
     */
    protected array $exceptionNames = [
        'InvalidNumberOfParametersException',
        'InvalidAttributeValueException',
    ];

    public function __construct(
        protected BaseConfig $baseConfig,
    ) {}


    /**
     * getDefinitionArray
     * @return array<string, array<array<string>|string|bool>>
     * @throws InvalidDefinitionsFileException
     */
    public static function getDefinitionArrayFromJsonFile(string $definitionsFile): array
    {
        /** @var string $jsonString */
        $jsonString = file_get_contents($definitionsFile);

        /**
         * the values in the json definitions are only strings or booleans.  This fact
         * is verified in the LeagueDefinitionBuilder class as the definitions are constructed
         */
        /** @var array<array<array<string>|string|bool>>|null $defs */
        $defs = json_decode($jsonString, true);

        if (is_null($defs)) {
            throw new InvalidDefinitionsFileException($definitionsFile);
        }

        /**
         * reformat so that names are the keys to the array
         */
        $keyedDefs = [];
        foreach ($defs as $def) {
            /** @var string $name */
            $name = $def['name'];
            $keyedDefs[$name] = $def;
        }
        return $keyedDefs;
    }

    public static function makeHeader(string $targetNamespace): string
    {
        $z = '';
        $z .= '<?php' . PHP_EOL;
        $z .= 'declare(strict_types=1);' . PHP_EOL;
        $z .= 'namespace ' . $targetNamespace . ';' . PHP_EOL;
        return $z;
    }

    /**
     * @param AttributeDefArray|EventDefArray|ElementDefArray $jsonDef
     *
     * @return CanonicalAttribute|CanonicalEvent|CanonicalElement|null
     */
    abstract function getCanonical(array $jsonDef): CanonicalAttribute|CanonicalEvent|CanonicalElement|null;
}
