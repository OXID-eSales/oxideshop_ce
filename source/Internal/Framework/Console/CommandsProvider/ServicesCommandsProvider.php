<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Framework\Console\CommandsProvider;

use OxidEsales\EshopCommunity\Internal\Framework\Console\AbstractShopAwareCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\DependencyInjection\ContainerInterface;

class ServicesCommandsProvider implements CommandsProviderInterface
{
    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * @var array
     */
    private $commands = [];

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
        if ($this->container->hasParameter('console.command.ids')) {
            foreach ($this->container->getParameter('console.command.ids') as $id) {
                $service = $this->container->get($id);
                $this->setShopAwareCommands($service);
                $this->setNonShopAwareCommands($service);
            }
        }
        return $this->commands;
    }

    /**
     * Set commands for modules.
     *
     * @param Command $service
     */
    private function setShopAwareCommands(Command $service)
    {
        if ($service instanceof AbstractShopAwareCommand && $service->isActive()) {
            $this->commands[] = $service;
        }
    }

    /**
     * Sets commands which should be shown independently from active shop.
     *
     * @param Command $service
     */
    private function setNonShopAwareCommands(Command $service)
    {
        if (!$service instanceof AbstractShopAwareCommand && $service instanceof Command) {
            $this->commands[] = $service;
        }
    }
}
