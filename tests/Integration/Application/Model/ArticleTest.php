<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Integration\Application\Model;

use DateTimeImmutable;
use OxidEsales\Eshop\Application\Model\Article;
use OxidEsales\Eshop\Core\Field;
use OxidEsales\Eshop\Core\Registry;
use OxidEsales\EshopCommunity\Core\Di\ContainerFacade;
use OxidEsales\EshopCommunity\Internal\Domain\Media\DataObject\Media;
use OxidEsales\EshopCommunity\Internal\Domain\Media\DataObject\MediaAttribute;
use OxidEsales\EshopCommunity\Internal\Domain\Media\DataObject\MediaPath;
use OxidEsales\EshopCommunity\Internal\Domain\Media\DataObject\MediaType;
use OxidEsales\EshopCommunity\Internal\Domain\Product\Media\DataObject\ProductMedia;
use OxidEsales\EshopCommunity\Internal\Domain\Product\Media\DataObject\ProductMediaRole;
use OxidEsales\EshopCommunity\Internal\Domain\Product\Media\DataObject\ProductMediaRoleSet;
use OxidEsales\EshopCommunity\Internal\Domain\Locale\Service\ActiveLocaleProviderInterface;
use OxidEsales\EshopCommunity\Internal\Domain\Media\Service\MediaAttributeServiceInterface;
use OxidEsales\EshopCommunity\Internal\Domain\Product\Media\Service\ProductMediaServiceInterface;
use OxidEsales\EshopCommunity\Internal\Domain\Product\Media\Service\ProductMediaViewServiceInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Config\Dao\ShopConfigurationSettingDaoInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Config\DataObject\ShopConfigurationSetting;
use OxidEsales\EshopCommunity\Internal\Framework\Config\DataObject\ShopSettingType;
use OxidEsales\EshopCommunity\Internal\Framework\Database\Id;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\Configuration\Dao\ThemeConfigurationDaoInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\Configuration\DataObject\ThemeConfiguration;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\Setting\Setting;
use OxidEsales\EshopCommunity\Internal\Transition\Utility\ContextInterface;
use OxidEsales\EshopCommunity\Tests\Integration\IntegrationTestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use Symfony\Component\Filesystem\Path;

final class ArticleTest extends IntegrationTestCase
{
    private static string $timeFormat = 'Y-m-d H:i:s';
    private static string $defaultTimestamp = '0000-00-00 00:00:00';

    private ProductMediaServiceInterface $productMediaService;
    private ProductMediaViewServiceInterface $productMediaViewService;

    public function setUp(): void
    {
        parent::setUp();

        Registry::getConfig()->init();
        $this->configureImageSettings();
        Registry::getConfig()->setConfigParam('blUseStock', false);

        $this->productMediaService = ContainerFacade::get(ProductMediaServiceInterface::class);
        $this->productMediaViewService = ContainerFacade::get(ProductMediaViewServiceInterface::class);
    }

    private function configureImageSettings(): void
    {
        $shopId = $this->get(ContextInterface::class)->getCurrentShopId();
        $dao = $this->get(ShopConfigurationSettingDaoInterface::class);

        $configuration = (new ThemeConfiguration())
            ->setId('testTheme')
            ->setActivated(true)
            ->addThemeSetting((new Setting())->setName('sIconsize')->setType('str')->setValue('87*87'))
            ->addThemeSetting((new Setting())->setName('sThumbnailsize')->setType('str')->setValue('200*200'))
            ->addThemeSetting((new Setting())->setName('sDetailImageSize')->setType('str')->setValue('600*600'))
            ->addThemeSetting((new Setting())->setName('sZoomImageSize')->setType('str')->setValue('1200*1200'));

        $this->get(ThemeConfigurationDaoInterface::class)->save($configuration, $shopId);

        $qualitySetting = new ShopConfigurationSetting();
        $qualitySetting->setName('sDefaultImageQuality');
        $qualitySetting->setValue('75');
        $qualitySetting->setType(ShopSettingType::STRING);
        $qualitySetting->setShopId($shopId);
        $dao->save($qualitySetting);

        $webpSetting = new ShopConfigurationSetting();
        $webpSetting->setName('blConvertImagesToWebP');
        $webpSetting->setValue(false);
        $webpSetting->setType(ShopSettingType::BOOLEAN);
        $webpSetting->setShopId($shopId);
        $dao->save($webpSetting);
    }

    public function testIsVisibleWithInactive(): void
    {
        $product = oxNew(Article::class);
        $product->oxarticles__oxactive = new Field(false);

        $this->assertFalse($product->isVisible());
    }

    public function testIsVisibleWithAlwaysActive(): void
    {
        $product = oxNew(Article::class);
        $product->oxarticles__oxactive = new Field(true);

        $this->assertTrue($product->isVisible());
    }

    public function testIsVisibleWithValidTimeRestrictionsAndDisabledConfig(): void
    {
        Registry::getConfig()->setConfigParam('blUseTimeCheck', false);
        $now = new DateTimeImmutable();
        $past = $now->modify('-1 day');
        $future = $now->modify('+1 day');
        $product = oxNew(Article::class);
        $product->oxarticles__oxactive = new Field(false);
        $product->oxarticles__oxactivefrom = new Field($past->format(self::$timeFormat));
        $product->oxarticles__oxactiveto = new Field($future->format(self::$timeFormat));

        $this->assertFalse($product->isVisible());
    }

    #[DataProvider('validTimeRestrictionsDataProvider')]
    public function testIsVisibleWithValidTimeRestrictions(string $activeFrom, string $activeTo): void
    {
        Registry::getConfig()->setConfigParam('blUseTimeCheck', true);

        $product = oxNew(Article::class);
        $product->oxarticles__oxactive = new Field(false);
        $product->oxarticles__oxactivefrom = new Field($activeFrom);
        $product->oxarticles__oxactiveto = new Field($activeTo);

        $this->assertTrue($product->isVisible());
    }

    public static function validTimeRestrictionsDataProvider(): array
    {
        $now = new DateTimeImmutable();
        $past = $now->modify('-1 day');
        $future = $now->modify('+1 day');

        return [
            [$past->format(self::$timeFormat), $future->format(self::$timeFormat)],
            [self::$defaultTimestamp, $future->format(self::$timeFormat)],
            [$now->format(self::$timeFormat), $future->format(self::$timeFormat)]
        ];
    }

    #[DataProvider('invalidTimeRestrictionsDataProvider')]
    public function testIsVisibleWithInvalidTimeRestrictions(string $activeFrom, string $activeTo): void
    {
        Registry::getConfig()->setConfigParam('blUseTimeCheck', true);

        $product = oxNew(Article::class);
        $product->oxarticles__oxactive = new Field(false);
        $product->oxarticles__oxactivefrom = new Field($activeFrom);
        $product->oxarticles__oxactiveto = new Field($activeTo);

        $this->assertFalse($product->isVisible());
    }

    public static function invalidTimeRestrictionsDataProvider(): array
    {
        $now = new DateTimeImmutable();
        $past = $now->modify('-1 day');
        $future = $now->modify('+1 day');

        return [
            [self::$defaultTimestamp, self::$defaultTimestamp],
            [$future->format(self::$timeFormat), self::$defaultTimestamp],
            [$future->format(self::$timeFormat), $past->format(self::$timeFormat)],
            [self::$defaultTimestamp, $past->format(self::$timeFormat)]
        ];
    }

    #[DataProvider('productActiveFieldStatesDataProvider')]
    public function testIsProductAlwaysActive(?bool $active, bool $result): void
    {
        $product = oxNew(Article::class);
        $product->oxarticles__oxactive = new Field($active);

        $this->assertEquals($result, $product->isProductAlwaysActive());
    }

    public static function productActiveFieldStatesDataProvider(): array
    {
        return [
            'NULL value' => [null, false],
            'false value' => [false, false],
            'true value' => [true, true],
        ];
    }

    #[DataProvider('validityTimeRangesDataProvider')]
    public function testHasProductValidTimeRange(string $activeFrom, string $activeTo, bool $result): void
    {
        $product = oxNew(Article::class);
        $product->oxarticles__oxactivefrom = new Field($activeFrom);
        $product->oxarticles__oxactiveto = new Field($activeTo);

        $this->assertEquals($result, $product->hasProductValidTimeRange());
    }

    public static function validityTimeRangesDataProvider(): array
    {
        $now = new DateTimeImmutable();
        return [
            'Empty active From/To' => [self::$defaultTimestamp, self::$defaultTimestamp, false],
            'Empty active From' => [self::$defaultTimestamp, $now->format(self::$timeFormat), true],
            'Empty active To' => [$now->format(self::$timeFormat), self::$defaultTimestamp, true],
            'With active From/to' => [$now->format(self::$timeFormat), $now->format(self::$timeFormat), true],
        ];
    }

    #[DataProvider('visibilityTimeRangesDataProvider')]
    public function testIsProductActive(string $activeFrom, string $activeTo, bool $result): void
    {
        $product = oxNew(Article::class);
        $product->oxarticles__oxactivefrom = new Field($activeFrom);
        $product->oxarticles__oxactiveto = new Field($activeTo);

        $this->assertEquals($result, $product->hasActiveTimeRange());
    }

    public static function visibilityTimeRangesDataProvider(): array
    {
        $now = new DateTimeImmutable();
        $past = $now->modify('-1 day')->format(self::$timeFormat);
        $future = $now->modify('+1 day')->format(self::$timeFormat);
        return [
            'Empty active From/To' => [self::$defaultTimestamp, self::$defaultTimestamp, false],
            'Empty activeFrom valid activeTo' => [self::$defaultTimestamp, $future, true],
            'Empty activeFrom invalid activeTo' => [self::$defaultTimestamp, $past, false],
            'Empty activeTo valid activeFrom' => [$past, self::$defaultTimestamp, true],
            'Empty activeTo invalid activeFrom' => [$future, self::$defaultTimestamp, false],
            'With valid From/to' => [$past, $future, true],
            'With invalid From/to' => [$future, $past, false],
        ];
    }

    public static function productLowStockDataProvider(): array
    {
        return [
            'Product in low stock: Shop limit reached, Product limit undefined' => [5, 0.0, 10, false],
            'Product in low stock: Shop limit exceeded, Product limit ignored' => [11, 20.0, 10, true],
            'Product in low stock: Product limit reached' => [5, 10.0, 0, true]
        ];
    }

    #[DataProvider('productLowStockDataProvider')]
    public function testProductLowStock(
        int $productStock,
        float $productLowStockLimit,
        int $shopLowStockLimit,
        bool $productLowStockActive
    ): void {
        Registry::getConfig()->setConfigParam('blUseStock', true);
        Registry::getConfig()->setConfigParam('sStockWarningLimit', $shopLowStockLimit);

        $product = oxNew(Article::class);
        $product->assign([
            'oxarticles__oxstock' => $productStock,
            'oxarticles__oxremindamount' => $productLowStockLimit,
            'oxarticles__oxlowstockactive' => $productLowStockActive,
            'oxarticles__oxparentid' => '',
            'oxarticles__oxstockflag' => 1,
            'oxarticles__oxshopid' => 1,
            'oxarticles__oxvarstock' => $productStock,
            'oxarticles__oxvarcount' => 0
        ]);

        $this->assertEquals(1, $product->getStockStatus());
    }

    public function testProductInStock(): void
    {
        Registry::getConfig()->setConfigParam('blUseStock', true);
        Registry::getConfig()->setConfigParam('sStockWarningLimit', 3);

        $product = oxNew(Article::class);
        $product->assign([
            'oxarticles__oxstock' => 5,
            'oxarticles__oxremindamount' => 0.0,
            'oxarticles__oxlowstockactive' => false,
            'oxarticles__oxparentid' => '',
            'oxarticles__oxstockflag' => 1,
            'oxarticles__oxshopid' => 1,
            'oxarticles__oxvarstock' => 5,
            'oxarticles__oxvarcount' => 0
        ]);

        $this->assertEquals(0, $product->getStockStatus());
    }

    public function testProductOutStock(): void
    {
        Registry::getConfig()->setConfigParam('blUseStock', true);
        Registry::getConfig()->setConfigParam('sStockWarningLimit', 0);

        $product = oxNew(Article::class);
        $product->assign([
            'oxarticles__oxstock' => -1,
            'oxarticles__oxremindamount' => 0.0,
            'oxarticles__oxlowstockactive' => false,
            'oxarticles__oxparentid' => '',
            'oxarticles__oxstockflag' => 1,
            'oxarticles__oxshopid' => 1,
            'oxarticles__oxvarstock' => -1,
            'oxarticles__oxvarcount' => 0
        ]);

        $this->assertEquals(-1, $product->getStockStatus());
    }

    public function testGetIconReturnsMediaViewFromService(): void
    {
        [$article, $productId] = $this->createArticleWithMedia();

        $expectedUrl = $this->productMediaViewService->getByRole(
            $productId,
            ProductMediaRole::from(ProductMediaRole::ICON)
        )->getDetailUrl();

        $this->assertSame($expectedUrl, $article->getIcon()->getDetailUrl());
    }

    public function testGetThumbnailReturnsMediaViewFromService(): void
    {
        [$article, $productId] = $this->createArticleWithMedia();

        $expectedUrl = $this->productMediaViewService->getByRole(
            $productId,
            ProductMediaRole::from(ProductMediaRole::THUMBNAIL)
        )->getDetailUrl();

        $this->assertSame($expectedUrl, $article->getThumbnail()->getDetailUrl());
    }

    public function testGetMediaReturnsDetailImageForRequestedPosition(): void
    {
        [$article, $productId] = $this->createArticleWithMedia();

        $expectedUrl = $this->productMediaViewService->getByPosition($productId, 1)->getDetailUrl();

        $this->assertSame($expectedUrl, $article->getMedia(1)->getDetailUrl());
    }


    private function createArticleWithMedia(): array
    {
        $productId = Id::generate();
        $this->createPersistedArticle($productId);
        $this->addProductMedia($productId, 'article-icon.jpg', 0, ProductMediaRole::ICON);
        $this->addProductMedia($productId, 'article-thumb.jpg', 0, ProductMediaRole::THUMBNAIL);
        $this->addProductMedia($productId, 'article-detail.jpg', 1, ProductMediaRole::DETAIL);
        $this->addProductMedia($productId, 'article-detail-2.jpg', 2, ProductMediaRole::DETAIL);

        $article = oxNew(Article::class);
        $article->load((string) $productId);

        return [$article, $productId];
    }

    private function createPersistedArticle(Id $productId): void
    {
        $article = oxNew(Article::class);
        $article->setId((string) $productId);
        $article->oxarticles__oxshopid = new Field((string) Registry::getConfig()->getShopId());
        $article->oxarticles__oxactive = new Field(true);
        $article->oxarticles__oxtitle = new Field('Article with media');
        $article->oxarticles__oxprice = new Field(12.5);
        $article->save();
    }

    public function testGetPictureGalleryReturnsCorrectActiveMedia(): void
    {
        [$article, $productId] = $this->createArticleWithMedia();

        $expectedMediaItems = $this->productMediaViewService->getAllByRole(
            $productId,
            ProductMediaRole::from(ProductMediaRole::DETAIL)
        );
        $expectedActiveMedia = reset($expectedMediaItems);

        $gallery = $article->getPictureGallery();

        $this->assertSame($expectedActiveMedia->getDetailUrl(), $gallery['activeMedia']->getDetailUrl());
    }

    public function testGetPictureGalleryReturnsAllMediaItems(): void
    {
        [$article, $productId] = $this->createArticleWithMedia();

        $expectedMediaItems = $this->productMediaViewService->getAllByRole(
            $productId,
            ProductMediaRole::from(ProductMediaRole::DETAIL)
        );

        $gallery = $article->getPictureGallery();

        $this->assertCount(count($expectedMediaItems), $gallery['mediaItems']);
    }

    public function testGetPictureGalleryHasMultipleImagesTrue(): void
    {
        [$article] = $this->createArticleWithMedia();

        $gallery = $article->getPictureGallery();

        $this->assertTrue($gallery['hasMultipleImages']);
    }

    public function testGetPictureGalleryHasMultipleImagesFalse(): void
    {
        $productId = Id::generate();
        $this->createPersistedArticle($productId);
        $this->addProductMedia($productId, 'single-image.jpg', 1, ProductMediaRole::DETAIL);

        $article = oxNew(Article::class);
        $article->load((string) $productId);

        $gallery = $article->getPictureGallery();

        $this->assertFalse($gallery['hasMultipleImages']);
    }

    public function testGetIconReturnsAltTextWhenSet(): void
    {
        $productId = Id::generate();
        $this->createPersistedArticle($productId);
        $iconMedia = $this->addProductMediaAndReturn($productId, 'icon.jpg', 0, ProductMediaRole::ICON);
        $this->setAltText($iconMedia->getMedia(), 'icon alt');

        $article = oxNew(Article::class);
        $article->load((string) $productId);

        $this->assertSame('icon alt', $article->getIcon()->getAttributes()->getAlt());
    }

    public function testGetIconReturnsNullAltTextWhenNotSet(): void
    {
        [$article] = $this->createArticleWithMedia();

        $this->assertFalse($article->getIcon()->getAttributes()->has(MediaAttribute::ALT));
    }

    public function testGetThumbnailReturnsAltTextWhenSet(): void
    {
        $productId = Id::generate();
        $this->createPersistedArticle($productId);
        $thumbMedia = $this->addProductMediaAndReturn($productId, 'thumb.jpg', 0, ProductMediaRole::THUMBNAIL);
        $this->setAltText($thumbMedia->getMedia(), 'thumb alt');

        $article = oxNew(Article::class);
        $article->load((string) $productId);

        $this->assertSame('thumb alt', $article->getThumbnail()->getAttributes()->getAlt());
    }

    public function testGetMediaReturnsAltTextWhenSet(): void
    {
        $productId = Id::generate();
        $this->createPersistedArticle($productId);
        $detailMedia = $this->addProductMediaAndReturn($productId, 'detail.jpg', 1, ProductMediaRole::DETAIL);
        $this->setAltText($detailMedia->getMedia(), 'detail alt');

        $article = oxNew(Article::class);
        $article->load((string) $productId);

        $this->assertSame('detail alt', $article->getMedia(1)->getAttributes()->getAlt());
    }

    public function testGetPictureGalleryReturnsAltTextForEachMedia(): void
    {
        $productId = Id::generate();
        $this->createPersistedArticle($productId);
        $detail1 = $this->addProductMediaAndReturn($productId, 'detail-1.jpg', 1, ProductMediaRole::DETAIL);
        $detail2 = $this->addProductMediaAndReturn($productId, 'detail-2.jpg', 2, ProductMediaRole::DETAIL);
        $this->setAltText($detail1->getMedia(), 'detail 1 alt');
        $this->setAltText($detail2->getMedia(), 'detail 2 alt');

        $article = oxNew(Article::class);
        $article->load((string) $productId);

        $gallery = $article->getPictureGallery();
        $altTexts = array_map(fn($view) => $view->getAttributes()->getAlt(), $gallery['mediaItems']);

        $this->assertContains('detail 1 alt', $altTexts);
        $this->assertContains('detail 2 alt', $altTexts);
    }

    private function setAltText(Media $media, string $altText): void
    {
        $locale = ContainerFacade::get(ActiveLocaleProviderInterface::class)->getActiveLocale();
        ContainerFacade::get(MediaAttributeServiceInterface::class)
            ->save(MediaAttribute::ALT, $altText, $media, $locale->getCode());
    }

    private function addProductMediaAndReturn(
        Id $productId,
        string $fileName,
        int $position,
        string $role
    ): ProductMedia {
        $media = new Media(
            Id::generate(),
            new MediaPath(Path::join('out', 'pictures', 'media', $fileName)),
            new MediaType('image/jpeg')
        );

        $roleSet = new ProductMediaRoleSet(ProductMediaRole::from($role));
        $productMedia = new ProductMedia(
            Id::generate(),
            $productId,
            $media,
            $roleSet
        );
        $productMedia->setPosition($position);
        $this->productMediaService->add($productMedia);
        return $productMedia;
    }

    private function addProductMedia(Id $productId, string $fileName, int $position, string $role): void
    {
        $this->addProductMediaAndReturn($productId, $fileName, $position, $role);
    }

    public function testGetTitleReturnsTitleWithVariant(): void
    {
        $article = oxNew(Article::class);
        $article->oxarticles__oxtitle = new Field('Stan Smith', Field::T_RAW);
        $article->oxarticles__oxvarselect = new Field('White 42', Field::T_RAW);

        $this->assertSame('Stan Smith White 42', $article->getTitle());
    }

    public function testGetTitleTrimsTrailingSpaceWhenVariantEmpty(): void
    {
        $article = oxNew(Article::class);
        $article->oxarticles__oxtitle = new Field('Stan Smith', Field::T_RAW);
        $article->oxarticles__oxvarselect = new Field('', Field::T_RAW);

        $this->assertSame('Stan Smith', $article->getTitle());
    }
}
