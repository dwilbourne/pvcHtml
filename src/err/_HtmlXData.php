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
            InvalidAttributeNameException::class => 1000,
            InvalidAttributeValueException::class => 1001,
            InvalidAttributeException::class => 1002,
            InvalidCustomDataNameException::class => 1003,
            InvalidEventNameException::class => 1004,
            InvalidTagException::class => 1005,
        ];
    }

    public function getXMessageTemplates(): array
    {
        return [
            InvalidAttributeNameException::class => 'Invalid attribute name ${attributeName}',
            InvalidAttributeValueException::class => 'Error trying to set attribute ${attributeName} to invalid value ${attributeValue}.',
            InvalidAttributeException::class => 'No such attribute in this tag: ${attrName}',
            InvalidCustomDataNameException::class => 'Invalid custom data name - must be only lower case letters.',
            InvalidEventNameException::class => 'Event name cannot be empty or a string which is not a valid event name.',
            InvalidTagException::class => 'Cannot make tag ${tagName}.  Either the name is invalid or there is no entry in TagConfig for that tag.',
        ];
    }
}