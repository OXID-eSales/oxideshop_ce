<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Theme\Twig;

use OxidEsales\EshopCommunity\Internal\Framework\Theme\Facade\ActiveThemeServiceInterface;

readonly class ThemeViewProxy
{
    public function __construct(
        private ActiveThemeServiceInterface $activeThemeService
    ) {
    }

    public function activeId(): string
    {
        return $this->activeThemeService->getActiveThemeId();
    }

    public function setting(string $name): mixed
    {
        return $this->activeThemeService->getSettingValue($name);
    }

    public function has(string $name): bool
    {
        return $this->activeThemeService->hasSetting($name);
    }

    /**
     * @return string[]
     */
    public function chain(): array
    {
        return $this->activeThemeService->getActiveThemeChain();
    }
}
