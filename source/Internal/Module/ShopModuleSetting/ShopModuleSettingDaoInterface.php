<?php
declare(strict_types=1);

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Module\ShopModuleSetting;

use OxidEsales\EshopCommunity\Internal\Module\ShopModuleSetting\ShopModuleSetting;

/**
 * @internal
 */
interface ShopModuleSettingDaoInterface
{
    /**
     * @param ShopModuleSetting $shopModuleSetting
     */
    public function save(ShopModuleSetting $shopModuleSetting);

    /**
     * @param string $name
     * @param string $moduleId
     * @param int    $shopId
     *
     * @return ShopModuleSetting
     */
    public function get(string $name, string $moduleId, int $shopId): ShopModuleSetting;
}
