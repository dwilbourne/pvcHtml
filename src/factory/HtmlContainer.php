<?php

/**
 * @author: Doug Wilbourne (dougwilbourne@gmail.com)
 */

declare(strict_types=1);

namespace pvc\html\factory;

use League\Container\Container;
use League\Container\Definition\Definition;
use League\Container\Definition\DefinitionAggregate;
use League\Container\ReflectionContainer;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;

/**
 * Class HtmlContainer
 */
class HtmlContainer implements ContainerInterface
{
    /**
     * @var Container
     */
    protected Container $leagueContainer;

    protected string $definitionsFile = __DIR__ . '/' . 'LeagueDefinitions.php';

    public function __construct(?string $leagueDefinitionFile = null)
    {
        /** @var array<Definition> $defs */
        $defs = include($leagueDefinitionFile ?? $this->definitionsFile);
        $aggregate = new DefinitionAggregate($defs);
        $this->leagueContainer = new Container($aggregate);

        /**
         * add the html factory definition
         */
        $this->leagueContainer->add(HtmlFactory::class);

        /**
         * enable autowiring
         */
        $this->leagueContainer->delegate(new ReflectionContainer());

    }

    /**
     * has
     * @param string $id
     * @return bool
     */
    public function has(string $id): bool
    {
        return $this->leagueContainer->has($id);
    }

    /**
     * get
     * @param string $id
     * @return mixed
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function get(string $id): mixed
    {
        return $this->leagueContainer->get($id);
    }
}
