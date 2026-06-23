<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Integration\Internal\Domain\Product\Media\Service;

use OxidEsales\EshopCommunity\Internal\Domain\Media\DataObject\Media;
use OxidEsales\EshopCommunity\Internal\Domain\Media\DataObject\MediaPath;
use OxidEsales\EshopCommunity\Internal\Domain\Media\DataObject\MediaType;
use OxidEsales\EshopCommunity\Internal\Domain\Product\Media\DataObject\ProductMedia;
use OxidEsales\EshopCommunity\Internal\Domain\Product\Media\DataObject\ProductMediaRole;
use OxidEsales\EshopCommunity\Internal\Domain\Product\Media\DataObject\ProductMediaRoleSet;
use OxidEsales\EshopCommunity\Internal\Domain\Product\Media\Service\ProductMediaServiceInterface;
use OxidEsales\EshopCommunity\Internal\Domain\Product\Media\Service\ProductMediaViewServiceInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Config\Dao\ShopConfigurationSettingDaoInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Config\DataObject\ShopConfigurationSetting;
use OxidEsales\EshopCommunity\Internal\Framework\Config\DataObject\ShopSettingType;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\Configuration\Dao\ThemeConfigurationDaoInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\Configuration\DataObject\ThemeConfiguration;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\Configuration\DataObject\ThemeConfiguration\Setting;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\Facade\ActiveThemeServiceInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\Setup\Bridge\ThemeActivationBridgeInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\State\ThemeStateServiceInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Database\Id;
use OxidEsales\EshopCommunity\Internal\Transition\Utility\ContextInterface;
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

    private const THEME_SOURCE = 'tests/Integration/Internal/Domain/Product/Media/Service/Fixtures/oe_media_test_theme';

    private Id $productId;
    private string $baseUrl;
    private int $shopId;
    private string $themeId;
    private string $originalThemeId;

    public function setUp(): void
    {
        parent::setUp();

        $context = $this->get(ContextInterface::class);
        $this->baseUrl = $context->getShopBaseUrl();
        $this->shopId = $context->getCurrentShopId();
        $this->themeId = 'oe_media_test_theme';
        $this->originalThemeId = $this->get(ThemeStateServiceInterface::class)->getActiveThemeId($this->shopId);
        $this->installAndActivateTheme();

        $this->productId = Id::generate();
        $this->configureImageSettings();
        $this->setupTestData();
    }

    public function tearDown(): void
    {
        if ($this->originalThemeId !== '') {
            $this->get(ThemeActivationBridgeInterface::class)->activate($this->originalThemeId, $this->shopId);
        }
        $this->get(ThemeConfigurationDaoInterface::class)->delete($this->themeId, $this->shopId);

        parent::tearDown();
    }

    public function testGetIconReturnsMediaViewWithAllUrls(): void
    {
        $result = $this->get(ProductMediaViewServiceInterface::class)->getByRole($this->productId, ProductMediaRole::from(ProductMediaRole::ICON));

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
        $result = $this->get(ProductMediaViewServiceInterface::class)->getByRole(Id::generate(), ProductMediaRole::from(ProductMediaRole::ICON));

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
        $result = $this->get(ProductMediaViewServiceInterface::class)->getByRole($this->productId, ProductMediaRole::from(ProductMediaRole::THUMBNAIL));

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

        $result = $this->get(ProductMediaViewServiceInterface::class)->getByRole($this->productId, ProductMediaRole::from(ProductMediaRole::ICON));
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

        $result = $this->get(ProductMediaViewServiceInterface::class)->getByRole($otherProductId, ProductMediaRole::from(ProductMediaRole::ICON));
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
        $result = $this->get(ProductMediaViewServiceInterface::class)->getByRole(Id::generate(), ProductMediaRole::from(ProductMediaRole::ICON));
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
        $results = $this->get(ProductMediaViewServiceInterface::class)->getAllByRole(Id::generate(), ProductMediaRole::from(ProductMediaRole::DETAIL));

        $this->assertEmpty($results);
    }

    public function testGetThumbnailUrl(): void
    {
        $result = $this->get(ProductMediaViewServiceInterface::class)->getByRole($this->productId, ProductMediaRole::from(ProductMediaRole::ICON));

        $this->assertEquals(
            $this->expectedUrlFor(self::CONFIG_KEY_THUMBNAIL_SIZE, 'icon.jpg'),
            $result->getThumbnailUrl()
        );
    }

    public function testFallbackHasThumbnailUrl(): void
    {
        $result = $this->get(ProductMediaViewServiceInterface::class)->getByRole(Id::generate(), ProductMediaRole::from(ProductMediaRole::ICON));

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

        $iconResult = $this->get(ProductMediaViewServiceInterface::class)->getByRole($otherProductId, ProductMediaRole::from(ProductMediaRole::ICON));

        $this->assertEquals(
            $this->expectedUrlFor(self::CONFIG_KEY_DETAIL_IMAGE_SIZE, 'fallback.jpg'),
            $iconResult->getDetailUrl()
        );
        $this->assertFalse($iconResult->isFallback());
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
        $media = new Media(Id::generate(), new MediaPath($path), new MediaType('image/jpeg'));
        $roleSet = new ProductMediaRoleSet(ProductMediaRole::from($role));
        $productMedia = new ProductMedia(Id::generate(), $productId, $media, $roleSet);
        $productMedia->setPosition($position);
        $this->get(ProductMediaServiceInterface::class)->add($productMedia);
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
        $value = (string) $this->get(ActiveThemeServiceInterface::class)->getSettingValue($key);
        [$width, $height] = explode('*', $value);
        return ['width' => (int) $width, 'height' => (int) $height];
    }

    private function getQualityFromConfig(): string
    {
        return (string) $this->get(ShopConfigurationSettingDaoInterface::class)->get(self::CONFIG_KEY_DEFAULT_IMAGE_QUALITY, $this->shopId)->getValue();
    }

    private function installAndActivateTheme(): void
    {
        $this->get(ThemeConfigurationDaoInterface::class)->save(
            (new ThemeConfiguration())->setId($this->themeId)->setThemeSource(self::THEME_SOURCE),
            $this->shopId
        );
        $this->get(ThemeActivationBridgeInterface::class)->activate($this->themeId, $this->shopId);
    }

    private function setThemeStringConfigValue(string $name, string $value): void
    {
        $dao = $this->get(ThemeConfigurationDaoInterface::class);
        $configuration = $dao->get($this->themeId, $this->shopId);

        if ($configuration->hasSetting($name)) {
            $configuration->getSetting($name)->setType(ShopSettingType::STRING)->setValue($value);
        } else {
            $configuration->addSetting(
                (new Setting())->setName($name)->setType(ShopSettingType::STRING)->setValue($value)
            );
        }

        $dao->save($configuration, $this->shopId);
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
