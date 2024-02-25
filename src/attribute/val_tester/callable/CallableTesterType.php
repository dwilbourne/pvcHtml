<?php

/**
 * @author: Doug Wilbourne (dougwilbourne@gmail.com)
 */
declare(strict_types=1);

namespace pvc\html\attribute\val_tester\callable;

use PhpExtended\MimeType\MimeTypeParser;
use PhpExtended\Parser\ParseException;

/**
 * Class CallableTesterType
 */
class CallableTesterType
{
    public function __invoke(string $mimeType): bool
    {
        $parser = new MimeTypeParser();
        try {
            $parser->parse($mimeType);
            return true;
        } catch (ParseException $e) {
            return false;
        }
    }
}