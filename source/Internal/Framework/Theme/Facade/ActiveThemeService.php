<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Theme\Facade;

use OxidEsales\EshopCommunity\Internal\Framework\Theme\Configuration\Dao\ThemeConfigurationDaoInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\State\ThemeStateServiceInterface;
use OxidEsales\EshopCommunity\Internal\Transition\Utility\ContextInterface;
use Psr\Cache\CacheItemPoolInterface;
use Symfony\Component\Filesystem\Path;

readonly class ActiveThemeService implements ActiveThemeServiceInterface
{
    private const CACHE_KEY = 'oxid_esales_theme_active_data';

    public function __construct(
        private ThemeStateServiceInterface $themeStateService,
        private ThemeConfigurationDaoInterface $themeConfigurationDao,
        private ContextInterface $context,
        private CacheItemPoolInterface $cache
    ) {
    }

    public function getActiveThemeId(): string
    {
        return $this->getData()['id'];
    }

    /**
     * @return string[]
     */
    public function getActiveThemeChain(): array
    {
        return $this->getData()['chain'];
    }

    public function getSettings(): array
    {
        return $this->getData()['settings'];
    }

    public function hasSetting(string $name): bool
    {
        return array_key_exists($name, $this->getData()['settings']);
    }

    public function getSettingValue(string $name): mixed
    {
        return $this->getData()['settings'][$name] ?? null;
    }

    /**
     * @return array<string, string>
     */
    public function getActiveThemeSourcePaths(): array
    {
        return $this->getData()['sources'];
    }

    private function getData(): array
    {
        $cacheItem = $this->cache->getItem(self::CACHE_KEY);

        if (!$cacheItem->isHit()) {
            $data = $this->buildData();
            $cacheItem->set($data);
            $this->cache->save($cacheItem);

            return $data;
        }

        return $cacheItem->get();
    }

    private function buildData(): array
    {
        $shopId = $this->context->getCurrentShopId();
        $chain = $this->themeStateService->getActiveThemeChain($shopId);

        $settings = [];
        $sources = [];
        foreach ($chain as $themeId) {
            $configuration = $this->themeConfigurationDao->get($themeId, $shopId);
            foreach ($configuration->getSettings() as $setting) {
                $settings[$setting->getName()] = $setting->getValue();
            }
            $sources[$themeId] = Path::join(
                $this->context->getShopRootPath(),
                $configuration->getThemeSource()
            );
        }

        return [
            'id' => $this->themeStateService->getActiveThemeId($shopId),
            'chain' => $chain,
            'settings' => $settings,
            'sources' => $sources,
        ];
    }
}
