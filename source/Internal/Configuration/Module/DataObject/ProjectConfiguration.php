<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\ModuleConfiguration\DataObject;

/**
 * @internal
 */
class ProjectConfiguration
{
    /**
     * @return array
     */
    public function getEnvironmentNames(): array
    {
        return [
            'dev',
            'testing',
            'staging',
            'production'
        ];
    }

    /**
     * @param string $name
     *
     * @return EnvironmentConfiguration
     */
    public function getEnvironmentConfiguration(string $name): EnvironmentConfiguration
    {
        return new EnvironmentConfiguration();
    }
}
