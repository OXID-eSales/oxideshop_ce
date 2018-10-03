<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Console;

use Doctrine\Common\Collections\ArrayCollection;
use OxidEsales\EshopCommunity\Internal\Console\CommandsProvider\CommandsProviderInterface;

/**
 * Builds commands collection.
 * @internal
 */
class CommandsCollectionBuilder
{
    /**
     * @var array
     */
    private $commandsProviders;

    /**
     * @param CommandsProviderInterface ...$commandsProviders
     */
    public function __construct(CommandsProviderInterface ...$commandsProviders)
    {
        $this->commandsProviders = $commandsProviders;
    }

    /**
     * @return ArrayCollection
     */
    public function build()
    {
        $collection = new ArrayCollection();
        array_map(function ($commandsProvider) use ($collection) {
            /** @var CommandsProviderInterface $commandsProvider */
            foreach ($commandsProvider->getCommands() as $command) {
                $collection->add($command);
            }
        }, $this->commandsProviders);

        return $collection;
    }
}
