<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\ModuleConfiguration\ProjectModuleConfiguration;

/**
 * @internal
 */
interface ConfigurationMapperInterface
{
    /**
     * @param ConfigurationInterface $configuration
     * @param array                  $configurationData
     * @return ConfigurationInterface
     */
    public function getConfiguration(
        ConfigurationInterface  $configuration,
        array                   $configurationData
    ): ConfigurationInterface;

    /**
     * @param ConfigurationInterface $configuration
     * @return array
     */
    public function getConfigurationData(ConfigurationInterface $configuration): array;
}
