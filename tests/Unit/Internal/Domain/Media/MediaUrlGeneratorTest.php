<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Unit\Internal\Domain\Media;

use OxidEsales\EshopCommunity\Internal\Domain\Media\DataObject\Media;
use OxidEsales\EshopCommunity\Internal\Domain\Media\DataObject\MediaPath;
use OxidEsales\EshopCommunity\Internal\Domain\Media\DataObject\MediaType;
use OxidEsales\EshopCommunity\Internal\Domain\Media\MediaUrlGenerator;
use OxidEsales\EshopCommunity\Internal\Framework\Config\Dao\ShopConfigurationSettingDaoInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Config\DataObject\ShopConfigurationSetting;
use OxidEsales\EshopCommunity\Internal\Framework\Database\Id;
use OxidEsales\EshopCommunity\Internal\Transition\Utility\ContextInterface;
use PHPUnit\Framework\TestCase;

final class MediaUrlGeneratorTest extends TestCase
{
    private ContextInterface $context;
    private ShopConfigurationSettingDaoInterface $configDao;
    private MediaUrlGenerator $urlGenerator;

    protected function setUp(): void
    {
        $this->context = $this->createMock(ContextInterface::class);
        $this->configDao = $this->createMock(ShopConfigurationSettingDaoInterface::class);

        $this->context->method('getShopBaseUrl')->willReturn('https://shop.example.com/');
        $this->context->method('getCurrentShopId')->willReturn(1);

        $qualitySetting = $this->createMock(ShopConfigurationSetting::class);
        $qualitySetting->method('getValue')->willReturn('75');

        $this->configDao->method('get')
            ->with('sDefaultImageQuality', 1)
            ->willReturn($qualitySetting);
    }

    public function testGeneratesUrlForLocalStorage(): void
    {
        $this->urlGenerator = new MediaUrlGenerator($this->context, $this->configDao);

        $media = new Media(
            Id::generate(),
            new MediaPath('out/pictures/media/product.jpg'),
            new MediaType('image/jpeg')
        );

        $result = $this->urlGenerator->generateSizedImageUrl($media, '300*200');

        $this->assertEquals(
            'https://shop.example.com/out/pictures/generated/media/300_200_75/product.jpg',
            $result
        );
    }

    public function testGeneratesUrlForCdnStorage(): void
    {
        $this->urlGenerator = new MediaUrlGenerator(
            $this->context,
            $this->configDao,
            'https://cdn.example.com/'
        );

        $media = new Media(
            Id::generate(),
            new MediaPath('out/pictures/media/product.jpg'),
            new MediaType('image/jpeg')
        );

        $result = $this->urlGenerator->generateSizedImageUrl($media, '300*200');

        $this->assertEquals(
            'https://cdn.example.com/generated/media/300_200_75/product.jpg',
            $result
        );
    }

    public function testHandlesDifferentImageSizes(): void
    {
        $this->urlGenerator = new MediaUrlGenerator($this->context, $this->configDao);

        $media = new Media(
            Id::generate(),
            new MediaPath('out/pictures/media/test.png'),
            new MediaType('image/png')
        );

        $thumbnail = $this->urlGenerator->generateSizedImageUrl($media, '87*87');
        $detail = $this->urlGenerator->generateSizedImageUrl($media, '600*600');

        $this->assertEquals(
            'https://shop.example.com/out/pictures/generated/media/87_87_75/test.png',
            $thumbnail
        );
        $this->assertEquals(
            'https://shop.example.com/out/pictures/generated/media/600_600_75/test.png',
            $detail
        );
    }

    public function testUsesConfiguredImageQuality(): void
    {
        $qualitySetting = $this->createMock(ShopConfigurationSetting::class);
        $qualitySetting->method('getValue')->willReturn('95');

        $configDao = $this->createMock(ShopConfigurationSettingDaoInterface::class);
        $configDao->method('get')
            ->with('sDefaultImageQuality')
            ->willReturn($qualitySetting);

        $this->urlGenerator = new MediaUrlGenerator($this->context, $configDao);

        $media = new Media(
            Id::generate(),
            new MediaPath('out/pictures/media/hq.jpg'),
            new MediaType('image/jpeg')
        );

        $result = $this->urlGenerator->generateSizedImageUrl($media, '400*300');

        $this->assertEquals(
            'https://shop.example.com/out/pictures/generated/media/400_300_95/hq.jpg',
            $result
        );
    }
}
