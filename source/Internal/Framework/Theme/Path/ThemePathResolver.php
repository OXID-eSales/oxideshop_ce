<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Theme\Path;

use OxidEsales\EshopCommunity\Internal\Framework\Theme\Cache\CacheItemNotFoundException;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\Cache\ThemeSourceCacheInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\Configuration\Dao\ThemeConfigurationDaoInterface;
use OxidEsales\EshopCommunity\Internal\Transition\Utility\BasicContextInterface;
use Symfony\Component\Filesystem\Path;

readonly class ThemePathResolver implements ThemePathResolverInterface
{
    public function __construct(
        private ThemeConfigurationDaoInterface $themeConfigurationDao,
        private BasicContextInterface $context,
        private ThemeSourceCacheInterface $themeSourceCache,
    ) {
    }

    public function getAbsolutePath(string $themeId, int $shopId): string
    {
        return Path::join($this->context->getShopRootPath(), $this->getSource($themeId, $shopId));
    }

    private function getSource(string $themeId, int $shopId): string
    {
        try {
            return $this->themeSourceCache->get($themeId, $shopId);
        } catch (CacheItemNotFoundException) {
            $source = $this->themeConfigurationDao->get($themeId, $shopId)->getSource();
            $this->themeSourceCache->put($themeId, $shopId, $source);

            return $source;
        }
    }
}
