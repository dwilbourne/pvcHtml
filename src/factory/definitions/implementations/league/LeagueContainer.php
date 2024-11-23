<?php

/**
 * @author: Doug Wilbourne (dougwilbourne@gmail.com)
 */

declare(strict_types=1);

namespace pvc\html\factory\definitions\implementations\league;

use League\Container\Container;
use League\Container\Definition\DefinitionInterface;
use League\Container\ReflectionContainer;
use pvc\interfaces\html\factory\definitions\AbstractDefinitionFactoryInterface;
use pvc\interfaces\html\factory\HtmlContainerInterface;

/**
 * Class HtmlLeagueContainer
 * @template VendorSpecificDefinition of AbstractDefinitionFactoryInterface
 * @implements HtmlContainerInterface<VendorSpecificDefinition<DefinitionInterface>>
 */
class LeagueContainer implements HtmlContainerInterface
{
    /**
     * @var Container
     */
    protected Container $container;

    /**
     * @param Container $container
     */
    public function __construct(Container $container)
    {
        $this->container = $container;

        /**
         * enable autowiring
         */
        $this->container->delegate(new ReflectionContainer());
    }

    /**
     * has
     * @param string $id
     * @return bool
     */
    public function has(string $id): bool
    {
        return $this->container->has($id);
    }

    /**
     * get
     * @param string $id
     * @return mixed
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function get(string $id): mixed
    {
        return $this->container->get($id);
    }

    /**
     * add
     * @param string $defId
     * @param DefinitionInterface|null $definition
     */
    public function add(string $defId, mixed $definition = null): void
    {
        $this->container->add($defId, $definition);
    }
}
