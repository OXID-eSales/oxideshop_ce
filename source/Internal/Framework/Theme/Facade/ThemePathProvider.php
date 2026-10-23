<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Theme\Facade;

use OxidEsales\EshopCommunity\Internal\Framework\Theme\Configuration\Dao\ThemeConfigurationDaoInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\Configuration\DataObject\ThemeConfiguration;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\Path\Service\ThemePathResolverInterface;
use Symfony\Contracts\Cache\TagAwareCacheInterface;

readonly class ThemePathProvider implements ThemePathProviderInterface
{
    public function __construct(
        private ThemePathResolverInterface $themePathResolver,
        private ThemeConfigurationDaoInterface $themeConfigurationDao,
        private TagAwareCacheInterface $cache,
        private int $shopId,
    ) {
    }

    public function getForTheme(string $themeId): string
    {
        return $this->cache->get(
            $this->getCacheKey($themeId),
            fn (): string => $this->themePathResolver->getAbsolutePath($themeId, $this->shopId)
        );
    }

    /** @return string[] */
    public function getForInstalledThemes(): array
    {
        return array_values(array_map(
            fn (ThemeConfiguration $configuration): string => $this->getForTheme($configuration->getId()),
            $this->themeConfigurationDao->getAll($this->shopId)
        ));
    }

    private function getCacheKey(string $themeId): string
    {
        return 'theme_path_' . $themeId;
    }
}
