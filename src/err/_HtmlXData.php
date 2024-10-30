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
            AmbiguousMethodCallException::class => 1000,
            AttributeNotAllowedException::class => 1001,
            DefinitionsFileException::class => 1002,
            InvalidAttributeIdNameException::class => 1003,
            InvalidAttributeValueException::class => 1004,
            InvalidCustomDataNameException::class => 1005,
            InvalidEventNameException::class => 1006,
            InvalidInnerTextException::class => 1007,
            InvalidMethodCallException::class => 1008,
            InvalidNumberOfParametersException::class => 1009,
            InvalidSubTagException::class => 1010,
            InvalidTagNameException::class => 1011,
            UnsetAttributeNameException::class => 1012,
            UnsetTagNameException::class => 1013,
        ];
    }

    public function getXMessageTemplates(): array
    {
        return [
            AmbiguousMethodCallException::class => 'Called method ${methodName} could be either an attribute or a tag - use addSubTag or setAttribute to differentiate the usage.',
            AttributeNotAllowedException::class => 'Attribute ${attributeName} is not permitted inside a(n) ${tagName} tag.',
            DefinitionsFileException::class => '${filePath} either does not exist or contains invalid definitions',
            InvalidAttributeIdNameException::class => 'Attribute id ${badName} is not a valid identifier for an attribute id.',
            InvalidAttributeValueException::class => 'Error trying to set attribute to invalid value.',
            InvalidCustomDataNameException::class => 'Invalid custom data id - must be only lower case letters.',
            InvalidEventNameException::class => '${eventName} is not a valid event id.',
            InvalidInnerTextException::class =>'Inner text or Msg objects not allowed within tag ${tagName}.',
            InvalidMethodCallException::class => '${methodName} is neither a valid subtag id, attribute id nor method.',
            InvalidNumberOfParametersException::class => 'Invalid number of parameters: expected ${expectedNumberOfParameters}',
            InvalidSubTagException::class => 'Invalid subtag - either the subtag is invalid or it is a duplicate of a required subtag (see HtmlConfig).',
            InvalidTagNameException::class => '${tagName} is not a valid tag id.',
            UnsetAttributeNameException::class => 'attribute id must be set before rendering.',
            UnsetTagNameException::class => 'tag id must be set before rendering',
        ];
    }
}