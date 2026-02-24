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
use OxidEsales\EshopCommunity\Tests\Unit\Internal\ContextStub;
use PHPUnit\Framework\TestCase;

final class MediaUrlGeneratorTest extends TestCase
{
    private ContextInterface $context;
    private ShopConfigurationSettingDaoInterface $configDao;
    private MediaUrlGenerator $urlGenerator;

    protected function setUp(): void
    {
        $this->context = new ContextStub();
        $this->configDao = $this->createStub(ShopConfigurationSettingDaoInterface::class);

        $this->context->setShopBaseUrl('https://shop.example.com/');

        $qualitySetting = $this->createStub(ShopConfigurationSetting::class);
        $qualitySetting->method('getValue')->willReturn('75');

        $this->configDao->method('get')
            ->willReturn($qualitySetting);
    }

    public function testGeneratesUrlForLocalStorage(): void
    {
        $this->urlGenerator = new MediaUrlGenerator($this->context, $this->configDao);

        $media = new Media(
            Id::generate(),
            new MediaPath('out/pictures/media/products/1001/product.jpg'),
            new MediaType('image/jpeg')
        );

        $result = $this->urlGenerator->generateSizedImageUrl($media, '300*200');

        $this->assertEquals(
            'https://shop.example.com/out/pictures/generated/media/products/1001/300_200_75/product.jpg',
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
            new MediaPath('out/pictures/media/products/1001/product.jpg'),
            new MediaType('image/jpeg')
        );

        $result = $this->urlGenerator->generateSizedImageUrl($media, '300*200');

        $this->assertEquals(
            'https://cdn.example.com/generated/media/products/1001/300_200_75/product.jpg',
            $result
        );
    }

    public function testHandlesDifferentImageSizes(): void
    {
        $this->urlGenerator = new MediaUrlGenerator($this->context, $this->configDao);

        $media = new Media(
            Id::generate(),
            new MediaPath('out/pictures/media/products/1001/test.png'),
            new MediaType('image/png')
        );

        $thumbnail = $this->urlGenerator->generateSizedImageUrl($media, '87*87');
        $detail = $this->urlGenerator->generateSizedImageUrl($media, '600*600');

        $this->assertEquals(
            'https://shop.example.com/out/pictures/generated/media/products/1001/87_87_75/test.png',
            $thumbnail
        );
        $this->assertEquals(
            'https://shop.example.com/out/pictures/generated/media/products/1001/600_600_75/test.png',
            $detail
        );
    }

    public function testGeneratesUrlWithEncodedFilename(): void
    {
        $this->urlGenerator = new MediaUrlGenerator($this->context, $this->configDao);

        $media = new Media(
            Id::generate(),
            new MediaPath('out/pictures/media/products/1001/Foto 1+#.jpg'),
            new MediaType('image/jpeg')
        );

        $result = $this->urlGenerator->generateSizedImageUrl($media, '300*200');

        $this->assertEquals(
            'https://shop.example.com/out/pictures/generated/media/products/1001/300_200_75/Foto%201%2B%23.jpg',
            $result
        );
    }

    public function testUsesConfiguredImageQuality(): void
    {
        $qualitySetting = $this->createStub(ShopConfigurationSetting::class);
        $qualitySetting->method('getValue')->willReturn('95');

        $configDao = $this->createStub(ShopConfigurationSettingDaoInterface::class);
        $configDao->method('get')
            ->willReturn($qualitySetting);

        $this->urlGenerator = new MediaUrlGenerator($this->context, $configDao);

        $media = new Media(
            Id::generate(),
            new MediaPath('out/pictures/media/products/1001/hq.jpg'),
            new MediaType('image/jpeg')
        );

        $result = $this->urlGenerator->generateSizedImageUrl($media, '400*300');

        $this->assertEquals(
            'https://shop.example.com/out/pictures/generated/media/products/1001/400_300_95/hq.jpg',
            $result
        );
    }
}
