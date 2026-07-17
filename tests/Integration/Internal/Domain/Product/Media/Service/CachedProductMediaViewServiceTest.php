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
use OxidEsales\EshopCommunity\Internal\Domain\Media\Service\MediaAttributeServiceInterface;
use OxidEsales\EshopCommunity\Internal\Domain\Product\Media\Dao\ProductMediaDaoInterface;
use OxidEsales\EshopCommunity\Internal\Domain\Product\Media\DataObject\ProductMedia;
use OxidEsales\EshopCommunity\Internal\Domain\Product\Media\DataObject\ProductMediaRole;
use OxidEsales\EshopCommunity\Internal\Domain\Product\Media\DataObject\ProductMediaRoleSet;
use OxidEsales\EshopCommunity\Internal\Domain\Product\Media\Service\ProductMediaServiceInterface;
use OxidEsales\EshopCommunity\Internal\Domain\Product\Media\Service\ProductMediaViewServiceInterface;
use OxidEsales\EshopCommunity\Internal\Domain\Product\Media\Service\ProductVariantMediaServiceInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Config\Dao\ShopConfigurationSettingDaoInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Config\DataObject\ShopConfigurationSetting;
use OxidEsales\EshopCommunity\Internal\Framework\Config\DataObject\ShopSettingType;
use OxidEsales\EshopCommunity\Internal\Framework\Database\Id;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\Configuration\Dao\ThemeConfigurationDaoInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\Configuration\DataObject\ThemeConfiguration;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\Setting\Setting;
use OxidEsales\EshopCommunity\Internal\Transition\Utility\ContextInterface;
use OxidEsales\EshopCommunity\Tests\Integration\IntegrationTestCase;
use Symfony\Component\Filesystem\Path;

final class CachedProductMediaViewServiceTest extends IntegrationTestCase
{
    private ProductMediaViewServiceInterface $viewService;
    private ProductMediaServiceInterface $productMediaService;
    private MediaAttributeServiceInterface $mediaAttributeService;
    private int $shopId;
    private string $themeId;

    public function setUp(): void
    {
        parent::setUp();

        $this->shopId = $this->get(ContextInterface::class)->getCurrentShopId();
        $this->themeId = $this->activateTestTheme($this->shopId);
        $this->viewService = $this->get(ProductMediaViewServiceInterface::class);
        $this->productMediaService = $this->get(ProductMediaServiceInterface::class);
        $this->mediaAttributeService = $this->get(MediaAttributeServiceInterface::class);

        $this->configureImageSettings();
    }

    public function testReturnsCachedViewWhenMediaChangesWithoutNotification(): void
    {
        $productId = Id::generate();
        $productMedia = $this->addProductMediaAndReturn($productId, 'stale.jpg');
        $firstView = $this->viewService->getByRole($productId, $this->detailRole());
        $this->assertFalse($firstView->isFallback());

        $this->get(ProductMediaDaoInterface::class)->delete($productMedia->getId());
        $secondView = $this->viewService->getByRole($productId, $this->detailRole());

        $this->assertFalse($secondView->isFallback());
        $this->assertSame($firstView->getDetailUrl(), $secondView->getDetailUrl());
    }

    public function testRemoveRefreshesView(): void
    {
        $productId = Id::generate();
        $productMedia = $this->addProductMediaAndReturn($productId, 'removed.jpg');
        $firstView = $this->viewService->getByRole($productId, $this->detailRole());
        $this->assertFalse($firstView->isFallback());

        $this->productMediaService->remove($productMedia);
        $refreshedView = $this->viewService->getByRole($productId, $this->detailRole());

        $this->assertTrue($refreshedView->isFallback());
    }

    public function testDeactivateRefreshesView(): void
    {
        $productId = Id::generate();
        $productMedia = $this->addProductMediaAndReturn($productId, 'deactivated.jpg');
        $firstView = $this->viewService->getByRole($productId, $this->detailRole());
        $this->assertFalse($firstView->isFallback());

        $this->productMediaService->deactivate($productMedia);
        $refreshedView = $this->viewService->getByRole($productId, $this->detailRole());

        $this->assertTrue($refreshedView->isFallback());
    }

    public function testSavingAttributeRefreshesView(): void
    {
        $productId = Id::generate();
        $productMedia = $this->addProductMediaAndReturn($productId, 'alt-saved.jpg');
        $firstView = $this->viewService->getByRole($productId, $this->detailRole());
        $this->assertFalse($firstView->getAttributes()->has(MediaAttribute::ALT));

        $this->mediaAttributeService->save(
            MediaAttribute::ALT,
            'fresh alt',
            $productMedia->getMedia(),
            $this->getActiveLocaleCode()
        );
        $refreshedView = $this->viewService->getByRole($productId, $this->detailRole());

        $this->assertSame('fresh alt', $refreshedView->getAttributes()->getAlt());
    }

    public function testDeletingAttributeRefreshesView(): void
    {
        $productId = Id::generate();
        $productMedia = $this->addProductMediaAndReturn($productId, 'alt-deleted.jpg');
        $this->mediaAttributeService->save(
            MediaAttribute::ALT,
            'old alt',
            $productMedia->getMedia(),
            $this->getActiveLocaleCode()
        );
        $firstView = $this->viewService->getByRole($productId, $this->detailRole());
        $this->assertSame('old alt', $firstView->getAttributes()->getAlt());

        $this->mediaAttributeService->delete(
            MediaAttribute::ALT,
            $productMedia->getMedia(),
            $this->getActiveLocaleCode()
        );
        $refreshedView = $this->viewService->getByRole($productId, $this->detailRole());

        $this->assertFalse($refreshedView->getAttributes()->has(MediaAttribute::ALT));
    }

    public function testAddRefreshesFallbackView(): void
    {
        $productId = Id::generate();
        $fallbackView = $this->viewService->getByRole($productId, $this->detailRole());
        $this->assertTrue($fallbackView->isFallback());

        $this->addProductMediaAndReturn($productId, 'first-upload.jpg');
        $refreshedView = $this->viewService->getByRole($productId, $this->detailRole());

        $this->assertFalse($refreshedView->isFallback());
    }

    public function testAddRefreshesEmptyViewCollection(): void
    {
        $productId = Id::generate();
        $this->assertCount(0, $this->viewService->getAllByRole($productId, $this->detailRole()));

        $this->addProductMediaAndReturn($productId, 'collection-new.jpg');
        $refreshedViews = $this->viewService->getAllByRole($productId, $this->detailRole());

        $this->assertCount(1, $refreshedViews);
    }

    public function testSortRefreshesViewPositions(): void
    {
        $productId = Id::generate();
        $firstMedia = $this->addProductMediaAndReturn($productId, 'sort-a.jpg', 0);
        $secondMedia = $this->addProductMediaAndReturn($productId, 'sort-b.jpg', 1);
        $initialView = $this->viewService->getByPosition($productId, 0);

        $this->productMediaService->sort($productId, [
            (string) $secondMedia->getId(),
            (string) $firstMedia->getId(),
        ]);
        $resortedView = $this->viewService->getByPosition($productId, 0);

        $this->assertNotSame($initialView->getDetailUrl(), $resortedView->getDetailUrl());
    }

    public function testAssignFromParentToVariantRefreshesVariantView(): void
    {
        $parentProductId = Id::generate();
        $variantProductId = Id::generate();
        $this->addProductMediaAndReturn($parentProductId, 'parent.jpg');
        $fallbackView = $this->viewService->getByRole($variantProductId, $this->detailRole());
        $this->assertTrue($fallbackView->isFallback());

        $this->get(ProductVariantMediaServiceInterface::class)
            ->assignFromParentToVariant($parentProductId, $variantProductId);
        $refreshedView = $this->viewService->getByRole($variantProductId, $this->detailRole());

        $this->assertFalse($refreshedView->isFallback());
    }

    public function testRemoveRefreshesViewCollection(): void
    {
        $productId = Id::generate();
        $removedProductMedia = $this->addProductMediaAndReturn($productId, 'collection-a.jpg', 1);
        $this->addProductMediaAndReturn($productId, 'collection-b.jpg', 2);
        $firstViews = $this->viewService->getAllByRole($productId, $this->detailRole());
        $this->assertCount(2, $firstViews);

        $this->productMediaService->remove($removedProductMedia);
        $refreshedViews = $this->viewService->getAllByRole($productId, $this->detailRole());

        $this->assertCount(1, $refreshedViews);
        $this->assertArrayNotHasKey(
            (string) $removedProductMedia->getMedia()->getId(),
            $refreshedViews
        );
    }

    private function activateTestTheme(int $shopId): string
    {
        $themeId = 'testTheme';
        $this->get(ThemeConfigurationDaoInterface::class)->save(
            (new ThemeConfiguration())->setId($themeId)->setSource('testSourcePath')->setActivated(true),
            $shopId
        );

        return $themeId;
    }

    private function detailRole(): ProductMediaRole
    {
        return ProductMediaRole::from(ProductMediaRole::DETAIL);
    }

    private function getActiveLocaleCode(): string
    {
        return $this->get(ActiveLocaleProviderInterface::class)->getActiveLocale()->getCode();
    }

    private function addProductMediaAndReturn(Id $productId, string $filename, int $position = 1): ProductMedia
    {
        $media = new Media(
            Id::generate(),
            new MediaPath(Path::join('out', 'pictures', 'media', $filename)),
            new MediaType('image/jpeg')
        );
        $productMedia = new ProductMedia(
            Id::generate(),
            $productId,
            $media,
            new ProductMediaRoleSet(ProductMediaRole::from(ProductMediaRole::DETAIL))
        );
        $productMedia->setPosition($position);
        $this->productMediaService->add($productMedia);

        return $productMedia;
    }

    private function configureImageSettings(): void
    {
        $configuration = new ThemeConfiguration();
        $configuration->setId($this->themeId)->setSource('testSourcePath')->setActivated(true);

        foreach ([
            'sIconsize' => '87*87',
            'sThumbnailsize' => '200*200',
            'sDetailImageSize' => '600*600',
            'sZoomImageSize' => '1200*1200',
        ] as $name => $value) {
            $setting = new Setting();
            $setting->setName($name)->setValue($value);
            $configuration->addThemeSetting($setting);
        }

        $this->get(ThemeConfigurationDaoInterface::class)->save($configuration, $this->shopId);

        $this->setStringConfigValue('sDefaultImageQuality', '75');
        $this->setBooleanConfigValue('blConvertImagesToWebP', false);
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
