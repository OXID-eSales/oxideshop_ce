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
use OxidEsales\EshopCommunity\Internal\Domain\Media\DataObject\MediaPath;
use OxidEsales\EshopCommunity\Internal\Domain\Media\DataObject\MediaType;
use OxidEsales\EshopCommunity\Internal\Domain\Product\Media\DataObject\ProductMedia;
use OxidEsales\EshopCommunity\Internal\Domain\Product\Media\DataObject\ProductMediaRole;
use OxidEsales\EshopCommunity\Internal\Domain\Product\Media\DataObject\ProductMediaRoleSet;
use OxidEsales\EshopCommunity\Internal\Domain\Product\Media\Service\ProductMediaServiceInterface;
use OxidEsales\EshopCommunity\Internal\Domain\Product\Media\Service\ProductMediaViewServiceInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Database\Id;
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
        Registry::getConfig()->setConfigParam('blUseStock', false);

        $this->productMediaService = ContainerFacade::get(ProductMediaServiceInterface::class);
        $this->productMediaViewService = ContainerFacade::get(ProductMediaViewServiceInterface::class);
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
        )->getUrl();

        $this->assertSame($expectedUrl, $article->getIcon()->getUrl());
    }

    public function testGetThumbnailReturnsMediaViewFromService(): void
    {
        [$article, $productId] = $this->createArticleWithMedia();

        $expectedUrl = $this->productMediaViewService->getByRole(
            $productId,
            ProductMediaRole::from(ProductMediaRole::THUMBNAIL)
        )->getUrl();

        $this->assertSame($expectedUrl, $article->getThumbnail()->getUrl());
    }

    public function testGetMediaReturnsDetailImageForRequestedPosition(): void
    {
        [$article, $productId] = $this->createArticleWithMedia();

        $expectedUrl = $this->productMediaViewService->getByPosition($productId, 1)->getUrl();

        $this->assertSame($expectedUrl, $article->getMedia(1)->getUrl());
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

        $this->assertSame($expectedActiveMedia->getUrl(), $gallery['activeMedia']->getUrl());
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

    private function addProductMedia(Id $productId, string $fileName, int $position, string $role): void
    {
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
    }
}
