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
            InnerTextNotAllowedException::class => 1000,
            InvalidAreaShapeException::class => 1001,
            InvalidAttributeEventNameException::class => 1002,
            InvalidAttributeValueException::class => 1003,
            InvalidCustomDataNameException::class => 1004,
            InvalidSubTagException::class => 1005,
            InvalidTagException::class => 1006,
            UnsetAttributeNameException::class => 1007,
            UnsetTagNameException::class => 1008,
        ];
    }

    public function getXMessageTemplates(): array
    {
        return [
            InnerTextNotAllowedException::class => 'Inner text or Msg objects not allowed within tag ${tagName}.',
            InvalidAreaShapeException::class => 'A shape attribute with coordinates must be rect, circle or poly',
            InvalidAttributeEventNameException::class => '${name} is not a valid attribute or event name',
            InvalidAttributeValueException::class => 'Error trying to set attribute ${attributeName} to invalid value.',
            InvalidCustomDataNameException::class => 'Invalid custom data name - must be only lower case letters.',
            InvalidSubTagException::class => 'Invalid subtag - either the subtag is invalid or it is a duplicate of a required subtag (see AttributeConfig).',
            InvalidTagException::class => 'Cannot make tag ${tagName}.  There is no entry in AttributeConfig for that tag.',
            UnsetAttributeNameException::class => 'attribute name must be set before rendering.',
            UnsetTagNameException::class => 'tag name must be set before rendering',
        ];
    }
}