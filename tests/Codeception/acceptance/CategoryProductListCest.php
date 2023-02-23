<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Codeception;

use OxidEsales\Codeception\Module\Translation\Translator;

final class CategoryProductListCest
{
    /**
     * @group category_product_list
     */
    public function filterAndNavigateThroughCategoryList(AcceptanceTester $I): void
    {
        $I->wantToTest('category product list filter functionality');
        $this->setNumberOfProductsInCategoryList($I);

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

        $homePage = $I->openShop();
        $productList = $homePage->openCategoryPage('Test category 0 [EN] šÄßüл');
        $productList->selectFilter('Test attribute 1 [EN] šÄßüл', 'attr value 1 [EN] šÄßüл')
            ->dontSeeProductData($productData2, 1)->openFilter('Test attribute 2 [EN] šÄßüл');
        $I->dontSee('attr value 12 [EN] šÄßüл', $productList->listFilter);
        $productList = $productList
            ->resetFilter()
            ->selectFilter('Test attribute 2 [EN] šÄßüл', 'attr value 12 [EN] šÄßüл')
            ->dontSeeProductData($productData, 1)
            ->resetFilter()
            ->selectProductsPerPage('1')
            ->selectFilter('Test attribute 3 [EN] šÄßüл', 'attr value 3 [EN] šÄßüл')
            ->seeProductData($productData, 1)
            ->openNextListPage()
            ->seeProductData($productData2, 1)
            ->seeSelectedFilter('Test attribute 3 [EN] šÄßüл', 'attr value 3 [EN] šÄßüл')
            ->openPreviousListPage()
            ->seeSelectedFilter('Test attribute 3 [EN] šÄßüл', 'attr value 3 [EN] šÄßüл')
            ->resetFilter();

        $I->dontSeeElement($productList->resetListFilter);
    }

    /**
     * @group category_product_list
     */
    public function sortAndNavigateThroughCategoryList(AcceptanceTester $I): void
    {
        $I->wantToTest('category product list sorting');
        $this->setNumberOfProductsInCategoryList($I);

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

        $homePage = $I->openShop();
        $productList = $homePage->openCategoryPage('Test category 0 [EN] šÄßüл');

        $productList->selectSorting('oxtitle', 'asc')
            ->seeProductData($productData, 1)
            ->seeProductData($productData2, 2)
            ->selectSorting('oxprice', 'desc')
            ->selectProductsPerPage('1')
            ->seeProductData($productData2, 1)
            ->openNextListPage()
            ->seeProductData($productData, 1);

        $I->amGoingTo('disable sorting at all');
        $I->updateConfigInDatabase('blShowSorting', false);
        $I->openShop()->openCategoryPage('Test category 0 [EN] šÄßüл');
        $I->dontSee(Translator::translate('SORT_BY'));
    }

    /**
     * @group category_product_list
     */
    public function navigateThroughPriceCategoryList(AcceptanceTester $I): void
    {
        $I->wantToTest('price category functionality');

        $I->updateInDatabase('oxcategories', ['OXACTIVE' => 1, 'OXACTIVE_1' => 1], ['OXID' => 'testpricecat']);
        $this->setNumberOfProductsInCategoryList($I);

        $productData = [
            'id' => '1000',
            'title' => 'Test product 0 [EN] šÄßüл',
            'description' => 'Test product 0 short desc [EN] šÄßüл',
            'price' => '50,00 € *'
        ];

        $productData2 = [
            'id' => '1002',
            'title' => 'Test product 2 [EN] šÄßüл',
            'description' => 'Test product 2 short desc [EN] šÄßüл',
            'price' => 'from 55,00 €'
        ];

        $homePage = $I->openShop();
        $productList = $homePage->openCategoryPage('price [EN] šÄßüл');

        $productList->selectSorting('oxtitle', 'asc')
            ->seeProductData($productData, 1)
            ->seeProductData($productData2, 2)
            ->selectSorting('oxprice', 'desc')
            ->selectProductsPerPage('1')
            ->seeProductData($productData2, 1)
            ->openNextListPage()
            ->seeProductData($productData, 1);
    }

    /**
     * @group category_product_list
     * @group product_variants
     */
    public function selectMultidimensionalVariantsInLists(AcceptanceTester $I): void
    {
        $I->wantToTest('multidimensional variants functionality in lists');

        $I->updateConfigInDatabase('blUseMultidimensionVariants', true, 'bool');
        $I->updateConfigInDatabase('bl_perfLoadSelectListsInAList', true, 'bool');
        $I->updateConfigInDatabase('bl_perfLoadSelectLists', true, 'bool');

        $productData = [
            'id' => '10014',
            'title' => '14 EN product šÄßüл',
            'description' => '13 EN description šÄßüл',
            'price' => 'from 15,00 €'
        ];

        $searchListPage = $I->openShop()
            ->searchFor($productData['id']);

        $searchListPage->seeProductData($productData, 1)->openProductDetailsPage(1)
            ->seeProductData($productData);
    }

    private function setNumberOfProductsInCategoryList(AcceptanceTester $I): void
    {
        $I->updateConfigInDatabase('aNrofCatArticles', serialize([20, 1, 2, 10, 100]), 'arr');
        $I->updateConfigInDatabase('aNrofCatArticlesInGrid', serialize([20, 1, 2, 10, 100]), "arr");
    }
}
