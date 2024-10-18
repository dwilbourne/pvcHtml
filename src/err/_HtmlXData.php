<?php

/**
 * @author: Doug Wilbourne (dougwilbourne@gmail.com)
 * @noinspection PhpCSValidationInspection
 */

declare (strict_types=1);

namespace pvc\html\abstract\err;

use pvc\err\XDataAbstract;

/**
 * Class _HtmlXData
 */
class _HtmlXData extends XDataAbstract
{

    public function getLocalXCodes(): array
    {
        return [
            AttributeNotAllowedException::class => 1008,
            InvalidAttributeNameException::class => 1000,
            InvalidAttributeValueException::class => 1001,
            InvalidCustomDataNameException::class => 1002,
            InvalidInnerTextException::class => 1003,
            InvalidSubTagException::class => 1004,
            UnsetAttributeNameException::class => 1006,
            UnsetTagNameException::class => 1007,
        ];
    }

    public function getXMessageTemplates(): array
    {
        return [
            AttributeNotAllowedException::class => 'Attribute ${attributeName} is not permitted inside a(n) ${tagName} tag.',
            InvalidAttributeNameException::class => 'Invalid attribute or event name',
            InvalidAttributeValueException::class => 'Error trying to set attribute ${attributeName} to invalid value.',
            InvalidCustomDataNameException::class => 'Invalid custom data name - must be only lower case letters.',
            InvalidInnerTextException::class =>'Inner text or Msg objects not allowed within tag ${tagName}.',
            InvalidSubTagException::class => 'Invalid subtag - either the subtag is invalid or it is a duplicate of a required subtag (see HtmlConfig).',
            UnsetAttributeNameException::class => 'attribute name must be set before rendering.',
            UnsetTagNameException::class => 'tag name must be set before rendering',
        ];
    }
}