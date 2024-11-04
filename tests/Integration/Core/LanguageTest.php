<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Integration\Core;

use OxidEsales\Eshop\Core\Language;
use OxidEsales\EshopCommunity\Core\Di\ContainerFacade;
use OxidEsales\EshopCommunity\Core\Registry;
use OxidEsales\EshopCommunity\Tests\Integration\IntegrationTestCase;
use Psr\Log\LoggerInterface;
use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Contracts\Cache\TagAwareCacheInterface;

class LanguageTest extends IntegrationTestCase
{
    public function testTranslateCachedString(): void
    {
        $stringToTranslate = uniqid();
        $cache = ContainerFacade::get(TagAwareCacheInterface::class);
        $logger = $this->createMock(LoggerInterface::class);
        Registry::set('logger', $logger);

        $config = Registry::getConfig();

        $logger->expects($this->once())
            ->method('warning')
            ->with(sprintf('translation for %s not found', $stringToTranslate), $this->anything());

        $language = new Language();
        $translatedString = $language->translateString($stringToTranslate, $language->getBaseLanguage());

        $this->assertEquals($stringToTranslate, $translatedString);

        $cache->invalidateTags(['oxid_esales.cache.language']);

        $langCacheName = sprintf(
            'langcache_%d_%s_%d_%s_default',
            $config->isAdmin(),
            $language->getBaseLanguage(),
            $config->getShopId(),
            $config->getConfigParam('sTheme')
        );

        $cache->get($langCacheName, function (ItemInterface $item) use ($stringToTranslate) {
            $item->tag('oxid_esales.cache.language');
            return [$stringToTranslate => 'translated value'];
        });

        $language = new Language();
        $translatedString = $language->translateString($stringToTranslate, $language->getBaseLanguage());

        $this->assertEquals('translated value', $translatedString);

        $cache->invalidateTags(['oxid_esales.cache.language']);
    }
}
