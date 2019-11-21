<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Smarty\Configuration;

interface SmartySettingsDataProviderInterface
{
    /**
     * Return basic smarty settings
     *
     * @return array
     */
    public function getSettings(): array;
}
