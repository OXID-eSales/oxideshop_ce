<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Core\Di;

use OxidEsales\EshopCommunity\Internal\Container\ContainerFactory;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Contracts\EventDispatcher\Event;

/**
 * A Service Locator fallback to use in the application areas, not managed by the Dependency Injection Component (e.g. Application, Core).
 * Never use this class (or Container directly) in other namespaces (e.g. Internal)!
 */
final class ContainerFacade
{
    public static function has(string $id): bool
    {
        return ContainerFactory::getInstance()
            ->getContainer()
            ->has($id);
    }

    /**
     * @template T
     * @param class-string<T> $id
     * @return T
     */
    public static function get(string $id): object
    {
        return ContainerFactory::getInstance()
            ->getContainer()
            ->get($id);
    }

    public static function hasParameter(string $name): bool
    {
        return ContainerFactory::getInstance()
            ->getContainer()
            ->hasParameter($name);
    }

    public static function getParameter(string $name): mixed
    {
        return ContainerFactory::getInstance()
            ->getContainer()
            ->getParameter($name);
    }

    /**
     * @template T of Event
     * @param T $event
     * @return T
     */
    public static function dispatch(Event $event): Event
    {
        return ContainerFactory::getInstance()
            ->getContainer()
            ->get(EventDispatcherInterface::class)
            ->dispatch($event);
    }
}
