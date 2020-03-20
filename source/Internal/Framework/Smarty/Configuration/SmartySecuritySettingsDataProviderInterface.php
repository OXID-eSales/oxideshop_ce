<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Smarty\Configuration;

interface SmartySecuritySettingsDataProviderInterface
{
    /**
     * Return smarty security settings.
     *
     * @return array
     */
    public function getSecuritySettings(): array;
}
