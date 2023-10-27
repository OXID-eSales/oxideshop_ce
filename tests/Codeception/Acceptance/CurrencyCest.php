<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Codeception\Acceptance;

use OxidEsales\EshopCommunity\Tests\Codeception\Support\AcceptanceTester;

final class CurrencyCest
{
    /**
     * @group currency_test
     */
    public function testCurrencySwitch(AcceptanceTester $I): void
    {
        $I->wantToTest('currency switching');

        $homePage = $I->openShop()->openHomePage();
        $homePage->switchCurrency('EUR');
        $productList = $homePage->openCategoryPage('Test category 0 [EN] šÄßüл');

        $productData = [
            'id' => '1000',
            'title' => 'Test product 0 [EN] šÄßüл',
            'description' => 'Test product 0 short desc [EN] šÄßüл',
            'price' => '50,00 €'
        ];

        $productList->seeProductData($productData);

        $productList->switchCurrency('GBP');
        $productData['price'] = '42.83 £';
        $productList->seeProductData($productData);
    }
}
