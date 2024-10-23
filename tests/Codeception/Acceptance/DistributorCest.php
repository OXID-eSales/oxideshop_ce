<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Codeception\Acceptance;

use Codeception\Attribute\Group;
use OxidEsales\Codeception\Page\Lists\DistributorList;
use OxidEsales\EshopCommunity\Tests\Codeception\Support\AcceptanceTester;

#[group('manufacturer')]
final class DistributorCest
{
    public function checkDistributorList(AcceptanceTester $I): void
    {
        $I->wantToTest('distributor functionality and product list navigation');
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

        $distributorListPage = new DistributorList($I);
        $I->amOnPage($distributorListPage->route([]));
        $distributorListPage->seeDistributorData(
            [
                'title' => 'Distributor [EN] šÄßüл',
                'count' => '3'
            ],
            1
        )
            ->openDistributorPage(1)
            ->seePageInformation([
                'title' => 'Distributor [EN] šÄßüл',
                'description' => 'Distributor description [EN] šÄßüл'
            ])->selectSorting('oxtitle', 'asc')
            ->seeProductData($productData, 1)
            ->seeProductData($productData2, 2)
            ->selectSorting('oxprice', 'desc')
            ->selectProductsPerPage('2')
            ->seeProductData($productData2, 1)
            ->openNextListPage()
            ->seeProductData($productData, 1)
            ->openPreviousListPage()
            ->seeProductData($productData2, 1);
    }
}
