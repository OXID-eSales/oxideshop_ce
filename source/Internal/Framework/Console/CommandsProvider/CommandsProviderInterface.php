<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Framework\Console\CommandsProvider;

/**
 * Provides commands classes.
 * @internal
 */
interface CommandsProviderInterface
{
    /**
     * @return array
     */
    public function getCommands(): array;
}
