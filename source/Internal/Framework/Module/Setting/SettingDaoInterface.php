<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Module\Setting;

interface SettingDaoInterface
{
    public function save(Setting $moduleSetting, string $moduleId, int $shopId): void;

    public function delete(Setting $moduleSetting, string $moduleId, int $shopId): void;

    public function get(string $name, string $moduleId, int $shopId): Setting;
}
