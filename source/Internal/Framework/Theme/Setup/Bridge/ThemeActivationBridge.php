<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Theme\Setup\Bridge;

use OxidEsales\EshopCommunity\Internal\Framework\Theme\Setup\Service\ThemeActivationServiceInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\State\ThemeStateServiceInterface;

readonly class ThemeActivationBridge implements ThemeActivationBridgeInterface
{
    public function __construct(
        private ThemeActivationServiceInterface $themeActivationService,
        private ThemeStateServiceInterface $themeStateService
    ) {
    }

    public function activate(string $themeId, int $shopId): void
    {
        $this->themeActivationService->activate($themeId, $shopId);
    }

    public function deactivate(string $themeId, int $shopId): void
    {
        $this->themeActivationService->deactivate($themeId, $shopId);
    }

    public function isActive(string $themeId, int $shopId): bool
    {
        return $this->themeStateService->isActive($themeId, $shopId);
    }
}
