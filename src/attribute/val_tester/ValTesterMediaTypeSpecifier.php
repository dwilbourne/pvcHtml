<?php

/**
 * @author: Doug Wilbourne (dougwilbourne@gmail.com)
 */
declare(strict_types=1);

namespace pvc\html\attribute\val_tester;

use pvc\interfaces\http\mimetype\MimeTypesInterface;
use pvc\interfaces\validator\ValTesterInterface;

/**
 * class ValTesterMediaTypeSpecifier
 * @implements ValTesterInterface<string>
 *  documentation from https://developer.mozilla.org/en-US/docs/Web/HTML/Attributes/accept#unique_file_type_specifiers
 *
 *  A valid case-insensitive filename extension, starting with a period (".") character. For example: .jpg, .pdf, or .doc.
 *  A valid MIME type string, with no extensions.
 *  The string audio/* meaning "any audio file".
 *  The string video/* meaning "any video file".
 *  The string image/* meaning "any image file".
 */
class ValTesterMediaTypeSpecifier implements ValTesterInterface
{
    protected MimeTypesInterface $mimeTypes;

    public function __construct(MimeTypesInterface $mimeTypes)
    {
        $this->mimeTypes = $mimeTypes;
    }

    /**
     * { @inheritDoc }
     */
    public function testValue(mixed $value): bool
    {
        $generalized = ['audio/*', 'video/*', 'image/*'];
        if (in_array($value, $generalized)) {
            return true;
        }

        if ($this->mimeTypes->isValidMimeTypeName($value) || $this->mimeTypes->isValidMimeTypeFileExtension($value)) {
            return true;
        }

        return false;
    }
}