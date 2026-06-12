<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Integration\Internal\Domain\Media\Service;

use OxidEsales\Eshop\Core\Registry;
use OxidEsales\EshopCommunity\Internal\Domain\Locale\Dao\LocaleDaoInterface;
use OxidEsales\EshopCommunity\Internal\Domain\Locale\DataObject\Locale;
use OxidEsales\EshopCommunity\Internal\Domain\Locale\Service\ActiveLocaleProviderInterface;
use OxidEsales\EshopCommunity\Internal\Domain\Media\Dao\MediaAttributeDaoInterface;
use OxidEsales\EshopCommunity\Internal\Domain\Media\Dao\MediaDaoInterface;
use OxidEsales\EshopCommunity\Internal\Domain\Media\DataObject\Media;
use OxidEsales\EshopCommunity\Internal\Domain\Media\DataObject\MediaAttribute;
use OxidEsales\EshopCommunity\Internal\Domain\Media\DataObject\MediaPath;
use OxidEsales\EshopCommunity\Internal\Domain\Media\DataObject\MediaType;
use OxidEsales\EshopCommunity\Internal\Domain\Media\Service\MediaAttributeViewServiceInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Database\Id;
use OxidEsales\EshopCommunity\Internal\Transition\Utility\ContextInterface;
use OxidEsales\EshopCommunity\Tests\Integration\IntegrationTestCase;

final class MediaAttributeViewServiceTest extends IntegrationTestCase
{
    private const DEFAULT_LOCALE_CODE = 'de_DE';

    private MediaAttributeViewServiceInterface $service;
    private MediaAttributeDaoInterface $dao;
    private Media $media;
    private int $shopId;

    public function setUp(): void
    {
        parent::setUp();
        $this->service = $this->get(MediaAttributeViewServiceInterface::class);
        $this->dao = $this->get(MediaAttributeDaoInterface::class);
        $this->shopId = $this->get(ContextInterface::class)->getCurrentShopId();
        $this->media = $this->createMedia();
    }

    public function testGetAttributesUsesActiveLocale(): void
    {
        $primaryLocale = $this->addLocaleWithFallback('fr_FR', self::DEFAULT_LOCALE_CODE);
        $this->switchActiveLocale($primaryLocale->getCode());
        $this->dao->save($this->createAttribute($this->media, $primaryLocale->getCode(), 'alt', 'french alt'));

        $result = $this->service->getAttributes($this->media);

        $this->assertSame('french alt', $result->getAlt());
    }

    public function testGetAttributesFollowsFallbackChain(): void
    {
        $fallbackLocale = $this->addLocaleWithFallback('it_IT', 'it_IT');
        $primaryLocale = $this->addLocaleWithFallback('fr_FR', $fallbackLocale->getCode());
        $this->switchActiveLocale($primaryLocale->getCode());
        $this->dao->save($this->createAttribute($this->media, $fallbackLocale->getCode(), 'alt', 'fallback alt'));

        $result = $this->service->getAttributes($this->media);

        $this->assertSame('fallback alt', $result->getAlt());
    }

    public function testGetAttributesPreferPrimaryOverFallback(): void
    {
        $primaryLocale = $this->addLocaleWithFallback('fr_FR', self::DEFAULT_LOCALE_CODE);
        $this->switchActiveLocale($primaryLocale->getCode());
        $this->dao->save($this->createAttribute($this->media, $primaryLocale->getCode(), 'alt', 'primary alt'));
        $this->dao->save($this->createAttribute($this->media, self::DEFAULT_LOCALE_CODE, 'alt', 'fallback alt'));

        $result = $this->service->getAttributes($this->media);

        $this->assertSame('primary alt', $result->getAlt());
    }

    public function testGetAttributesMergesIndependentAttributesAcrossChain(): void
    {
        $primaryLocale = $this->addLocaleWithFallback('fr_FR', self::DEFAULT_LOCALE_CODE);
        $this->switchActiveLocale($primaryLocale->getCode());
        $this->dao->save($this->createAttribute($this->media, $primaryLocale->getCode(), 'title', 'french title'));
        $this->dao->save($this->createAttribute($this->media, self::DEFAULT_LOCALE_CODE, 'alt', 'fallback alt'));

        $result = $this->service->getAttributes($this->media);

        $this->assertSame('french title', $result->get('title'));
        $this->assertSame('fallback alt', $result->getAlt());
    }

    public function testGetAttributesIgnoresLocalesOutsideChain(): void
    {
        $primaryLocale = $this->addLocaleWithFallback('fr_FR', self::DEFAULT_LOCALE_CODE);
        $unrelatedLocale = $this->addLocaleWithFallback('it_IT', 'it_IT');
        $this->switchActiveLocale($primaryLocale->getCode());
        $this->dao->save($this->createAttribute($this->media, $unrelatedLocale->getCode(), 'alt', 'italian alt'));

        $result = $this->service->getAttributes($this->media);

        $this->assertFalse($result->has('alt'));
    }

    public function testGetAttributesReturnsEmptyForMediaWithoutRows(): void
    {
        $result = $this->service->getAttributes($this->media);

        $this->assertFalse($result->has('alt'));
    }

    public function testGetAttributesIgnoresOtherShops(): void
    {
        $activeLocale = $this->get(ActiveLocaleProviderInterface::class)->getActiveLocale();
        $this->dao->save(new MediaAttribute(
            Id::generate(),
            $this->media->getId(),
            $activeLocale->getCode(),
            2,
            'alt',
            'other shop alt',
        ));

        $result = $this->service->getAttributes($this->media);

        $this->assertFalse($result->has('alt'));
    }

    private function switchActiveLocale(string $code): void
    {
        $config = Registry::getConfig();
        $params = $config->getConfigParam('aLanguageParams');
        $params[Registry::getLang()->getLanguageAbbr()]['locale'] = $code;
        $config->saveShopConfVar('aarr', 'aLanguageParams', $params);
    }

    private function addLocaleWithFallback(string $code, string $fallbackCode): Locale
    {
        $locale = new Locale($code, $code, $fallbackCode);
        $localeDao = $this->get(LocaleDaoInterface::class);
        $localeDao->add($locale);
        $localeDao->addToShop($code, $this->shopId);
        return $locale;
    }

    private function createMedia(): Media
    {
        $media = new Media(Id::generate(), new MediaPath('media/test.jpg'), new MediaType('image/jpeg'));
        $this->get(MediaDaoInterface::class)->add($media);
        return $media;
    }

    private function createAttribute(Media $media, string $localeCode, string $name, string $value): MediaAttribute
    {
        return new MediaAttribute(
            Id::generate(),
            $media->getId(),
            $localeCode,
            $this->shopId,
            $name,
            $value
        );
    }
}
