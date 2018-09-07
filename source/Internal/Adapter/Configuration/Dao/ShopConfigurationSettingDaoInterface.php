<?php
declare(strict_types=1);

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Adapter\Configuration\Dao;

use OxidEsales\EshopCommunity\Internal\Adapter\Configuration\DataObject\ShopConfigurationSetting;

/**
 * @internal
 */
interface ShopConfigurationSettingDaoInterface
{
    /**
     * @param ShopConfigurationSetting $shopConfigurationSetting
     */
    public function save(ShopConfigurationSetting $shopConfigurationSetting);

    /**
     * @param string $name
     * @param int    $shopId
     * @return ShopConfigurationSetting
     */
    public function get(string $name, int $shopId): ShopConfigurationSetting;
}
