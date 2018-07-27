<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\ModuleConfiguration\ProjectModuleConfiguration;

/**
 * @internal
 */
interface RepositoryInterface
{
    /**
     * @return ConfigurationInterface
     */
    public function getConfiguration(): ConfigurationInterface;

    /**
     * @param ConfigurationInterface $configuration
     */
    public function saveConfiguration(ConfigurationInterface $configuration);
}
