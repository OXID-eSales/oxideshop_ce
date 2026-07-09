<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Integration\Core;

use OxidEsales\Eshop\Core\Language;
use OxidEsales\EshopCommunity\Core\Registry;
use OxidEsales\EshopCommunity\Internal\Framework\Cache\ShopCacheCleanerInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\State\Exception\ActiveThemeNotFoundException;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\State\ThemeStateServiceInterface;
use OxidEsales\EshopCommunity\Tests\Integration\IntegrationTestCase;
use Psr\Log\LoggerInterface;
use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Contracts\Cache\TagAwareCacheInterface;

use function sprintf;

final class LanguageTest extends IntegrationTestCase
{
    public function tearDown(): void
    {
        $this->get(TagAwareCacheInterface::class)->invalidateTags(['oxid_esales.cache.language']);

        parent::tearDown();
    }

    public function testTranslateStringWithMissingTranslation(): void
    {
        $translationKey = uniqid('some-key-', true);
        $language = new Language();
        $logger = $this->createMock(LoggerInterface::class);
        Registry::set('logger', $logger);

        $logger->expects($this->once())
            ->method('warning')
            ->with(sprintf('translation for %s not found', $translationKey), $this->anything());

        $translation = $language->translateString($translationKey, $language->getBaseLanguage());

        $this->assertEquals($translationKey, $translation);
    }

    public function testTranslateStringWithTranslationInCache(): void
    {
        $translationKey = uniqid('some-key-', true);
        $cachedTranslation = 'some-translation';
        $language = new Language();
        $shopId = (int) Registry::getConfig()->getShopId();
        try {
            $activeThemeId = $this->get(ThemeStateServiceInterface::class)->getActiveThemeId($shopId);
        } catch (ActiveThemeNotFoundException) {
            $activeThemeId = null;
        }
        $cacheKey = sprintf(
            'langcache_%d_%s_%d_%s_default',
            Registry::getConfig()->isAdmin(),
            $language->getBaseLanguage(),
            $shopId,
            $activeThemeId
        );

        $this->get(ShopCacheCleanerInterface::class)->clearAll();
        $this->get(TagAwareCacheInterface::class)
            ->get($cacheKey, function (ItemInterface $item) use ($translationKey, $cachedTranslation) {
                $item->tag('oxid_esales.cache.language');
                return [$translationKey => $cachedTranslation];
            });

        $translation = $language->translateString($translationKey, $language->getBaseLanguage());

        $this->assertEquals($cachedTranslation, $translation);
    }
}
