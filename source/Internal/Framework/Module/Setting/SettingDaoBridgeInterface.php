<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Module\Setting;

interface SettingDaoBridgeInterface
{
    public function save(Setting $moduleSetting, string $moduleId): void;
    public function get(string $name, string $moduleId): Setting;
}
