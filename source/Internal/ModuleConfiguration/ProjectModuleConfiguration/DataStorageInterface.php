<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\ModuleConfiguration\ProjectModuleConfiguration;

/**
 * @internal
 */
interface DataStorageInterface
{
    /**
     * @return array
     */
    public function get(): array;

    /**
     * @param array $configuration
     */
    public function save(array $configuration);
}
