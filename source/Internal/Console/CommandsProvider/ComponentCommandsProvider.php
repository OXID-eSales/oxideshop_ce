<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Console\CommandsProvider;

use Composer\Repository\WritableRepositoryInterface;

/**
 * Class responsible for providing classes which are later on registered as commands.
 * @internal
 */
class ComponentCommandsProvider implements CommandsProviderInterface
{
    /**
     * @var WritableRepositoryInterface
     */
    private $localRepository;

    /**
     * @param WritableRepositoryInterface $localRepository
     */
    public function __construct(WritableRepositoryInterface $localRepository)
    {
        $this->localRepository = $localRepository;
    }

    /**
     * @return array
     */
    public function getCommands(): array
    {
        $commandsClasses = [];
        $packages = $this->localRepository->getPackages();
        foreach ($packages as $package) {
            if (isset($package->getExtra()['oxideshop'])
                && isset($package->getExtra()['oxideshop']['console-commands'])
                && is_array($package->getExtra()['oxideshop']['console-commands'])
            ) {
                foreach ($package->getExtra()['oxideshop']['console-commands'] as $commandClass) {
                    $commandsClasses[] = new $commandClass;
                }
            }
        }
        return $commandsClasses;
    }
}
