<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\ModuleConfiguration\ProjectModuleConfiguration;

/**
 * @internal
 */
class ConfigurationFactory implements ConfigurationFactoryInterface
{
    /**
     * @return ConfigurationInterface
     */
    public function create(): ConfigurationInterface
    {
        return new Configuration();
    }
}
