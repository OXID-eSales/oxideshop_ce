<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Framework\Theme\Setup\Service;

interface ThemeActivationServiceInterface
{
    public function activate(string $themeId, int $shopId): void;

    public function deactivate(string $themeId, int $shopId): void;
}
