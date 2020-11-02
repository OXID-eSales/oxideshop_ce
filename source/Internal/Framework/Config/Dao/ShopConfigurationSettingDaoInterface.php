<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Config\Dao;

use OxidEsales\EshopCommunity\Internal\Framework\Config\DataObject\ShopConfigurationSetting;
use OxidEsales\EshopCommunity\Internal\Framework\Dao\EntryDoesNotExistDaoException;

interface ShopConfigurationSettingDaoInterface
{
    public function save(ShopConfigurationSetting $shopConfigurationSetting);

    /**
     * @throws EntryDoesNotExistDaoException
     */
    public function get(string $name, int $shopId): ShopConfigurationSetting;

    public function delete(ShopConfigurationSetting $setting);
}
