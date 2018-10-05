<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Module\Configuration\Provider;

use OxidEsales\EshopCommunity\Internal\Module\Configuration\DataObject\ModuleConfiguration;

/**
 * @internal
 */
interface ModuleConfigurationProviderInterface
{
    /**
     * @param string $moduleId
     * @param string $environment
     * @param int    $shopId
     * @return ModuleConfiguration
     */
    public function getModuleConfiguration(string $moduleId, string $environment, int $shopId): ModuleConfiguration;
}
