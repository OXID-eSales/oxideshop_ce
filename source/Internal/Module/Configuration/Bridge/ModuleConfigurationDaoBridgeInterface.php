<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Module\Configuration\Bridge;

use OxidEsales\EshopCommunity\Internal\Module\Configuration\DataObject\ModuleConfiguration;

/**
 * @internal
 */
interface ModuleConfigurationDaoBridgeInterface
{
    /**
     * @param string $moduleId
     * @return ModuleConfiguration
     */
    public function get(string $moduleId): ModuleConfiguration;

    /**
     * @param ModuleConfiguration $moduleConfiguration
     */
    public function save(ModuleConfiguration $moduleConfiguration);
}
