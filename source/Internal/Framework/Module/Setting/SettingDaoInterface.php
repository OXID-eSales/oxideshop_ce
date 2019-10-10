<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Framework\Module\Setting;

interface SettingDaoInterface
{
    /**
     * @param Setting $moduleSetting
     * @param string $moduleId
     * @param int $shopId
     */
    public function save(Setting $moduleSetting, string $moduleId, int $shopId): void;

    /**
     * @param Setting $moduleSetting
     * @param string $moduleId
     * @param int $shopId
     */
    public function delete(Setting $moduleSetting, string $moduleId, int $shopId): void;

    /**
     * @param string $name
     * @param string $moduleId
     * @param int    $shopId
     *
     * @return Setting
     */
    public function get(string $name, string $moduleId, int $shopId): Setting;
}
