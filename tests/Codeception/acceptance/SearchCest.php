<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Codeception;

use OxidEsales\Codeception\Module\Translation\Translator;

class SearchCest
{
    /**
     * @group myAccount
     * @group wishList
     *
     * @param AcceptanceTester $I
     */
    public function searchAndNavigateInProductList(AcceptanceTester $I)
    {
        $I->wantToTest('if sorting, paging and navigation is working correctly in search list');

        $I->updateConfigInDatabase('aNrofCatArticles', serialize(["1", "2", "10", "20", "50", "100"]), "arr");
        $I->updateConfigInDatabase('aNrofCatArticlesInGrid', serialize(["12", "16", "24", "32"]), "arr");

        $productData = [
            'id' => '1000',
            'title' => 'Test product 0 [EN] šÄßüл',
            'description' => 'Test product 0 short desc [EN] šÄßüл',
            'price' => '50,00 € *'
        ];
        $productData2 = [
            'id' => '1001',
            'title' => 'Test product 1 [EN] šÄßüл',
            'description' => 'Test product 1 short desc [EN] šÄßüл',
            'price' => '100,00 € *'
        ];

        $productData3 = [
            'id' => '10014',
            'title' => '14 EN product šÄßüл',
            'description' => '13 EN description šÄßüл',
            'price' => 'from 15,00 €'
        ];

        $searchListPage = $I->openShop()
            ->searchFor('notExisting')
            ->seeSearchCount(0);
        $I->see(Translator::translate('NO_ITEMS_FOUND'));

        $searchListPage = $searchListPage->searchFor('100')
            ->seeSearchCount(4)
            ->selectSorting('oxtitle', 'asc')
            ->selectProductsPerPage(2);

        $I->see(Translator::translate('PRODUCTS_PER_PAGE').' 2');

        $searchListPage = $searchListPage->seeProductData($productData3, 1)
            ->seeProductData($productData, 2)
            ->openNextListPage()
            ->seeProductData($productData2, 1)
            ->openPreviousListPage()
            ->seeProductData($productData3, 1)
            ->selectSorting('oxprice', 'desc')
            ->seeProductData($productData2, 1)
            ->openListPageNumber(2)
            ->seeProductData($productData, 1)
            ->seeProductData($productData3, 2)
            ->selectListDisplayType(Translator::translate('grid'));
        $I->see(Translator::translate('PRODUCTS_PER_PAGE').' 12');
        $I->dontSeeElement($searchListPage->nextListPage);
    }
}
