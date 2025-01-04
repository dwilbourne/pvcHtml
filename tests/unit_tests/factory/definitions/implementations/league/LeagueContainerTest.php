<?php

/**
 * @author: Doug Wilbourne (dougwilbourne@gmail.com)
 */
declare (strict_types=1);

namespace pvcTests\html\unit_tests\factory\definitions\implementations\league;

use League\Container\Container;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use pvc\html\factory\definitions\implementations\league\HtmlContainer;

class LeagueContainerTest extends TestCase
{
    protected Container|MockObject $leagueContainer;

    protected HtmlContainer $container;

    public function setUp(): void
    {
        $this->leagueContainer = $this->createMock(Container::class);
        $this->container = new HtmlContainer($this->leagueContainer);

    }

    /**
     * testConstruct
     * @covers \pvc\html\factory\definitions\implementations\league\LeagueContainer::__construct
     */
    public function testConstruct(): void
    {
        self::assertInstanceOf(HtmlContainer::class, $this->container);
    }

    /**
     * testHas
     * @covers \pvc\html\factory\definitions\implementations\league\LeagueContainer::has
     */
    public function testHas(): void
    {
        $testDefId = 'foo';
        $returnValue = false;
        $this->leagueContainer
            ->expects($this->once())
            ->method('has')
            ->with($testDefId)
            ->willReturn($returnValue);
        self::assertFalse($this->container->has($testDefId));
    }

    /**
     * testGet
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @covers \pvc\html\factory\definitions\implementations\league\LeagueContainer::get
     */
    public function testGet(): void
    {
        $testDefId = 'foo';
        $returnValue = 'some kind of definition';
        $this->leagueContainer
            ->expects($this->once())
            ->method('get')
            ->with($testDefId)
            ->willReturn($returnValue);

        self::assertEquals($returnValue, $this->container->get($testDefId));
    }

    /**
     * testAdd
     * @covers \pvc\html\factory\definitions\implementations\league\LeagueContainer::add
     */
    public function testAddA(): void
    {
        $testDefId = 'foo';
        $this->leagueContainer
            ->expects($this->once())
            ->method('add')
            ->with($testDefId);

        $this->container->add($testDefId);
    }
}
