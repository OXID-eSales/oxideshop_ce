<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Theme\Facade;

use OxidEsales\EshopCommunity\Internal\Framework\Theme\Activation\DataObject\ActiveTheme;
use Symfony\Contracts\Cache\TagAwareCacheInterface;

class CachedActiveThemeProvider implements ActiveThemeProviderInterface
{
    private ActiveTheme $activeTheme;

    public function __construct(
        private readonly ActiveThemeProviderInterface $activeThemeProvider,
        private readonly TagAwareCacheInterface $cache,
        private readonly string $activeThemeId,
    ) {
    }

    public function getActiveTheme(): ActiveTheme
    {
        return $this->activeTheme ??= $this->cache->get(
            $this->getCacheKey(),
            fn (): ActiveTheme => $this->activeThemeProvider->getActiveTheme()
        );
    }

    private function getCacheKey(): string
    {
        return 'active_theme_' . $this->activeThemeId;
    }
}
