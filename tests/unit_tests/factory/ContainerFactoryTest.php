<?php

/**
 * @author: Doug Wilbourne (dougwilbourne@gmail.com)
 */
declare (strict_types=1);

namespace pvcTests\html\unit_tests\factory;

use League\Container\Definition\Definition;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use pvc\html\err\DefinitionsFileException;
use pvc\html\factory\ContainerFactory;

/**
 * Class ContainerFactoryTest
 * @runTestsInSeparateProcesses
 */
class ContainerFactoryTest extends TestCase
{
    protected ContainerFactory $containerFactory;

    protected string $fixturesDir = __DIR__ . '/fixture/';

    public function setUp(): void
    {
        $this->containerFactory = new ContainerFactory($this->fixturesDir);
    }

    /**
     * testConstruct
     * @covers \pvc\html\factory\ContainerFactory::__construct
     */
    public function testConstruct(): void
    {
        self::assertInstanceOf(ContainerFactory::class,$this->containerFactory);
    }

    /**
     * testMakeElementDefinition
     * @covers \pvc\html\factory\ContainerFactory::makeElementDefinition
     */
    public function testMakeElementDefinition(): void
    {
        $name = 'ul';
        $tagType = 'Tag';
        $allowedAttributes = ['attr1', 'attr2'];
        $allowedSubtags = ['tag1', 'tag2'];

        $def = $this->containerFactory->makeElementDefinition(
            $name,
            $tagType,
            $allowedAttributes,
            $allowedSubtags);

        self::assertInstanceOf(Definition::class, $def);
    }

    /**
     * testMakeAttributeDefinition
     * @covers \pvc\html\factory\ContainerFactory::makeAttributeDefinition
     */
    public function testMakeAttributeDefinition(): void
    {
        $id = 'target';
        $name = 'target';
        $type = 'AttributeSingleValue';
        $tester = 'targetTester';
        $global = false;

        $def = $this->containerFactory->makeAttributeDefinition($id, $name, $type, $tester, $global);
        self::assertInstanceOf(Definition::class, $def);
    }

    /**
     * testMakeAttributeTesterDefinition
     * @covers \pvc\html\factory\ContainerFactory::makeAttributeValueTesterDefinition
     */
    public function testMakeAttributeTesterDefinition(): void
    {
        $name = 'urlTester';
        $testerType = "pvc\\validator\\val_tester\\filter_var\FilterVarTester";
        $testerArg = "pvc\\filtervar\\FilterVarValidateUrl";

        $expected = "";
        $expected .= "(new League\\Container\\Definition\Definition('urlTester', 'pvc\\validator\\val_tester\\filter_var\\FilterVarTester'))";
        $expected .= "->addArgument('pvc\\filtervar\\FilterVarValidateUrl')";

        $def = $this->containerFactory->makeAttributeValueTesterDefinition($name, $testerType, $testerArg);
        self::assertInstanceOf(Definition::class, $def);
    }

    /**
     * testMakeEventDefinition
     * @covers \pvc\html\factory\ContainerFactory::makeEventDefinition
     */
    public function testMakeEventDefinition(): void
    {
        $name = 'onchange';
        $def = $this->containerFactory->makeEventDefinition($name);
        self::assertInstanceOf(Definition::class, $def);
    }

    /**
     * testGetElementDefinitionArrayThrowsExceptionWithBadFileName
     * @throws DefinitionsFileException
     * @covers \pvc\html\factory\ContainerFactory::getDefinitionArray
     */
    public function testGetElementDefinitionArrayThrowsExceptionWithBadFileName(): void
    {
        $containerFactory = new ContainerFactory('foobar');
        self::expectException(DefinitionsFileException::class);
        $containerFactory->makeElementContainer();
    }

    /**
     * testGetElementDefinitionArrayThrowsExceptionWhenFileHasInvalidJson
     * @throws DefinitionsFileException
     * @covers \pvc\html\factory\ContainerFactory::getDefinitionArray
     */
    public function testGetElementDefinitionArrayThrowsExceptionWhenFileHasInvalidJson(): void
    {
        uopz_set_return("json_decode", null);
        self::expectException(DefinitionsFileException::class);
        $this->containerFactory->makeElementContainer();
        uopz_unset_return("json_decode");
    }

    /**
     * testMakeElementContainer
     * @covers \pvc\html\factory\ContainerFactory::makeElementContainer
     * @covers \pvc\html\factory\ContainerFactory::getDefinitionArray
     * @covers \pvc\html\factory\ContainerFactory::getElementNames
     */
    public function testMakeElementContainer(): void
    {
        self::assertInstanceOf(ContainerInterface::class, $this->containerFactory->makeElementContainer());
        self::assertEquals(2, count($this->containerFactory->getElementNames()));
    }

    /**
     * testMakeAttributeContainer
     * @covers \pvc\html\factory\ContainerFactory::makeAttributeContainer
     * @covers \pvc\html\factory\ContainerFactory::getAttributeNames
     * @covers \pvc\html\factory\ContainerFactory::getAttributeValueTesterNames
     */
    public function testMakeAttributeContainer(): void
    {
        self::assertInstanceOf(ContainerInterface::class, $this->containerFactory->makeAttributeContainer());
        self::assertEquals(2, count($this->containerFactory->getAttributeNames()));
        self::assertEquals(2, count($this->containerFactory->getAttributeValueTesterNames()));
    }

    /**
     * testMakeEventContainer
     * @covers \pvc\html\factory\ContainerFactory::makeEventContainer
     * @covers \pvc\html\factory\ContainerFactory::getEventNames
     */
    public function testMakeEventContainer(): void
    {
        self::assertInstanceOf(ContainerInterface::class, $this->containerFactory->makeEventContainer());
        self::assertEquals(2, count($this->containerFactory->getEventNames()));
    }
}
