<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Config\Dao;

use OxidEsales\EshopCommunity\Internal\Framework\Config\DataObject\ThemeConfigurationSetting;
use OxidEsales\EshopCommunity\Internal\Framework\Dao\EntryDoesNotExistDaoException;

interface ThemeConfigurationSettingDaoInterface
{
    public function save(ThemeConfigurationSetting $themeConfigurationSetting): void;

    /**
     * @throws EntryDoesNotExistDaoException
     */
    public function get(string $name, int $shopId, string $themeId): ThemeConfigurationSetting;

    public function delete(ThemeConfigurationSetting $themeConfigurationSetting): void;
}
