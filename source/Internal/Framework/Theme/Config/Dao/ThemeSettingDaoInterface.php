<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Theme\Config\Dao;

use OxidEsales\EshopCommunity\Internal\Framework\Theme\Config\DataObject\ThemeSetting;
use OxidEsales\EshopCommunity\Internal\Framework\Dao\EntryDoesNotExistDaoException;

interface ThemeSettingDaoInterface
{
    public function save(ThemeSetting $setting): void;

    /**
     * @throws EntryDoesNotExistDaoException
     */
    public function get(string $name, int $shopId, string $themeId): ThemeSetting;

    public function delete(ThemeSetting $setting): void;
}
