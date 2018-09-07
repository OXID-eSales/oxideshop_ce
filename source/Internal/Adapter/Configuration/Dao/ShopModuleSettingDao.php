<?php
declare(strict_types=1);

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Adapter\Configuration\Dao;

use OxidEsales\EshopCommunity\Internal\Adapter\Configuration\DataObject\ShopModuleSetting;

/**
 * @internal
 */
class ShopModuleSettingDao implements ShopModuleSettingDaoInterface
{
    /**
     * @param ShopModuleSetting $shopModuleSetting
     */
    public function save(ShopModuleSetting $shopModuleSetting)
    {
        // TODO: Implement save() method.
    }

    /**
     * @param string $name
     * @param string $moduleId
     * @param int    $shopId
     *
     * @return ShopModuleSetting
     */
    public function get(string $name, string $moduleId, int $shopId): ShopModuleSetting
    {
        // TODO: Implement get() method.
    }
}
