<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Theme\Facade;

use OxidEsales\EshopCommunity\Internal\Framework\Theme\Configuration\Dao\ThemeConfigurationDaoInterface;

readonly class InstalledThemeProvider implements InstalledThemeProviderInterface
{
    public function __construct(
        private ThemeConfigurationDaoInterface $themeConfigurationDao,
        private int $shopId,
    ) {
    }

    /** @return string[] */
    public function getThemeIds(): array
    {
        $themeIds = [];
        foreach (array_keys($this->themeConfigurationDao->getAll($this->shopId)) as $themeId) {
            $themeIds[] = (string) $themeId;
        }

        return $themeIds;
    }
}
