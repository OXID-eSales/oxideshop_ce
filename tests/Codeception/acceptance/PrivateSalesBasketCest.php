<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Codeception\acceptance;

use OxidEsales\Codeception\Module\Translation\Translator;
use OxidEsales\EshopCommunity\Tests\Codeception\AcceptanceTester;

final class PrivateSalesBasketCest
{
    public function testIfblBasketExcludeEnabledBlocksRootCatChange(AcceptanceTester $I): void
    {
        $I->wantToTest('if blBasketExcludeEnabled blocks rootCatChange and continue shopping clears basket.');

        $I->updateConfigInDatabase('blBasketExcludeEnabled', 'true', 'bool');

        $I->updateInDatabase('oxcategories', ['OXACTIVE' => 1], ['OXID' => 'testcategory2']);
        $I->haveInDatabase(
            'oxobject2category',
            [
                'OXID' => 'testobject2category1002',
                'OXOBJECTID' => '1002',
                'OXCATNID' => 'testcategory2',
                'OXPOS' => 0,
            ]
        );

        $I->clearShopCache();

        $homePage = $I->openShop();

        $basketPage = $homePage->openCategoryPage('Test category 0 [EN] šÄßüл')
            ->openProductDetailsPage(1)
            ->addProductToBasket(1)
            ->openBasket();

        $productData = [
            'id' => '1000',
            'title' => 'Test product 0 [EN] šÄßüл',
            'amount' => 1,
            'totalPrice' => '50,00 €'
        ];

        $basketPage->seeBasketContains([$productData], '50,00 €');

        $homePage->openCategoryPage('Test category 0 [EN] šÄßüл');
        $I->dontSee(Translator::translate('ROOT_CATEGORY_CHANGED'));

        $homePage->openCategoryPage('Test category 2 [EN] šÄßüл')
            ->confirmMainCategoryChanged()
            ->checkBasketEmpty();
    }

    public function checkIfblBasketExcludeEnabledAlsoClearsByEmptyBasket(AcceptanceTester $I): void
    {
        $I->wantToTest('if blBasketExcludeEnabled rootCatChange is no longer blocked by an empty basket.');

        $I->updateConfigInDatabase('blBasketExcludeEnabled', 'true', 'bool');

        $I->updateInDatabase('oxcategories', ['OXACTIVE' => 1], ['OXID' => 'testcategory2']);
        $I->haveInDatabase(
            'oxobject2category',
            [
                'OXID' => 'testobject2category1002',
                'OXOBJECTID' => '1002',
                'OXCATNID' => 'testcategory2',
                'OXPOS' => 0,
            ]
        );

        $I->clearShopCache();

        $homePage = $I->openShop();

        $homePage->openCategoryPage('Test category 0 [EN] šÄßüл')
            ->openProductDetailsPage(1)
            ->addProductToBasket(1)
            ->openBasket();
        $homePage->openCategoryPage('Test category 2 [EN] šÄßüл')
            ->openBasketIfMainCategoryChanged()
            ->updateProductAmount(0);

        $homePage->openCategoryPage('Test category 2 [EN] šÄßüл');
        $I->dontSee(Translator::translate('ROOT_CATEGORY_CHANGED'));
    }

    /** @group private_shopping_basket_expiration */
    public function testPrivateShoppingBasketExpiration(AcceptanceTester $I): void
    {
        $I->wantToTest('private basket reservation expiration');

        $I->updateInDatabase('oxarticles', ['oxstock' => '2', 'oxstockflag' => '2'], ['oxid' => '1000']);
        $I->updateConfigInDatabase('blPsBasketReservationEnabled', 'true', 'bool');
        $I->updateConfigInDatabase('iPsBasketReservationTimeout', '10', 'str');

        $I->clearShopCache();
        $homePage = $I->openShop();

        $homePage->openCategoryPage('Test category 0 [EN] šÄßüл')
            ->openProductDetailsPage(1)
            ->addProductToBasket(2);

        $homePage->seeCountdownWithinBasket();

        $I->openShop()->searchFor('1000');
        $I->see(Translator::translate('NO_ITEMS_FOUND'));
        //we need to wait for the timeout
        $I->wait(12);

        $I->dontSee("expired products are still visible in basket popup...", "modalbasketFlyout");

        $homePage = $I->openShop();
        $homePage->checkBasketEmpty();
        $homePage->searchFor('1000');
        $I->dontSee(Translator::translate('NO_ITEMS_FOUND'));
    }
}
