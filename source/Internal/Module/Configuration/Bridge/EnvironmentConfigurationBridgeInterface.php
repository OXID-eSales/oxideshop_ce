<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Module\Configuration\Bridge;

use OxidEsales\EshopCommunity\Internal\Module\Configuration\DataObject\EnvironmentConfiguration;

/**
 * @internal
 */
interface EnvironmentConfigurationBridgeInterface
{
    /**
     * @return EnvironmentConfiguration
     */
    public function get(): EnvironmentConfiguration;

    /**
     * @param EnvironmentConfiguration $environmentConfiguration
     */
    public function save(EnvironmentConfiguration $environmentConfiguration);
}
