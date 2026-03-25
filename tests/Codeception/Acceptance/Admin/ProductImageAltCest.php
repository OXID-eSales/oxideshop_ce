<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Codeception\Acceptance\Admin;

use Codeception\Attribute\Group;
use OxidEsales\Codeception\Admin\Product\ImageAltProductPage;
use OxidEsales\EshopCommunity\Tests\Codeception\Support\AcceptanceTester;

#[Group('admin', 'product', 'imagealt')]
final class ProductImageAltCest
{
    private string $productNumber = '1000';
    private string $productId = '1000';
    private string $mediaId = 'test-media-1000-1';
    private string $productMediaId = 'test-pm-1000-1';
    private string $localeId = 'de_DE';

    public function imageAltTabShowsImagesForProduct(AcceptanceTester $I): void
    {
        $I->wantTo('verify that the image alt tab shows product images');

        $this->prepareProductMedia($I);
        $imageAltTab = $this->openImageAltTab($I);

        $I->expect('the product image is visible');
        $imageAltTab->seeImage($this->productMediaId);
    }

    public function expandImageAndSeeLocaleInputs(AcceptanceTester $I): void
    {
        $I->wantTo('expand an image and see locale alt text inputs');

        $this->prepareProductMedia($I);
        $imageAltTab = $this->openImageAltTab($I);

        $I->amGoingTo('expand the product image');
        $imageAltTab->expandImage($this->productMediaId);

        $I->expect('the expanded image with locale inputs is visible');
        $imageAltTab->seeExpandedImage($this->productMediaId);
    }

    public function saveAltTextAndItPersistsWithFilledBadge(AcceptanceTester $I): void
    {
        $I->wantTo('save alt text and verify it persists with correct language badge state');

        $this->prepareProductMedia($I);
        $imageAltTab = $this->openImageAltTab($I);

        $I->amGoingTo('clear the alt text and save');
        $imageAltTab->expandImage($this->productMediaId);
        $imageAltTab->fillAltText($this->productMediaId, $this->localeId, '');
        $imageAltTab->saveAltTexts($this->productMediaId);

        $I->expect('the language badge to be empty');
        $imageAltTab->seeLanguageBadgeEmpty($this->productMediaId, $this->localeId);

        $I->amGoingTo('fill in the alt text and save');
        $imageAltTab->expandImage($this->productMediaId);
        $imageAltTab->fillAltText($this->productMediaId, $this->localeId, 'Test Alt Text DE');
        $imageAltTab->seeLanguageBadgeChanged($this->productMediaId, $this->localeId);
        $imageAltTab->saveAltTexts($this->productMediaId);

        $I->expect('the language badge to be filled and the text to persist');
        $imageAltTab->seeLanguageBadgeFilled($this->productMediaId, $this->localeId);
        $imageAltTab->expandImage($this->productMediaId);
        $imageAltTab->seeAltText($this->productMediaId, $this->localeId, 'Test Alt Text DE');
    }

    private function prepareProductMedia(AcceptanceTester $I): void
    {
        $I->haveInDatabase('oxmedia', [
            'id' => $this->mediaId,
            'path' => 'out/pictures/media/test-product-1000.jpg',
            'type' => 'image/jpeg',
        ]);
        $I->haveInDatabase('oxproduct_media', [
            'id' => $this->productMediaId,
            'product_id' => $this->productId,
            'media_id' => $this->mediaId,
            'position' => 1,
            'active' => 1,
        ]);
        $I->haveInDatabase('oxproduct_media_roles', [
            'product_media_id' => $this->productMediaId,
            'role' => 'detail',
        ]);
    }

    private function openImageAltTab(AcceptanceTester $I): ImageAltProductPage
    {
        $adminPanel = $I->loginAdmin();
        $products = $adminPanel->openProducts();
        $mainProductPage = $products->findByProductNumber($this->productNumber);

        return $mainProductPage->openImageAltTab();
    }
}
