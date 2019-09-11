<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Framework\Module\Setting;

/**
 * @internal
 */
interface SettingDaoInterface
{
    /**
     * @param Setting $shopModuleSetting
     */
    public function save(Setting $shopModuleSetting);

    /**
     * @param Setting $shopModuleSetting
     */
    public function delete(Setting $shopModuleSetting);

    /**
     * @param string $name
     * @param string $moduleId
     * @param int    $shopId
     *
     * @return Setting
     */
    public function get(string $name, string $moduleId, int $shopId): Setting;
}
