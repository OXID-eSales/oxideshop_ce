<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Integration\Internal\Domain\Product\Media;

use OxidEsales\EshopCommunity\Internal\Domain\Media\DataObject\Media;
use OxidEsales\EshopCommunity\Internal\Domain\Media\DataObject\MediaPath;
use OxidEsales\EshopCommunity\Internal\Domain\Media\DataObject\MediaType;
use OxidEsales\EshopCommunity\Internal\Domain\Product\Media\DataObject\ProductMedia;
use OxidEsales\EshopCommunity\Internal\Domain\Product\Media\DataObject\ProductMediaRole;
use OxidEsales\EshopCommunity\Internal\Domain\Product\Media\DataObject\ProductMediaRoleSet;
use OxidEsales\EshopCommunity\Internal\Domain\Product\Media\DataObject\SystemProductMediaRole;
use OxidEsales\EshopCommunity\Internal\Domain\Product\Media\ProductMediaServiceInterface;
use OxidEsales\EshopCommunity\Internal\Domain\Product\Media\ProductMediaViewServiceInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Config\Dao\ShopConfigurationSettingDaoInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Config\Dao\ThemeConfigurationSettingDaoInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Config\DataObject\ShopConfigurationSetting;
use OxidEsales\EshopCommunity\Internal\Framework\Config\DataObject\ShopSettingType;
use OxidEsales\EshopCommunity\Internal\Framework\Config\DataObject\ThemeConfigurationSetting;
use OxidEsales\EshopCommunity\Internal\Framework\Database\ConnectionFactoryInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Database\Id;
use OxidEsales\EshopCommunity\Internal\Transition\Adapter\ShopAdapterInterface;
use OxidEsales\EshopCommunity\Internal\Transition\Utility\ContextInterface;
use OxidEsales\EshopCommunity\Tests\ContainerTrait;
use OxidEsales\EshopCommunity\Tests\DatabaseTrait;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Filesystem\Path;

final class ProductMediaViewServiceTest extends TestCase
{
    use ContainerTrait;
    use DatabaseTrait;

    private const CONFIG_KEY_ICON_SIZE = 'sIconsize';
    private const CONFIG_KEY_THUMBNAIL_SIZE = 'sThumbnailsize';
    private const CONFIG_KEY_DETAIL_IMAGE_SIZE = 'sDetailImageSize';
    private const CONFIG_KEY_ZOOM_IMAGE_SIZE = 'sZoomImageSize';
    private const CONFIG_KEY_DEFAULT_IMAGE_QUALITY = 'sDefaultImageQuality';
    private const CONFIG_KEY_CONVERT_IMAGES_TO_WEBP = 'blConvertImagesToWebP';

    private ProductMediaViewServiceInterface $service;
    private ProductMediaServiceInterface $productMediaService;
    private ShopConfigurationSettingDaoInterface $configDao;
    private ThemeConfigurationSettingDaoInterface $themeConfigDao;
    private Id $productId;
    private string $baseUrl;
    private int $shopId;
    private string $themeId;

    protected function setUp(): void
    {
        parent::setUp();

        $this->setParameter('oxid_esales.alternative_image_url', '');
        $this->attachContainerToContainerFactory();

        $this->beginTransaction($this->get(ConnectionFactoryInterface::class)->create());

        $this->service = $this->get(ProductMediaViewServiceInterface::class);
        $this->productMediaService = $this->get(ProductMediaServiceInterface::class);
        $this->configDao = $this->get(ShopConfigurationSettingDaoInterface::class);
        $this->themeConfigDao = $this->get(ThemeConfigurationSettingDaoInterface::class);
        $context = $this->get(ContextInterface::class);
        $this->baseUrl = $context->getShopBaseUrl();
        $this->shopId = $context->getCurrentShopId();
        $this->themeId = $this->get(ShopAdapterInterface::class)->getActiveThemeId();

        $this->productId = Id::generate();
        $this->configureImageSettings();
        $this->setupTestData();
    }

    protected function tearDown(): void
    {
        $this->rollBackTransaction($this->get(ConnectionFactoryInterface::class)->create());
        parent::tearDown();
    }

    public function testGetIconReturnsMediaViewWithAllUrls(): void
    {
        $result = $this->service->getIcon($this->productId);

        $this->assertEquals(
            $this->expectedUrlFor(self::CONFIG_KEY_DETAIL_IMAGE_SIZE, 'icon.jpg'),
            $result->getUrl()
        );
        $this->assertEquals(
            $this->expectedUrlFor(self::CONFIG_KEY_ICON_SIZE, 'icon.jpg'),
            $result->getIconUrl()
        );
        $this->assertEquals(
            $this->expectedUrlFor(self::CONFIG_KEY_ZOOM_IMAGE_SIZE, 'icon.jpg'),
            $result->getZoomUrl()
        );
    }

    public function testGetIconReturnsFallbackWhenMissing(): void
    {
        $result = $this->service->getIcon(Id::generate());

        $this->assertEquals(
            $this->expectedUrlFor(self::CONFIG_KEY_DETAIL_IMAGE_SIZE, 'nopic.jpg'),
            $result->getUrl()
        );
        $this->assertEquals(
            $this->expectedUrlFor(self::CONFIG_KEY_ICON_SIZE, 'nopic.jpg'),
            $result->getIconUrl()
        );
        $this->assertEquals(
            $this->expectedUrlFor(self::CONFIG_KEY_ZOOM_IMAGE_SIZE, 'nopic.jpg'),
            $result->getZoomUrl()
        );
        $this->assertTrue($result->isFallback());
    }

    public function testGetThumbnailReturnsMediaViewWithAllUrls(): void
    {
        $result = $this->service->getThumbnail($this->productId);

        $this->assertEquals(
            $this->expectedUrlFor(self::CONFIG_KEY_DETAIL_IMAGE_SIZE, 'thumb.jpg'),
            $result->getUrl()
        );
        $this->assertEquals(
            $this->expectedUrlFor(self::CONFIG_KEY_ICON_SIZE, 'thumb.jpg'),
            $result->getIconUrl()
        );
        $this->assertEquals(
            $this->expectedUrlFor(self::CONFIG_KEY_ZOOM_IMAGE_SIZE, 'thumb.jpg'),
            $result->getZoomUrl()
        );
    }

    public function testGetMediaReturnsMediaViewWithAllUrls(): void
    {
        $result = $this->service->getMedia($this->productId, 1);

        $this->assertEquals(
            $this->expectedUrlFor(self::CONFIG_KEY_DETAIL_IMAGE_SIZE, 'detail.jpg'),
            $result->getUrl()
        );
        $this->assertEquals(
            $this->expectedUrlFor(self::CONFIG_KEY_ICON_SIZE, 'detail.jpg'),
            $result->getIconUrl()
        );
        $this->assertEquals(
            $this->expectedUrlFor(self::CONFIG_KEY_ZOOM_IMAGE_SIZE, 'detail.jpg'),
            $result->getZoomUrl()
        );
    }

    public function testGetMediaReturnsFallbackForMissingPosition(): void
    {
        $result = $this->service->getMedia($this->productId, 999);

        $this->assertEquals(
            $this->expectedUrlFor(self::CONFIG_KEY_DETAIL_IMAGE_SIZE, 'nopic.jpg'),
            $result->getUrl()
        );
        $this->assertEquals(
            $this->expectedUrlFor(self::CONFIG_KEY_ICON_SIZE, 'nopic.jpg'),
            $result->getIconUrl()
        );
        $this->assertEquals(
            $this->expectedUrlFor(self::CONFIG_KEY_ZOOM_IMAGE_SIZE, 'nopic.jpg'),
            $result->getZoomUrl()
        );
        $this->assertTrue($result->isFallback());
    }

    public function testUsesConfiguredQuality(): void
    {
        $this->setStringConfigValue(self::CONFIG_KEY_DEFAULT_IMAGE_QUALITY, '95');

        $result = $this->service->getIcon($this->productId);
        $this->assertEquals(
            $this->expectedUrlFor(self::CONFIG_KEY_DETAIL_IMAGE_SIZE, 'icon.jpg', '95'),
            $result->getUrl()
        );
        $this->assertEquals(
            $this->expectedUrlFor(self::CONFIG_KEY_ICON_SIZE, 'icon.jpg', '95'),
            $result->getIconUrl()
        );
        $this->assertEquals(
            $this->expectedUrlFor(self::CONFIG_KEY_ZOOM_IMAGE_SIZE, 'icon.jpg', '95'),
            $result->getZoomUrl()
        );
    }

    public function testIconFallsBackToFirstActiveWhenIconMissing(): void
    {
        $otherProductId = Id::generate();

        $media = new Media(
            Id::generate(),
            new MediaPath(Path::join('out', 'pictures', 'media', 'detail.jpg')),
            new MediaType('image/jpeg')
        );

        $roleSet = new ProductMediaRoleSet(ProductMediaRole::from(SystemProductMediaRole::Detail->value));
        $productMedia = new ProductMedia(
            Id::generate(),
            $otherProductId,
            $media,
            $roleSet
        );
        $productMedia->setPosition(1);
        $this->productMediaService->add($productMedia);

        $result = $this->service->getIcon($otherProductId);
        $this->assertEquals(
            $this->expectedUrlFor(self::CONFIG_KEY_DETAIL_IMAGE_SIZE, 'detail.jpg'),
            $result->getUrl()
        );
        $this->assertEquals(
            $this->expectedUrlFor(self::CONFIG_KEY_ICON_SIZE, 'detail.jpg'),
            $result->getIconUrl()
        );
        $this->assertEquals(
            $this->expectedUrlFor(self::CONFIG_KEY_ZOOM_IMAGE_SIZE, 'detail.jpg'),
            $result->getZoomUrl()
        );
    }

    public function testFallbackUsesWebPWhenEnabled(): void
    {
        $this->setBooleanConfigValue(self::CONFIG_KEY_CONVERT_IMAGES_TO_WEBP, true);
        $result = $this->service->getIcon(Id::generate());
        $this->assertEquals(
            $this->expectedUrlFor(self::CONFIG_KEY_DETAIL_IMAGE_SIZE, 'nopic.webp'),
            $result->getUrl()
        );
        $this->assertEquals(
            $this->expectedUrlFor(self::CONFIG_KEY_ICON_SIZE, 'nopic.webp'),
            $result->getIconUrl()
        );
        $this->assertEquals(
            $this->expectedUrlFor(self::CONFIG_KEY_ZOOM_IMAGE_SIZE, 'nopic.webp'),
            $result->getZoomUrl()
        );
    }

    public function testGetActiveByProductIdReturnsMediaViews(): void
    {
        $results = $this->service->getActiveByProductId($this->productId);

        $this->assertCount(3, $results);
        $urls = array_map(fn($result) => $result->getUrl(), $results);

        $this->assertContains(
            $this->expectedUrlFor(self::CONFIG_KEY_DETAIL_IMAGE_SIZE, 'icon.jpg'),
            $urls
        );
        $this->assertContains(
            $this->expectedUrlFor(self::CONFIG_KEY_DETAIL_IMAGE_SIZE, 'thumb.jpg'),
            $urls
        );
        $this->assertContains(
            $this->expectedUrlFor(self::CONFIG_KEY_DETAIL_IMAGE_SIZE, 'detail.jpg'),
            $urls
        );
    }

    public function testGetActiveByProductIdReturnsEmptyForNonExistentProduct(): void
    {
        $results = $this->service->getActiveByProductId(Id::generate());

        $this->assertEmpty($results);
    }

    public function testGetThumbnailUrl(): void
    {
        $result = $this->service->getIcon($this->productId);

        $this->assertEquals(
            $this->expectedUrlFor(self::CONFIG_KEY_THUMBNAIL_SIZE, 'icon.jpg'),
            $result->getThumbnailUrl()
        );
    }

    public function testFallbackHasThumbnailUrl(): void
    {
        $result = $this->service->getIcon(Id::generate());

        $this->assertEquals(
            $this->expectedUrlFor(self::CONFIG_KEY_THUMBNAIL_SIZE, 'nopic.jpg'),
            $result->getThumbnailUrl()
        );
        $this->assertTrue($result->isFallback());
    }

    public function testGetMediaWithFallbackBehavior(): void
    {
        $otherProductId = Id::generate();
        $this->addProductMedia(
            $otherProductId,
            Path::join('out', 'pictures', 'media', 'fallback.jpg'),
            1,
            SystemProductMediaRole::Detail
        );

        $iconResult = $this->service->getIcon($otherProductId);

        $this->assertEquals(
            $this->expectedUrlFor(self::CONFIG_KEY_DETAIL_IMAGE_SIZE, 'fallback.jpg'),
            $iconResult->getUrl()
        );
        $this->assertFalse($iconResult->isFallback());
    }

    private function setupTestData(): void
    {
        $this->addProductMedia(
            $this->productId,
            Path::join('out', 'pictures', 'media', 'icon.jpg'),
            0,
            SystemProductMediaRole::Icon
        );
        $this->addProductMedia(
            $this->productId,
            Path::join('out', 'pictures', 'media', 'thumb.jpg'),
            0,
            SystemProductMediaRole::Thumb
        );
        $this->addProductMedia(
            $this->productId,
            Path::join('out', 'pictures', 'media', 'detail.jpg'),
            1,
            SystemProductMediaRole::Detail
        );
    }

    private function addProductMedia(Id $productId, string $path, int $position, SystemProductMediaRole $role): void
    {
        $media = new Media(Id::generate(), new MediaPath($path), new MediaType('image/jpeg'));
        $roleSet = new ProductMediaRoleSet(ProductMediaRole::from($role->value));
        $productMedia = new ProductMedia(Id::generate(), $productId, $media, $roleSet);
        $productMedia->setPosition($position);
        $this->productMediaService->add($productMedia);
    }

    private function configureImageSettings(): void
    {
        $this->setThemeStringConfigValue(self::CONFIG_KEY_ICON_SIZE, '87*87');
        $this->setThemeStringConfigValue(self::CONFIG_KEY_THUMBNAIL_SIZE, '200*200');
        $this->setThemeStringConfigValue(self::CONFIG_KEY_DETAIL_IMAGE_SIZE, '600*600');
        $this->setThemeStringConfigValue(self::CONFIG_KEY_ZOOM_IMAGE_SIZE, '1200*1200');
        $this->setStringConfigValue(self::CONFIG_KEY_DEFAULT_IMAGE_QUALITY, '75');
        $this->setBooleanConfigValue(self::CONFIG_KEY_CONVERT_IMAGES_TO_WEBP, false);
    }

    private function expectedUrlFor(string $sizeConfigKey, string $filename, ?string $qualityOverride = null): string
    {
        $size = $this->getSizeFromConfig($sizeConfigKey);
        $quality = $qualityOverride ?? $this->getQualityFromConfig();
        $sizePath = $size['width'] . '_' . $size['height'] . '_' . $quality;
        return Path::join(
            $this->baseUrl,
            'out',
            'pictures',
            'generated',
            'media',
            $sizePath,
            $filename
        );
    }

    private function getSizeFromConfig(string $key): array
    {
        $value = (string) $this->themeConfigDao->get($key, $this->shopId, $this->themeId)->getValue();
        [$width, $height] = explode('*', $value);
        return ['width' => (int) $width, 'height' => (int) $height];
    }

    private function getQualityFromConfig(): string
    {
        return (string) $this->configDao->get(self::CONFIG_KEY_DEFAULT_IMAGE_QUALITY, $this->shopId)->getValue();
    }

    private function setThemeStringConfigValue(string $name, string $value): void
    {
        $setting = new ThemeConfigurationSetting();
        $setting->setName($name);
        $setting->setValue($value);
        $setting->setType(ShopSettingType::STRING);
        $setting->setShopId($this->shopId);
        $setting->setThemeId($this->themeId);
        $this->themeConfigDao->save($setting);
    }

    private function setStringConfigValue(string $name, string $value): void
    {
        $setting = new ShopConfigurationSetting();
        $setting->setName($name);
        $setting->setValue($value);
        $setting->setType(ShopSettingType::STRING);
        $setting->setShopId($this->shopId);
        $this->configDao->save($setting);
    }

    private function setBooleanConfigValue(string $name, bool $value): void
    {
        $setting = new ShopConfigurationSetting();
        $setting->setName($name);
        $setting->setValue($value);
        $setting->setType(ShopSettingType::BOOLEAN);
        $setting->setShopId($this->shopId);
        $this->configDao->save($setting);
    }
}
