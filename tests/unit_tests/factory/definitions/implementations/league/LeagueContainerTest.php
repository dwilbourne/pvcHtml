<?php

/**
 * @author: Doug Wilbourne (dougwilbourne@gmail.com)
 */
declare (strict_types=1);

namespace pvcTests\html\unit_tests\factory\definitions\implementations\league;

use League\Container\Container;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use pvc\html\factory\definitions\implementations\league\LeagueContainer;

class LeagueContainerTest extends TestCase
{
    protected Container|MockObject $container;

    protected LeagueContainer $leagueContainer;

    public function setUp(): void
    {
        $this->container = $this->createMock(Container::class);
        $this->leagueContainer = new LeagueContainer($this->container);
    }

    /**
     * testConstruct
     * @covers \pvc\html\factory\definitions\implementations\league\LeagueContainer::__construct
     */
    public function testConstruct(): void
    {
        self::assertInstanceOf(LeagueContainer::class, $this->leagueContainer);
    }

    /**
     * testHas
     * @covers \pvc\html\factory\definitions\implementations\league\LeagueContainer::has
     */
    public function testHas(): void
    {
        $testDefId = 'foo';
        $returnValue = false;
        $this->container
            ->expects($this->once())
            ->method('has')
            ->with($testDefId)
            ->willReturn($returnValue);
        self::assertFalse($this->leagueContainer->has($testDefId));
    }

    /**
     * testGet
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     * @covers \pvc\html\factory\definitions\implementations\league\LeagueContainer::get
     */
    public function testGet(): void
    {
        $testDefId = 'foo';
        $returnValue = 'some kind of definition';
        $this->container
            ->expects($this->once())
            ->method('get')
            ->with($testDefId)
            ->willReturn($returnValue);

        self::assertEquals($returnValue, $this->leagueContainer->get($testDefId));
    }

    /**
     * testAdd
     * @covers \pvc\html\factory\definitions\implementations\league\LeagueContainer::add
     */
    public function testAddA(): void
    {
        $testDefId = 'foo';
        $this->container
            ->expects($this->once())
            ->method('add')
            ->with($testDefId);

        $this->leagueContainer->add($testDefId);
    }
}
