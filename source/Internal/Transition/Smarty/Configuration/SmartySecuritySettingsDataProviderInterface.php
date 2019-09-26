<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Transition\Smarty\Configuration;

/**
 * Interface SmartySecuritySettingsDataProviderInterface
 * @package OxidEsales\EshopCommunity\Internal\Smarty\Configuration
 */
interface SmartySecuritySettingsDataProviderInterface
{
    /**
     * Return smarty security settings.
     *
     * @return array
     */
    public function getSecuritySettings(): array;
}
