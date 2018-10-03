<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Console\CommandsProvider;

use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides commands as services.
 * @internal
 */
class ServicesCommandsProvider implements CommandsProviderInterface
{
    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * @return array
     */
    public function getCommands(): array
    {
        $commands = [];
        if ($this->container->hasParameter('console.command.ids')) {
            foreach ($this->container->getParameter('console.command.ids') as $id) {
                $commands[] = $this->container->get($id);
            }
        }
        return $commands;
    }
}
