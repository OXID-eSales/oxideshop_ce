<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Codeception;

use OxidEsales\Codeception\Module\Translation\Translator;

final class ManufacturerCest
{
    /**
     * @group manufacturer
     */
    public function checkManufacturerList(AcceptanceTester $I): void
    {
        $I->wantToTest('manufacturer list');
        $I->updateConfigInDatabase('bl_showManufacturer', true);

        $homePage = $I->openShop();
        $homePage->openManufacturerListPage()
            ->seeManufacturerData(
                [
                    'title' => 'Manufacturer [EN] šÄßüл',
                    'count' => '3'
                ],
                1
            )
            ->openManufacturerPage(1)
            ->seePageInformation([
                'title' => 'Manufacturer [EN] šÄßüл',
                'description' => 'Manufacturer description [EN] šÄßüл'
            ]);
    }

    /**
     * @group manufacturer
     * @group product_list
     */
    public function checkAndNavigateThroughManufacturerProductList(AcceptanceTester $I): void
    {
        $I->wantToTest('manufacturer functionality and product list navigation');
        $I->updateConfigInDatabase('bl_showManufacturer', true);
        $I->updateConfigInDatabase('aNrofCatArticles', serialize([10, 50, 100, 2, 1]), 'arr');
        $I->updateConfigInDatabase('aNrofCatArticlesInGrid', serialize([10, 50, 100, 2, 1]), 'arr');

        $productData = [
            'id' => '1000',
            'title' => 'Test product 0 [EN] šÄßüл',
            'description' => 'Test product 0 short desc [EN] šÄßüл',
            'price' => '50,00 €'
        ];

        $productData2 = [
            'id' => '1001',
            'title' => 'Test product 1 [EN] šÄßüл',
            'description' => 'Test product 1 short desc [EN] šÄßüл',
            'price' => '100,00 €'
        ];

        $homePage = $I->openShop();
        $productList = $homePage->openManufacturerFromStarPage('Manufacturer [EN] šÄßüл')
        ->seePageInformation([
            'title' => 'Manufacturer [EN] šÄßüл',
            'description' => 'Manufacturer description [EN] šÄßüл'
        ]);

        $productList->selectSorting('oxtitle', 'asc')
            ->seeProductData($productData, 1)
            ->seeProductData($productData2, 2)
            ->selectSorting('oxprice', 'desc')
            ->selectProductsPerPage('2')
            ->seeProductData($productData2, 1)
            ->openNextListPage()
            ->seeProductData($productData, 1)
            ->openPreviousListPage()
            ->seeProductData($productData2, 1);

        //Dont show manufacturers at all
        $I->updateConfigInDatabase('bl_perfLoadManufacturerTree', false);
        $I->updateConfigInDatabase('bl_showManufacturer', false);
        $homePage = $I->openShop();
        $I->dontSee(Translator::translate('OUR_BRANDS'));
    }
}
