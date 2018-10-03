<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Console\CommandsProvider;

use OxidEsales\Eshop\Core\Module\Module;
use OxidEsales\EshopCommunity\Internal\Adapter\ShopAdapterInterface;

/**
 * Provides modules commands.
 * @internal
 */
class ModuleCommandsProvider implements CommandsProviderInterface
{
    /**
     * @var ShopAdapterInterface
     */
    private $shopAdapter;

    /**
     * @param ShopAdapterInterface $adapter
     */
    public function __construct(ShopAdapterInterface $adapter)
    {
        $this->shopAdapter = $adapter;
    }

    /**
     * @return array
     */
    public function getCommands(): array
    {
        $commands = [];
        /** @var Module $module */
        foreach ($this->shopAdapter->getModules() as $module) {
            if ($module->isActive() && is_array($module->getInfo('commands'))) {
                foreach ($module->getInfo('commands') as $commandClass) {
                    $commands[] = new $commandClass;
                }
            }
        }

        return $commands;
    }
}
