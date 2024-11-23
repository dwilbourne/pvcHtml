<?php

/**
 * @author: Doug Wilbourne (dougwilbourne@gmail.com)
 * @noinspection PhpCSValidationInspection
 */

declare (strict_types=1);

namespace pvc\html\err;

use pvc\err\XDataAbstract;

/**
 * Class _HtmlXData
 */
class _HtmlXData extends XDataAbstract
{

    public function getLocalXCodes(): array
    {
        return [
            AttributeNotAllowedException::class => 1002,
            InvalidDefinitionsFileException::class => 1003,
            InvalidAttributeIdNameException::class => 1004,
            InvalidAttributeValueException::class => 1005,
            InvalidCustomDataNameException::class => 1006,
            InvalidEventNameException::class => 1007,
            InvalidNumberOfParametersException::class => 1011,
            ChildElementNotAllowedException::class => 1012,
            InvalidTagNameException::class => 1013,
            MakeDefinitionException::class => 1016,
            DTOInvalidPropertyValueException::class => 1017,
            InvalidAttributeValueTesterNameException::class => 1018,
            DuplicateDefinitionIdException::class => 1019,
            InvalidDefinitionIdException::class => 1020,
            DTOMissingPropertyException::class => 1021,
            DTOExtraPropertyException::class => 1025,
            InvalidAttributeException::class => 1026,
        ];
    }

    public function getXMessageTemplates(): array
    {
        return [
            AttributeNotAllowedException::class => 'Attribute ${attributeDefId} is not permitted inside a(n) ${tagDefId} tag.',
            InvalidDefinitionsFileException::class => '${filePath} either does not exist or contains invalid definitions',
            InvalidAttributeIdNameException::class => 'Attribute id ${badName} is not a valid identifier for an attribute id.',
            InvalidAttributeValueException::class => 'Error trying to set attribute ${attributeName} to invalid value [${badValue}].',
            InvalidAttributeValueTesterNameException::class => '${attributeValueTesterDefId} is not a valid value tester definition id',
            InvalidCustomDataNameException::class => 'Invalid custom data name [${badName}] - must be only lower case letters.',
            DTOInvalidPropertyValueException::class => 'DTO ${className} error - cannot assign value ${value} to property ${propertyName}',
            InvalidEventNameException::class => '${eventName} is not a valid event id.',
            InvalidNumberOfParametersException::class => 'Invalid number of parameters: expected ${expectedNumberOfParameters}',
            ChildElementNotAllowedException::class => 'Invalid subtag [${badDefId}] - either the subtag is invalid or it is a duplicate of a required subtag (see HtmlConfig).',
            InvalidTagNameException::class => '${tagDefId} is not a valid tag id.',
            MakeDefinitionException::class => 'Unable to make definition of type ${type}.',
            DuplicateDefinitionIdException::class => 'Definition id ${defId} already exists in the container.',
            InvalidDefinitionIdException::class => 'No definition exists for defId ${defId}.',
            DTOMissingPropertyException::class => 'DTO ${className} constructor is missing the following properties: [${missingPropertyNames}].',
            DTOExtraPropertyException::class => 'DTO ${className} constructor was passed an extra property [${extraPropertyName}]',
            InvalidAttributeException::class => '${badAttributeName} is not a valid attribute.',
        ];
    }
}