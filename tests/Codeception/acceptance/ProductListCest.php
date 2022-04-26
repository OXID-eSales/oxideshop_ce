<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Codeception;

final class ProductListCest
{
    /**
     * Product list. check category filter reset button functionality
     * @group product_list
     * @group frontend
     */
    public function testCategoryFilterReset(AcceptanceTester $I): void
    {
        $I->wantToTest('category filter reset button functionality');

        $homePage = $I->openShop();
        $I->waitForPageLoad();
        $homePage->openCategoryPage('Kiteboarding');
        $I->waitForPageLoad();
        $homePage->openCategoryPage('Kites');
        $I->waitForPageLoad();
        $I->seeElement("//form[@id='filterList']");

        $I->click("//*[@id='filterList']/div[@class='btn-group'][1]/button");
        $I->waitForText("Freeride");
        $I->click("Freeride");
        $I->waitForPageLoad();
        $I->seeElement("//*[@id='resetFilter']/button");
        $I->click("//*[@id='resetFilter']/button");
        $I->waitForPageLoad();

        $I->click("//*[@id='filterList']/div[@class='btn-group'][2]/button");
        $I->waitForText("kite");
        $I->click("kite");
        $I->waitForPageLoad();
        $I->seeElement("//*[@id='resetFilter']/button");

        $I->click("//*[@id='resetFilter']/button");
        $I->waitForPageLoad();
        $I->dontSeeElement("//*[@id='resetFilter']/button");
    }

    /**
     * @group productList
     * @group productVariants
     *
     * @param AcceptanceTester $I
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

        $searchListPage->seeProductData($productData, 1);

        $detailsPage = $searchListPage->selectVariant(1, 'M');
        $detailsPage->seeProductData($productData);
    }
}
