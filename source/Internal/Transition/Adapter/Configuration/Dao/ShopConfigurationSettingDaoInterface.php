<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Transition\Adapter\Configuration\Dao;

use OxidEsales\EshopCommunity\Internal\Transition\Adapter\Configuration\DataObject\ShopConfigurationSetting;
use OxidEsales\EshopCommunity\Internal\Framework\Dao\EntryDoesNotExistDaoException;

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
     *
     * @throws EntryDoesNotExistDaoException
     */
    public function get(string $name, int $shopId): ShopConfigurationSetting;

    /**
     * @param ShopConfigurationSetting $setting
     */
    public function delete(ShopConfigurationSetting $setting);
}
