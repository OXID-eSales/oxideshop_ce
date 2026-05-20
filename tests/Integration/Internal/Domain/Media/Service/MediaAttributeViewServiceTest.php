<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Integration\Internal\Domain\Media\Service;

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
    private MediaAttributeViewServiceInterface $service;
    private MediaAttributeDaoInterface $dao;
    private Media $media;
    private Locale $activeLocale;
    private int $shopId;

    public function setUp(): void
    {
        parent::setUp();
        $this->service = $this->get(MediaAttributeViewServiceInterface::class);
        $this->dao = $this->get(MediaAttributeDaoInterface::class);
        $this->activeLocale = $this->get(ActiveLocaleProviderInterface::class)->getActiveLocale();
        $this->shopId = $this->get(ContextInterface::class)->getCurrentShopId();
        $this->media = $this->createMedia();
    }

    public function testGetAttributesFollowsFallbackChain(): void
    {
        $primaryLocale = $this->addLocaleWithFallback('fr_FR', $this->activeLocale->getCode());

        $this->dao->save($this->createAttribute($this->media, $this->activeLocale, MediaAttribute::ALT, 'fallback alt'));

        $result = $this->service->getAttributes($this->media, $primaryLocale->getCode());

        $this->assertSame('fallback alt', $result->getAlt());
    }

    public function testGetAttributesPreferPrimaryOverFallback(): void
    {
        $primaryLocale = $this->addLocaleWithFallback('fr_FR', $this->activeLocale->getCode());

        $this->dao->save($this->createAttribute($this->media, $primaryLocale, MediaAttribute::ALT, 'primary alt'));
        $this->dao->save($this->createAttribute($this->media, $this->activeLocale, MediaAttribute::ALT, 'fallback alt'));

        $result = $this->service->getAttributes($this->media, $primaryLocale->getCode());

        $this->assertSame('primary alt', $result->getAlt());
    }

    public function testGetAttributesMergesIndependentAttributesAcrossChain(): void
    {
        $primaryLocale = $this->addLocaleWithFallback('fr_FR', $this->activeLocale->getCode());

        $this->dao->save($this->createAttribute($this->media, $primaryLocale, 'title', 'french title'));
        $this->dao->save($this->createAttribute($this->media, $this->activeLocale, MediaAttribute::ALT, 'fallback alt'));

        $result = $this->service->getAttributes($this->media, $primaryLocale->getCode());

        $this->assertSame('french title', $result->get('title'));
        $this->assertSame('fallback alt', $result->getAlt());
    }

    public function testGetAttributesIgnoresLocalesOutsideChain(): void
    {
        $primaryLocale = $this->addLocaleWithFallback('fr_FR', $this->activeLocale->getCode());
        $unrelatedLocale = $this->addLocaleWithFallback('it_IT', 'it_IT');

        $this->dao->save($this->createAttribute($this->media, $unrelatedLocale, MediaAttribute::ALT, 'italian alt'));

        $result = $this->service->getAttributes($this->media, $primaryLocale->getCode());

        $this->assertFalse($result->has(MediaAttribute::ALT));
    }

    public function testGetAttributesReturnsEmptyForMediaWithoutRows(): void
    {
        $result = $this->service->getAttributes($this->media, $this->activeLocale->getCode());

        $this->assertFalse($result->has(MediaAttribute::ALT));
    }

    public function testGetAttributesIgnoresOtherShops(): void
    {
        $this->dao->save(new MediaAttribute(
            Id::generate(),
            $this->media->getId(),
            $this->activeLocale->getCode(),
            2,
            MediaAttribute::ALT,
            'other shop alt',
        ));

        $result = $this->service->getAttributes($this->media, $this->activeLocale->getCode());

        $this->assertFalse($result->has(MediaAttribute::ALT));
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

    private function createAttribute(Media $media, Locale $locale, string $name, string $value): MediaAttribute
    {
        return new MediaAttribute(
            Id::generate(),
            $media->getId(),
            $locale->getCode(),
            $this->shopId,
            $name,
            $value
        );
    }
}
