<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Codeception\Acceptance;

use Codeception\Attribute\Group;
use Codeception\Util\Fixtures;
use OxidEsales\EshopCommunity\Tests\Codeception\Support\AcceptanceTester;

#[Group('product')]
final class ProductMediaAltAttributesCest
{
    #[Group('main', 'product')]
    public function productMediaAltAttributesAreDisplayedInStorefront(AcceptanceTester $I): void
    {
        $I->wantToTest('if product media alt attributes are displayed in storefront views');

        $fixture = $this->getProductMediaAltAttributesFixture();

        $detailsPage = $I->openShop()
            ->searchFor($fixture['searchTerm'])
            ->seeProductImageAltText($fixture['primaryAltText'])
            ->openProduct();

        $detailsPage
            ->seeGalleryThumbnailAltText($fixture['primaryAltText'])
            ->seeGalleryThumbnailAltText($fixture['secondaryAltText'], 2)
            ->seeActiveGalleryImageAltText($fixture['primaryAltText']);
    }

    private function getProductMediaAltAttributesFixture(): array
    {
        return Fixtures::get('productMediaAltAttributes10181');
    }
}
