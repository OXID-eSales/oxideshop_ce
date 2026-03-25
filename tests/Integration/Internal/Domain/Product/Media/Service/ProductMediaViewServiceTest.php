<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Integration\Internal\Domain\Product\Media\Service;

use OxidEsales\EshopCommunity\Internal\Domain\Locale\Service\ActiveLocaleProviderInterface;
use OxidEsales\EshopCommunity\Internal\Domain\Media\DataObject\Media;
use OxidEsales\EshopCommunity\Internal\Domain\Media\DataObject\MediaAttribute;
use OxidEsales\EshopCommunity\Internal\Domain\Media\DataObject\MediaPath;
use OxidEsales\EshopCommunity\Internal\Domain\Media\DataObject\MediaType;
use OxidEsales\EshopCommunity\Internal\Domain\Product\Media\DataObject\ProductMedia;
use OxidEsales\EshopCommunity\Internal\Domain\Product\Media\DataObject\ProductMediaRole;
use OxidEsales\EshopCommunity\Internal\Domain\Product\Media\DataObject\ProductMediaRoleSet;
use OxidEsales\EshopCommunity\Internal\Domain\Media\Service\MediaAttributeServiceInterface;
use OxidEsales\EshopCommunity\Internal\Domain\Product\Media\Service\ProductMediaServiceInterface;
use OxidEsales\EshopCommunity\Internal\Domain\Product\Media\Service\ProductMediaViewServiceInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Config\Dao\ShopConfigurationSettingDaoInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\Config\Dao\ThemeSettingDaoInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Config\DataObject\ShopConfigurationSetting;
use OxidEsales\EshopCommunity\Internal\Framework\Config\DataObject\ShopSettingType;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\Config\DataObject\ThemeSetting;
use OxidEsales\EshopCommunity\Internal\Framework\Database\Id;
use OxidEsales\EshopCommunity\Internal\Transition\Adapter\ShopAdapterInterface;
use OxidEsales\EshopCommunity\Internal\Transition\Utility\ContextInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Database\ConnectionFactoryInterface;
use OxidEsales\EshopCommunity\Tests\Integration\IntegrationTestCase;
use Symfony\Component\Filesystem\Path;

final class ProductMediaViewServiceTest extends IntegrationTestCase
{
    private const CONFIG_KEY_ICON_SIZE = 'sIconsize';
    private const CONFIG_KEY_THUMBNAIL_SIZE = 'sThumbnailsize';
    private const CONFIG_KEY_DETAIL_IMAGE_SIZE = 'sDetailImageSize';
    private const CONFIG_KEY_ZOOM_IMAGE_SIZE = 'sZoomImageSize';
    private const CONFIG_KEY_DEFAULT_IMAGE_QUALITY = 'sDefaultImageQuality';
    private const CONFIG_KEY_CONVERT_IMAGES_TO_WEBP = 'blConvertImagesToWebP';

    private Id $productId;
    private string $baseUrl;
    private int $shopId;
    private string $themeId;

    public function setUp(): void
    {
        parent::setUp();

        $context = $this->get(ContextInterface::class);
        $this->baseUrl = $context->getShopBaseUrl();
        $this->shopId = $context->getCurrentShopId();
        $this->themeId = $this->get(ShopAdapterInterface::class)->getActiveThemeId();

        $this->productId = Id::generate();
        $this->configureImageSettings();
        $this->setupTestData();
    }

    public function testGetIconReturnsMediaViewWithAllUrls(): void
    {
        $result = $this->get(ProductMediaViewServiceInterface::class)
            ->getByRole($this->productId, ProductMediaRole::from(ProductMediaRole::ICON));

        $this->assertEquals(
            $this->expectedUrlFor(self::CONFIG_KEY_DETAIL_IMAGE_SIZE, 'icon.jpg'),
            $result->getDetailUrl()
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
        $result = $this->get(ProductMediaViewServiceInterface::class)
            ->getByRole(Id::generate(), ProductMediaRole::from(ProductMediaRole::ICON));

        $this->assertEquals(
            $this->expectedUrlFor(self::CONFIG_KEY_DETAIL_IMAGE_SIZE, 'nopic.jpg'),
            $result->getDetailUrl()
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
        $result = $this->get(ProductMediaViewServiceInterface::class)
            ->getByRole($this->productId, ProductMediaRole::from(ProductMediaRole::THUMBNAIL));

        $this->assertEquals(
            $this->expectedUrlFor(self::CONFIG_KEY_DETAIL_IMAGE_SIZE, 'thumb.jpg'),
            $result->getDetailUrl()
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
        $result = $this->get(ProductMediaViewServiceInterface::class)->getByPosition($this->productId, 1);

        $this->assertEquals(
            $this->expectedUrlFor(self::CONFIG_KEY_DETAIL_IMAGE_SIZE, 'detail.jpg'),
            $result->getDetailUrl()
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
        $result = $this->get(ProductMediaViewServiceInterface::class)->getByPosition($this->productId, 999);

        $this->assertEquals(
            $this->expectedUrlFor(self::CONFIG_KEY_DETAIL_IMAGE_SIZE, 'nopic.jpg'),
            $result->getDetailUrl()
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

        $result = $this->get(ProductMediaViewServiceInterface::class)
            ->getByRole($this->productId, ProductMediaRole::from(ProductMediaRole::ICON));
        $this->assertEquals(
            $this->expectedUrlFor(self::CONFIG_KEY_DETAIL_IMAGE_SIZE, 'icon.jpg', '95'),
            $result->getDetailUrl()
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

        $roleSet = new ProductMediaRoleSet(ProductMediaRole::from(ProductMediaRole::DETAIL));
        $productMedia = new ProductMedia(
            Id::generate(),
            $otherProductId,
            $media,
            $roleSet
        );
        $productMedia->setPosition(1);
        $this->get(ProductMediaServiceInterface::class)->add($productMedia);

        $result = $this->get(ProductMediaViewServiceInterface::class)
            ->getByRole($otherProductId, ProductMediaRole::from(ProductMediaRole::ICON));
        $this->assertEquals(
            $this->expectedUrlFor(self::CONFIG_KEY_DETAIL_IMAGE_SIZE, 'detail.jpg'),
            $result->getDetailUrl()
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
        $result = $this->get(ProductMediaViewServiceInterface::class)
            ->getByRole(Id::generate(), ProductMediaRole::from(ProductMediaRole::ICON));
        $this->assertEquals(
            $this->expectedUrlFor(self::CONFIG_KEY_DETAIL_IMAGE_SIZE, 'nopic.webp'),
            $result->getDetailUrl()
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

    public function testGetAllActiveDetailReturnsMediaViews(): void
    {
        $results = $this->get(ProductMediaViewServiceInterface::class)->getAllByRole(
            $this->productId,
            ProductMediaRole::from(ProductMediaRole::DETAIL)
        );

        $this->assertCount(3, $results);
        $urls = array_map(fn($result) => $result->getDetailUrl(), $results);

        $this->assertContains(
            $this->expectedUrlFor(self::CONFIG_KEY_DETAIL_IMAGE_SIZE, 'detail.jpg'),
            $urls
        );
        $this->assertContains(
            $this->expectedUrlFor(self::CONFIG_KEY_DETAIL_IMAGE_SIZE, 'detail-2.jpg'),
            $urls
        );
        $this->assertContains(
            $this->expectedUrlFor(self::CONFIG_KEY_DETAIL_IMAGE_SIZE, 'detail-3.jpg'),
            $urls
        );
        $this->assertNotContains(
            $this->expectedUrlFor(self::CONFIG_KEY_DETAIL_IMAGE_SIZE, 'icon.jpg'),
            $urls
        );
        $this->assertNotContains(
            $this->expectedUrlFor(self::CONFIG_KEY_DETAIL_IMAGE_SIZE, 'thumb.jpg'),
            $urls
        );
    }

    public function testGetAllActiveDetailReturnsEmptyForNonExistentProduct(): void
    {
        $results = $this->get(ProductMediaViewServiceInterface::class)
            ->getAllByRole(Id::generate(), ProductMediaRole::from(ProductMediaRole::DETAIL));

        $this->assertEmpty($results);
    }

    public function testGetAllByRoleReturnsAltTextForEachMedia(): void
    {
        $productId = Id::generate();
        $media1 = $this->addProductMediaAndReturn(
            $productId,
            Path::join('out', 'pictures', 'media', 'batch-a.jpg'),
            1,
            ProductMediaRole::DETAIL
        );
        $media2 = $this->addProductMediaAndReturn(
            $productId,
            Path::join('out', 'pictures', 'media', 'batch-b.jpg'),
            2,
            ProductMediaRole::DETAIL
        );
        $this->setAltText($media1->getMedia(), 'alt for a');
        $this->setAltText($media2->getMedia(), 'alt for b');

        $results = $this->get(ProductMediaViewServiceInterface::class)
            ->getAllByRole($productId, ProductMediaRole::from(ProductMediaRole::DETAIL));

        $this->assertSame('alt for a', $results[(string) $media1->getMedia()->getId()]->getAttributes()->getAlt());
        $this->assertSame('alt for b', $results[(string) $media2->getMedia()->getId()]->getAttributes()->getAlt());
    }

    public function testGetThumbnailUrl(): void
    {
        $result = $this->get(ProductMediaViewServiceInterface::class)
            ->getByRole($this->productId, ProductMediaRole::from(ProductMediaRole::ICON));

        $this->assertEquals(
            $this->expectedUrlFor(self::CONFIG_KEY_THUMBNAIL_SIZE, 'icon.jpg'),
            $result->getThumbnailUrl()
        );
    }

    public function testFallbackHasThumbnailUrl(): void
    {
        $result = $this->get(ProductMediaViewServiceInterface::class)
            ->getByRole(Id::generate(), ProductMediaRole::from(ProductMediaRole::ICON));

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
            ProductMediaRole::DETAIL
        );

        $iconResult = $this->get(ProductMediaViewServiceInterface::class)
            ->getByRole($otherProductId, ProductMediaRole::from(ProductMediaRole::ICON));

        $this->assertEquals(
            $this->expectedUrlFor(self::CONFIG_KEY_DETAIL_IMAGE_SIZE, 'fallback.jpg'),
            $iconResult->getDetailUrl()
        );
        $this->assertFalse($iconResult->isFallback());
    }

    public function testGetByRoleReturnsAltTextWhenSet(): void
    {
        $productId = Id::generate();
        $productMedia = $this->addProductMediaAndReturn(
            $productId,
            Path::join('out', 'pictures', 'media', 'alt-test.jpg'),
            1,
            ProductMediaRole::DETAIL
        );
        $this->setAltText($productMedia->getMedia(), 'my alt text');

        $result = $this->get(ProductMediaViewServiceInterface::class)
            ->getByRole($productId, ProductMediaRole::from(ProductMediaRole::DETAIL));

        $this->assertSame('my alt text', $result->getAttributes()->getAlt());
    }

    public function testGetByRoleReturnsNoAltTextWhenNotSet(): void
    {
        $result = $this->get(ProductMediaViewServiceInterface::class)
            ->getByRole($this->productId, ProductMediaRole::from(ProductMediaRole::ICON));

        $this->assertFalse($result->getAttributes()->has(MediaAttribute::ALT));
    }

    public function testGetByPositionReturnsAltTextWhenSet(): void
    {
        $productId = Id::generate();
        $productMedia = $this->addProductMediaAndReturn(
            $productId,
            Path::join('out', 'pictures', 'media', 'pos-alt-test.jpg'),
            1,
            ProductMediaRole::DETAIL
        );
        $this->setAltText($productMedia->getMedia(), 'position alt text');

        $result = $this->get(ProductMediaViewServiceInterface::class)
            ->getByPosition($productId, 1);

        $this->assertSame('position alt text', $result->getAttributes()->getAlt());
    }

    public function testGetByPositionReturnsNoAltTextWhenNotSet(): void
    {
        $result = $this->get(ProductMediaViewServiceInterface::class)
            ->getByPosition($this->productId, 1);

        $this->assertFalse($result->getAttributes()->has(MediaAttribute::ALT));
    }

    public function testGetAllByRoleReturnsNoAltTextWhenNotSet(): void
    {
        $productId = Id::generate();
        $this->addProductMediaAndReturn(
            $productId,
            Path::join('out', 'pictures', 'media', 'no-alt.jpg'),
            1,
            ProductMediaRole::DETAIL
        );

        $results = $this->get(ProductMediaViewServiceInterface::class)
            ->getAllByRole($productId, ProductMediaRole::from(ProductMediaRole::DETAIL));

        $this->assertFalse(reset($results)->getAttributes()->has(MediaAttribute::ALT));
    }

    private function setupTestData(): void
    {
        $this->addProductMedia(
            $this->productId,
            Path::join('out', 'pictures', 'media', 'icon.jpg'),
            0,
            ProductMediaRole::ICON
        );
        $this->addProductMedia(
            $this->productId,
            Path::join('out', 'pictures', 'media', 'thumb.jpg'),
            0,
            ProductMediaRole::THUMBNAIL
        );
        $this->addProductMedia(
            $this->productId,
            Path::join('out', 'pictures', 'media', 'detail.jpg'),
            1,
            ProductMediaRole::DETAIL
        );
        $this->addProductMedia(
            $this->productId,
            Path::join('out', 'pictures', 'media', 'detail-2.jpg'),
            2,
            ProductMediaRole::DETAIL
        );
        $this->addProductMedia(
            $this->productId,
            Path::join('out', 'pictures', 'media', 'detail-3.jpg'),
            3,
            ProductMediaRole::DETAIL
        );
    }

    private function addProductMedia(Id $productId, string $path, int $position, string $role): void
    {
        $this->addProductMediaAndReturn($productId, $path, $position, $role);
    }

    private function addProductMediaAndReturn(Id $productId, string $path, int $position, string $role): ProductMedia
    {
        $media = new Media(Id::generate(), new MediaPath($path), new MediaType('image/jpeg'));
        $roleSet = new ProductMediaRoleSet(ProductMediaRole::from($role));
        $productMedia = new ProductMedia(Id::generate(), $productId, $media, $roleSet);
        $productMedia->setPosition($position);
        $this->get(ProductMediaServiceInterface::class)->add($productMedia);
        return $productMedia;
    }

    private function setAltText(Media $media, string $altText): void
    {
        $locale = $this->get(ActiveLocaleProviderInterface::class)->getActiveLocale();
        $this->get(MediaAttributeServiceInterface::class)->save(MediaAttribute::ALT, $altText, $media, $locale->getCode());
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
        $value = (string) $this->get(ThemeSettingDaoInterface::class)
            ->get($key, $this->shopId, $this->themeId)->getValue();
        [$width, $height] = explode('*', $value);
        return ['width' => (int) $width, 'height' => (int) $height];
    }

    private function getQualityFromConfig(): string
    {
        return (string) $this->get(ShopConfigurationSettingDaoInterface::class)
            ->get(self::CONFIG_KEY_DEFAULT_IMAGE_QUALITY, $this->shopId)->getValue();
    }

    private function setThemeStringConfigValue(string $name, string $value): void
    {
        $setting = new ThemeSetting();
        $setting->setName($name);
        $setting->setValue($value);
        $setting->setType(ShopSettingType::STRING);
        $setting->setShopId($this->shopId);
        $setting->setThemeId($this->themeId);
        $this->get(ThemeSettingDaoInterface::class)->save($setting);
    }

    private function setStringConfigValue(string $name, string $value): void
    {
        $setting = new ShopConfigurationSetting();
        $setting->setName($name);
        $setting->setValue($value);
        $setting->setType(ShopSettingType::STRING);
        $setting->setShopId($this->shopId);
        $this->get(ShopConfigurationSettingDaoInterface::class)->save($setting);
    }

    private function setBooleanConfigValue(string $name, bool $value): void
    {
        $setting = new ShopConfigurationSetting();
        $setting->setName($name);
        $setting->setValue($value);
        $setting->setType(ShopSettingType::BOOLEAN);
        $setting->setShopId($this->shopId);
        $this->get(ShopConfigurationSettingDaoInterface::class)->save($setting);
    }
}
