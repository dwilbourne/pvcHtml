<?php

/**
 * @author: Doug Wilbourne (dougwilbourne@gmail.com)
 */
declare (strict_types=1);

namespace pvcTests\html\unit_tests\builder;

use PHPUnit\Framework\TestCase;
use pvc\html\builder\HtmlFactory;
use pvc\html\err\InvalidDefinitionsFileException;
use pvc\interfaces\html\factory\definitions\AbstractDefinitionFactoryInterface;
use pvc\interfaces\html\factory\HtmlContainerInterface;

/**
 * Class HtmlBuilderUopz
 * @runTestsInSeparateProcesses
 */
class HtmlBuilderUopz extends TestCase
{
    protected string $goodDefs = __DIR__ . '/definitions/json/GoodDefinitions.json';

    public function testGetDefinitionsArrayThrowsExceptionWhenFileGetContentsReturnsFalse(): void
    {
        $container = $this->createMock(HtmlContainerInterface::class);
        $definitionFactory = $this->createMock(AbstractDefinitionFactoryInterface::class);

        uopz_set_return('file_get_contents', false);
        self::expectException(InvalidDefinitionsFileException::class);
        $htmlFactory = new HtmlFactory($container, $definitionFactory, $this->goodDefs);
        uopz_unset_return('file_get_contents');
    }

}
