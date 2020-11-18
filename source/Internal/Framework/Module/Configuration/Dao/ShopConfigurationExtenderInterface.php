<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\Dao;

interface ShopConfigurationExtenderInterface
{
    /**
     * @param int $shopId
     * @param array $shopConfiguration
     * @return array
     */
    public function getExtendedConfiguration(int $shopId, array $shopConfiguration): array;
}
