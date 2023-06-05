<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Console\CommandsProvider;

/**
 * @deprecated since v7.1.0
 **/
interface CommandsProviderInterface
{
    /**
     * @return array
     */
    public function getCommands(): array;
}
