<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Theme\Facade;

use OxidEsales\EshopCommunity\Internal\Framework\Theme\Path\Service\ThemePathResolverInterface;
use Symfony\Contracts\Cache\TagAwareCacheInterface;

readonly class ThemePathProvider implements ThemePathProviderInterface
{
    public function __construct(
        private ThemePathResolverInterface $themePathResolver,
        private TagAwareCacheInterface $cache,
        private int $shopId,
    ) {
    }

    public function getAbsolutePath(string $themeId): string
    {
        return $this->cache->get(
            $this->getCacheKey($themeId),
            fn (): string => $this->themePathResolver->getAbsolutePath($themeId, $this->shopId)
        );
    }

    private function getCacheKey(string $themeId): string
    {
        return 'theme_path_' . $themeId;
    }
}
