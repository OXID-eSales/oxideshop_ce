<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Console\CommandsProvider;

use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * @deprecated since v7.1.0
 **/
class ServicesCommandsProvider implements CommandsProviderInterface
{
    /**
     * @var array
     */
    private $commands = [];

    public function __construct(private ContainerInterface $container)
    {
    }

    /**
     * @return array
     */
    public function getCommands(): array
    {
        $this->addLazyLoadedCommands();
        $this->addIdCommands();

        return $this->commands;
    }

    private function addLazyLoadedCommands(): void
    {
        if ($this->container->has('console.command_loader')) {
            $commandLoader = $this->container->get('console.command_loader');
            foreach ($commandLoader->getNames() as $aliases) {
                $this->commands[] =  $commandLoader->get($aliases);
            }
        }
    }

    private function addIdCommands(): void
    {
        if ($this->container->hasParameter('console.command.ids')) {
            foreach ($this->container->getParameter('console.command.ids') as $id) {
                $this->commands[] = $this->container->get($id);
            }
        }
    }
}
